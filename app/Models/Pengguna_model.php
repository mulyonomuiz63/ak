<?php

namespace App\Models;

use CodeIgniter\Model;

class Pengguna_model extends Model
{

    // ------------------------- >   Ubah Data Disini Aja

    var $tabelview = 'v_pengguna';
    var $tabel     = 'pengguna';
    var $idpengguna = 'idpengguna';

    var $column_order = array(null, 'idpengguna', 'namapengguna', 'username', 'level2'); //set nama field yang bisa diurutkan
    var $column_search = array('idpengguna', 'namapengguna', 'username', 'level2'); //set nama field yang akan di cari
    var $order = array('idpengguna' => 'desc'); // default order 

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
        if (session()->get('idpengguna') != '8888888888') {
            $this->builder->where('idperusahaan', session()->get('idperusahaan'));
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
        $builder = $this->db->table($this->tabelview);
        return $builder->get();
    }

    public function deleteFile($data, $idpengguna)
    {
        $builder = $this->db->table('pengguna');

        $builder->where('idpengguna', $idpengguna);
        return $builder->update($data);
    }

    public function get_by_id($id)
    {
        $idperusahaan = decrypt($id);
        $builder = $this->db->table($this->tabelview);
        $builder->where('md5(idpengguna)', $idperusahaan);
        if (session()->get('idpengguna') != '8888888888') {
            $builder->where('idperusahaan', session()->get('idperusahaan'));
        }
        return $builder->get();
    }
    public function get_by_id_id_pic($id)
    {
        $builder = $this->db->table('pengguna');
        $builder->where('idpengguna', $id);

        return $builder->get();
    }

    public function hapus($id)
    {
        $idperusahaan = decrypt($id);
        $builder = $this->db->table($this->tabel);
        $builder->where('md5(idpengguna)', $idperusahaan);
        return $builder->delete();
    }

    public function simpan($data)
    {
        $builder = $this->db->table($this->tabel);
        $builder->where('username', $data['username']);
        $isexist = $builder->countAllResults();
        if ($isexist)
            return false;
        $builder2 = $this->db->table($this->tabel);
        return $builder2->insert($data);
    }

    public function updateWhere($data, $id)
    {
        $builder = $this->db->table($this->tabel);
        $builder->where('idpengguna', $id);
        return $builder->update($data);
    }
}

/* End of file Pengguna_model.php */
/* Location: ./application/models/Pengguna_model.php */