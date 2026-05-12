<?php

namespace App\Models;

use CodeIgniter\Model;

class Perusahaan_model extends Model
{

    // ------------------------- >   Ubah Data Disini Aja

    var $tabelview = 'v_perusahaan';
    var $tabel     = 'perusahaan';
    var $idperusahaan = 'idperusahaan';

    var $column_order = array(null, 'idperusahaan', 'namaperusahaan', 'tglmulaiusaha', 'tglregistrasi', 'statusaktif2'); //set nama field yang bisa diurutkan
    var $column_search = array('idperusahaan', 'namaperusahaan', 'statusaktif2'); //set nama field yang akan di cari
    var $order = array('idperusahaan' => 'desc'); // default order 

    // ----------------------------


    function get_datatables()
    {
        $this->_get_datatables_query();
        if (!empty($_POST['length']) && $_POST['length'] != -1)
            $this->builder->limit($_POST['length'], $_POST['start']);
        return $this->builder->get();
    }

    private function _get_datatables_query()
    {
        $this->builder = $this->db->table($this->tabelview);
        $this->builder->where('idperusahaan != "9999999999"');

        $builder3 = $this->db->table('v_langganan a');
        $langganan = $builder3->getWhere(array('a.status' => 'B', 'a.idperusahaan' => session()->get('idperusahaan')))->getRow();
        if ($langganan->status == "B") {
            $dataP = [
                'tglregistrasi'  => date('Y-m-d H:i:s', strtotime($langganan->tgl_mulai)),
                'tglberakhir'    => $langganan->tgl_akhir,
            ];
            $builderP = $this->db->table('perusahaan');
            $builderP->where('idperusahaan', session()->get('idperusahaan'));
            $builderP->update($dataP);
        }

        if (session()->get('level') != 9)
            $this->builder->where('idperusahaan', session()->get('idperusahaan'));

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

    function count_filtered()
    {
        $this->_get_datatables_query();
        $this->builder->select('count(*) as jlh');
        $query = $this->builder->get();
        return $query->getRow()->jlh;
    }

    public function count_all()
    {
        $builder = $this->db->table($this->tabelview);
        return $builder->countAllResults();
    }

    public function get_all()
    {
        $builder = $this->db->table($this->tabel);
        return $builder->get();
    }

    public function get_by_id($id)
    {
        $idperusahaan = decrypt($id);
        $builder = $this->db->table($this->tabel);
        if (session()->get('level') != 9) {
            // $builder->where('idperusahaan', session()->get('idperusahaan'));
            $builder->where('md5(idperusahaan)', $idperusahaan);
        } else {
            $builder->where('md5(idperusahaan)', $idperusahaan);
        }
        return $builder->get();
    }

    public function hapus($id)
    {
        $idperusahaan = decrypt($id);
        $builder = $this->db->table('pengguna');
        $builder->where('md5(idperusahaan)', $idperusahaan);
        $builder->delete();
        $builder2 = $this->db->table($this->tabel);
        $builder2->where('md5(idperusahaan)', $idperusahaan);
        return $builder2->delete();
    }

    public function simpan($data, $dataPengguna)
    {
        $this->db->transBegin();

        $builder = $this->db->table($this->tabel);
        $builder->insert($data);
        $builder2 = $this->db->table('pengguna');
        $builder2->insert($dataPengguna);

        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            return false;
        } else {
            $this->db->transCommit();
            return true;
        }
    }

    public function updateWhere($data, $id)
    {
        $builder = $this->db->table($this->tabel);
        $builder->where('idperusahaan', $id);
        return $builder->update($data);
    }
}

/* End of file Perusahaan_model.php */
/* Location: ./application/models/Perusahaan_model.php */