<?php

namespace App\Models;

use CodeIgniter\Model;

class Histori_model extends Model
{
    var $tabel              = 'perusahaan_langganan';
    var $column_order = array(null, 'status',); //set nama field yang bisa diurutkan
    var $column_search = array('status', 'nama_langganan', 'namaperusahaan', 'tgl_mulai', 'tgl_akhir'); //set nama field yang akan di cari
    var $order = array('idpl' => 'desc'); // default order 

    //detail
    var $column_orderDetail = array(null, 'status',); //set nama field yang bisa diurutkan
    var $column_searchDetail = array('status', 'nama_langganan', 'nominal', 'namaperusahaan'); //set nama field yang akan di cari
    var $orderDetail = array('idpayment' => 'desc'); // default order 


    public function getLangganan()
    {
        $this->builder = $this->db->table('langganan a');
        $this->builder->select('a.*, b.diskon, c.komisi');
        $this->builder->join('diskon b', 'a.iddiskon = b.iddiskon');
        $this->builder->join('komisi c', 'a.idkomisi = c.idkomisi');
        $this->builder->where('a.deleted_at', null);
        $this->builder->where('a.idlangganan !=', 1);
        return $this->builder->get()->getResult();
    }

    function get_datatables($status)
    {
        $this->_get_datatables_query($status);
        if (!empty($_POST['length']) && $_POST['length'] != -1)
            $this->builder->limit($_POST['length'], $_POST['start']);
        return $this->builder->get();
    }

    private function _get_datatables_query($status)
    {
        $this->builder = $this->db->table('v_langganan a');
        $this->builder->select('a.*');
        if (session()->get('idperusahaan') != '9999999999') {
            $this->builder->where('a.idperusahaan', session()->get('idperusahaan'));
        } else {
            $this->builder->where('a.status', $status);
            $this->builder->where('a.nama_langganan !=', 'Gratis');
            $this->builder->where('a.idperusahaan !=', session()->get('idperusahaan'));
        }
        $i = 0;
        foreach ($this->column_search as $item) {
            if (!empty($_POST['search']['value'])) {
                if ($i === 0) {
                    $this->builder->groupStart(); // Untuk Menggabung beberapa kondisi "AND"
                    $this->builder->like($item, $_POST['search']['value']);
                } else {
                    $this->builder->orLike($item, $_POST['search']['value']);
                }
                if (count($this->column_search) - 1 == $i) //last loop
                    $this->builder->groupEnd();
            }
            $i++;
        }

        // -------------------------> Proses Order by        
        if (isset($_POST['order'])) {
            $this->builder->orderBy($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->builder->orderBy(key($order), $order[key($order)]);
        }
    }

    function count_filtered($statusTransaksi)
    {
        $this->_get_datatables_query($statusTransaksi);
        $this->builder->select('count(*) as jlh');
        $query = $this->builder->get();
        return $query->getRow()->jlh;
    }

    public function count_all($statusTransaksi)
    {
        $this->_get_datatables_query($statusTransaksi);
        return $this->builder->countAllResults();
    }

    public function simpan($data, $data1)
    {


        $this->db->transBegin();
        $builder = $this->db->table('transaksi_pl');
        $builder->insert($data);


        $builder1 = $this->db->table('perusahaan');
        $builder1->where('idperusahaan', session()->get('idperusahaan'));
        $builder1->update($data1);
        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            return false;
        } else {
            $this->db->transCommit();
            return true;
        }
    }

    public function simpanUpload($data, $data1)
    {
        $this->db->transBegin();
        $builder = $this->db->table('payment');
        $builder->insert($data);

        $builder1 = $this->db->table('transaksi_pl');
        $builder1->where('idperusahaan', session()->get('idperusahaan'));
        $builder1->update($data1);
        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            return false;
        } else {
            $this->db->transCommit();
            return true;
        }
    }

    public function simpanApprove($nominal_komisi, $idperusahaan)
    {
        $this->db->transBegin();
        $builder = $this->db->table('transaksi_pl');
        $rsperusahaan = $builder->getWhere(array('idperusahaan' => $idperusahaan))->getRow();
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


        $builder = $this->db->table('perusahaan_langganan');
        $builder->where('idperusahaan', $idperusahaan);
        $builder->update($data);

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
                'nominal_komisi' => $nominal_komisi,
                'bukti_pembayaran' => '',
                'status' => 'S',
            ];
            $builder1 = $this->db->table('payment');
            $builder1->where('idpayment', $rsperusahaan1->idpayment);
            $builder1->update($data1);
        }
        if (file_exists('./uploads/buktitransaksi/thumbnails/' . $rsperusahaan1->bukti_pembayaran)) {
            unlink('./uploads/buktitransaksi/thumbnails/' . $rsperusahaan1->bukti_pembayaran);
        };

        $builder2 = $this->db->table('transaksi_pl');
        $builder2->where('idperusahaan', $idperusahaan);
        $builder2->delete();

        $builder3 = $this->db->table('perusahaan_langganan a');
        $builder3->join('marketer b', 'a.kode_referal = b.kode_referal');
        $rsmarketer = $builder3->getWhere(array('a.status' => 'B', 'a.idperusahaan' => $idperusahaan))->getRow();

        if ($rsmarketer != null) {
            $builder4 = $this->db->table('marketer');
            $rssaldo = $builder4->getWhere(array('kode_referal' => $rsmarketer->kode_referal))->getRow();
            $saldo = ($rssaldo->saldo) + $nominal_komisi;
            $data2 = [
                'saldo' => $saldo,
            ];
            $builder5 = $this->db->table('marketer');
            $builder5->where('kode_referal', $rsmarketer->kode_referal);
            $builder5->update($data2);
        }


        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            return false;
        } else {
            $this->db->transCommit();
            return true;
        }
    }

    public function get_by_id($idperusahaan)
    {
        $builder = $this->db->table('payment a');
        $builder->select('a.*, b.kode_referal, b.namaperusahaan, e.diskon, f.komisi');
        $builder->join('perusahaan b', 'a.idperusahaan = b.idperusahaan');
        $builder->join('transaksi_pl c', 'a.idperusahaan = c.idperusahaan');
        $builder->join('langganan d', 'c.idlangganan = d.idlangganan');
        $builder->join('diskon e', 'd.iddiskon = e.iddiskon');
        $builder->join('komisi f', 'd.idkomisi = f.idkomisi');
        $builder->where('a.idperusahaan', $idperusahaan);
        $builder->where('a.status', 'P');
        return $builder->get();
    }


    public function get_caraBayar($idperusahaan)
    {
        $builder = $this->db->table('transaksi_pl a');
        $builder->select('a.*');
        $builder->where('a.idperusahaan', $idperusahaan);
        return $builder->get();
    }




    function get_datatablesDetail($idperusahaan)
    {
        $this->_get_datatables_queryDetail($idperusahaan);
        if (!empty($_POST['length']) && $_POST['length'] != -1)
            $this->builder->limit($_POST['length'], $_POST['start']);
        return $this->builder->get();
    }

    private function _get_datatables_queryDetail($idp)
    {
        $idperusahaan = decrypt($idp);
        $this->builder = $this->db->table('payment a');
        $this->builder->select('a.*, b.namaperusahaan');
        $this->builder->join('perusahaan b', 'a.idperusahaan = b.idperusahaan');
        $this->builder->whereIn('a.status', ['T', 'S']);
        $this->builder->where('md5(a.idperusahaan)', $idperusahaan);
        $i = 0;
        foreach ($this->column_searchDetail as $item) {
            if (!empty($_POST['search']['value'])) {
                if ($i === 0) {
                    $this->builder->groupStart(); // Untuk Menggabung beberapa kondisi "AND"
                    $this->builder->like($item, $_POST['search']['value']);
                } else {
                    $this->builder->orLike($item, $_POST['search']['value']);
                }
                if (count($this->column_searchDetail) - 1 == $i) //last loop
                    $this->builder->groupEnd();
            }
            $i++;
        }

        // -------------------------> Proses Order by        
        if (isset($_POST['order'])) {
            $this->builder->orderBy($this->column_orderDetail[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->orderDetail;
            $this->builder->orderBy(key($order), $order[key($order)]);
        }
    }

    function count_filteredDetail($idperusahaan)
    {
        $this->_get_datatables_queryDetail($idperusahaan);
        $this->builder->select('count(*) as jlh');
        $query = $this->builder->get();
        return $query->getRow()->jlh;
    }

    public function count_allDetail($idperusahaan)
    {
        $this->_get_datatables_queryDetail($idperusahaan);
        return $this->builder->countAllResults();
    }


    public function get_by_idDetail($idpayment)
    {
        $builder = $this->db->table('payment a');
        $builder->select('a.*, b.kode_referal, b.namaperusahaan');
        $builder->join('perusahaan b', 'a.idperusahaan = b.idperusahaan');
        $builder->where('a.idpayment', $idpayment);
        return $builder->get();
    }
}

/* End of file Pengguna_model.php */
/* Location: ./application/models/Pengguna_model.php */