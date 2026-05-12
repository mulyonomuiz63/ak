<?php

namespace App\Models;

use CodeIgniter\Model;

class Iklan_model extends Model
{

    // ------------------------- >   Ubah Data Disini Aja

    var $tabel     = 'iklan';
    var $idiklan = 'idiklan';

    var $column_order = array(null, 'idiklan', 'nama_iklan'); //set nama field yang bisa diurutkan
    var $column_search = array('idiklan', 'nama_iklan'); //set nama field yang akan di cari
    var $order = array('status' => 'asc'); // default order 

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
        $this->builder = $this->db->table($this->tabel);
        
        if ($_POST['status'] != '') {
            $this->builder->where('status', $_POST['status']);
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
        $builder = $this->db->table($this->tabel);
        return $builder->countAllResults();
    }

    public function get_all()
    {
        $builder = $this->db->table($this->tabel);
        return $builder->get();
    }

    public function get_by_id($id)
    {
        $idiklan = decrypt($id);
        $builder = $this->db->table($this->tabel);
        $builder->where('md5(idiklan)', $idiklan);
        return $builder->get();
    }

    public function hapus($id)
    {
        $idiklan = decrypt($id);
        $builder = $this->db->table($this->tabel);
        $builder->where('md5(idiklan)', $idiklan);
        return $builder->delete();
    }

    public function simpan($data)
    {
        $builder = $this->db->table($this->tabel);
        return $builder->insert($data);
    }

    public function updateWhere($data, $id)
    {
        $builder = $this->db->table($this->tabel);
        $builder->where('idiklan', $id);
        return $builder->update($data);
    }
}
