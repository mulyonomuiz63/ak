<?php

namespace App\Controllers;

use Midtrans\Notification;
use App\Libraries\MidtransLib;
use CodeIgniter\API\ResponseTrait;

class Payment extends BaseController
{
    use ResponseTrait;
    
    public function tokenProses()
    {
        $midtrans = new MidtransLib();
        $db = \Config\Database::connect();
        $id_transaksi = $this->request->getPost('idpl');
        $transaksi = $db->table('transaksi_pl')
            ->select('transaksi_pl.*, perusahaan.namaperusahaan')
            ->join('perusahaan', 'transaksi_pl.idperusahaan=perusahaan.idperusahaan')
            ->where('transaksi_pl.idpl', $id_transaksi)->get()->getRow();
    
        if (!$transaksi) {
            return $this->fail("Transaksi tidak ditemukan");
        }
    
        // 1. CEK EXPIRED
        $is_expired = false;
        if ($transaksi->snap_token) {
            $last_update = strtotime($transaksi->updated_at);
            if (time() - $last_update > 86400) { // Jika lebih dari 24 jam
                $is_expired = true;
            }
        }
    
        // 2. LOGIKA JIKA EXPIRED: Reset data lama agar dianggap transaksi baru
        if ($is_expired) {
            $db->table('transaksi_pl')->where('idpl', $id_transaksi)->delete();
            
            // Beritahu frontend bahwa data sudah dihapus karena expired
            return $this->response->setJSON([
                'status' => 'deleted', 
                'message' => 'Transaksi telah kadaluarsa dan dihapus otomatis.'
            ]);
        }
    
        // 3. JIKA MASIH VALID: Kembalikan token yang ada
        if ($transaksi->snap_token && !$is_expired) {
            return $this->respond(['token' => $transaksi->snap_token]);
        }
    
        // 4. JIKA BARU / SUDAH DIRESET: Buat Token Snap Baru
        // Gunakan time() agar Order ID selalu unik (Midtrans menolak Order ID yang sama jika sudah expired)
        $new_order_id = 'INV-' . $id_transaksi . '-' . time();
        
        $params = [
            'transaction_details' => [
                'order_id'     => $new_order_id,
                'gross_amount' => (int)$transaksi->nominal, 
            ],
            'customer_details' => [
                'first_name' => $transaksi->namaperusahaan,
            ]
        ];
    
        try {
            $snapToken = $midtrans->getSnapToken($params);
    
            // Update database dengan token baru
            $db->table('transaksi_pl')->where('idpl', $id_transaksi)->update([
                'status'          => 'P',
                'status_transfer' => 'VA',
                'order_id'        => $new_order_id,
                'snap_token'      => $snapToken,
                'updated_at'      => date('Y-m-d H:i:s'), // Update waktu agar cek expired di klik berikutnya akurat
            ]);
            
            $nama_langganan = $this->request->getPost('nama_langganan');
            $nominal = $this->request->getPost('nominalUpload');
            $kode_unik = $this->request->getPost('kode_unik');
            
             $data = [
                'idperusahaan'      => session()->get('idperusahaan'),
                'nama_langganan'    => $nama_langganan,
                'nominal'           => $nominal,
                'kode_unik'         => $kode_unik,
                'status'            => 'P',
            ];
            $builder = $db->table('payment');
            $builder->insert($data);
    
            return $this->respond(['token' => $snapToken]);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function getToken($id_transaksi)
    {
        $midtrans = new MidtransLib();
        $db = \Config\Database::connect();
        
        $transaksi = $db->table('transaksi_pl')
            ->select('transaksi_pl.*, perusahaan.namaperusahaan')
            ->join('perusahaan', 'transaksi_pl.idperusahaan=perusahaan.idperusahaan')
            ->where('transaksi_pl.idpl', $id_transaksi)->get()->getRow();
    
        if (!$transaksi) {
            return $this->fail("Transaksi tidak ditemukan");
        }
    
        // 1. CEK EXPIRED
        $is_expired = false;
        if ($transaksi->snap_token) {
            $last_update = strtotime($transaksi->updated_at);
            if (time() - $last_update > 86400) { // Jika lebih dari 24 jam
                $is_expired = true;
            }
        }
    
        // 2. LOGIKA JIKA EXPIRED: Reset data lama agar dianggap transaksi baru
        if ($is_expired) {
            $db->table('transaksi_pl')->where('idpl', $id_transaksi)->delete();
            
            // Beritahu frontend bahwa data sudah dihapus karena expired
            return $this->response->setJSON([
                'status' => 'deleted', 
                'message' => 'Transaksi telah kadaluarsa dan dihapus otomatis.'
            ]);
        }
    
        // 3. JIKA MASIH VALID: Kembalikan token yang ada
        if ($transaksi->snap_token && !$is_expired) {
            return $this->respond(['token' => $transaksi->snap_token]);
        }
    
        // 4. JIKA BARU / SUDAH DIRESET: Buat Token Snap Baru
        // Gunakan time() agar Order ID selalu unik (Midtrans menolak Order ID yang sama jika sudah expired)
        $new_order_id = 'INV-' . $id_transaksi . '-' . time();
        
        $params = [
            'transaction_details' => [
                'order_id'     => $new_order_id,
                'gross_amount' => (int)$transaksi->nominal, 
            ],
            'customer_details' => [
                'first_name' => $transaksi->namaperusahaan,
            ]
        ];
    
        try {
            $snapToken = $midtrans->getSnapToken($params);
    
            // Update database dengan token baru
            $db->table('transaksi_pl')->where('idpl', $id_transaksi)->update([
                'status'          => 'P',
                'status_transfer' => 'VA',
                'order_id'        => $new_order_id,
                'snap_token'      => $snapToken,
                'updated_at'      => date('Y-m-d H:i:s'), // Update waktu agar cek expired di klik berikutnya akurat
            ]);
    
            return $this->respond(['token' => $snapToken]);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    // Handler untuk Webhook Midtrans (Sesuai dokumentasi terbaru)
    public function prosesVerifikasi()
    {
        $midtrans = new MidtransLib();

        $order_id    = $this->request->getGet('order_id');
        $status_code = $this->request->getGet('status_code');
        $status      = $this->request->getGet('transaction_status');

        // Ambil data awal dari transaksi_pl berdasarkan order_id untuk mendapatkan konteks
        $pembayaran = $this->db->table('transaksi_pl')
            ->where('order_id', $order_id)
            ->get()->getRow();
    
        if ($status == 'settlement' || $status == 'capture') {
            
            // Pastikan data ditemukan sebelum memproses
            if ($pembayaran) {
                $idperusahaan = $pembayaran->idperusahaan;
              
                // --- MULAI PROSES ASLI ANDA (TIDAK DIUBAH) ---
                $this->db->transBegin();
                
                $builder = $this->db->table('transaksi_pl');
                $rsperusahaan = $builder->getWhere(array('idperusahaan' => $idperusahaan))->getRow();
                
                if ($rsperusahaan) {
                    $data = [
                        'idperusahaan'  => $rsperusahaan->idperusahaan,
                        'kode_referal'  => $rsperusahaan->kode_referal,
                        'idlangganan'   => $rsperusahaan->idlangganan,
                        'va'            => $rsperusahaan->va,
                        'nominal'       => $rsperusahaan->nominal,
                        'kode_unik'     => $rsperusahaan->kode_unik,
                        'status'        => 'B',
                        'tgl_mulai'     => $rsperusahaan->tgl_mulai,
                        'tgl_akhir'     => $rsperusahaan->tgl_akhir,
                    ];
    
                    $builder_pl = $this->db->table('perusahaan_langganan');
                    $builder_pl->where('idperusahaan', $idperusahaan);
                    $builder_pl->update($data);
    
                    $dataP = [
                        'tglregistrasi'  => date('Y-m-d H:i:s', strtotime($rsperusahaan->tgl_mulai)),
                        'tglberakhir'    => $rsperusahaan->tgl_akhir,
                    ];
                    $builderP = $this->db->table('perusahaan');
                    $builderP->where('idperusahaan', $idperusahaan);
                    $builderP->update($dataP);
    
                    $builder11 = $this->db->table('payment');
                    $rsperusahaan1 = $builder11->getWhere(array('idperusahaan' => $idperusahaan, 'status' => 'P'))->getRow();
    
                    if ($rsperusahaan1) {
                        $data1 = [
                            'bukti_pembayaran' => '',
                            'status' => 'S',
                        ];
                        $builder1 = $this->db->table('payment');
                        $builder1->where('idpayment', $rsperusahaan1->idpayment);
                        $builder1->update($data1);
    
                        // Hapus file thumbnails jika ada
                        if ($rsperusahaan1->bukti_pembayaran && file_exists('./uploads/buktitransaksi/thumbnails/' . $rsperusahaan1->bukti_pembayaran)) {
                            unlink('./uploads/buktitransaksi/thumbnails/' . $rsperusahaan1->bukti_pembayaran);
                        }
                    }
    
                    $builder2 = $this->db->table('transaksi_pl');
                    $builder2->where('idperusahaan', $idperusahaan);
                    $builder2->delete();
                }
    
                if ($this->db->transStatus() === FALSE) {
                    $this->db->transRollback();
                } else {
                    $this->db->transCommit();
                }
                // --- AKHIR PROSES ASLI ANDA ---
            }
             echo 'd';
    
        } elseif ($status == 'pending') {
            // Logika status pending jika diperlukan
        } elseif (in_array($status, ['deny', 'expire', 'cancel'])) {
            // Hapus data transaksi_pl jika kadaluarsa
            $this->db->table('transaksi_pl')->where('order_id', $order_id)->delete();
        }
    
        return redirect()->to('Histori');
    }
    
    public function updateToManual($idpl)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('transaksi_pl');
    
        $data = [
            'status_transfer' => 'M' // 'M' proses manual
        ];
    
        $builder->where('idpl', $idpl);
        
        if ($builder->update($data)) {
            // AMBIL DATA TERBARU setelah update
            $updatedData = $db->table('transaksi_pl')->join('langganan','langganan.idlangganan=transaksi_pl.idlangganan')
                              ->select('transaksi_pl.*, langganan.nama_langganan')
                              ->where('transaksi_pl.idpl', $idpl)
                              ->get()
                              ->getRow();
    
            return $this->response->setJSON([
                'status' => 'success', 
                'message' => 'Berhasil beralih ke manual',
                'data'    => $updatedData // Data dikirim di sini
            ]);
        } else {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error', 
                'message' => 'Gagal update data'
            ]);
        }
    }
}