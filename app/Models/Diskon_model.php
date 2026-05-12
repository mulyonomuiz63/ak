<?php

namespace App\Models;

use CodeIgniter\Model;

class Diskon_model extends Model
{
    var $tabel              = 'diskon';
    public function getDiskon()
    {
        $builder = $this->db->table($this->tabel);
        $builder->where('iddiskon', session()->get('iddiskon'));
        return $builder->get()->getFirstRow();
    }


    public function simpan($data)
    {
        $builder = $this->db->table($this->tabel);
        return $builder->insert($data);
    }
    public function updateWhere($data, $id)
    {
        $iddiskon = decrypt($id);
        $builder = $this->db->table($this->tabel);
        $builder->where('md5(iddiskon)', $iddiskon);
        return $builder->update($data);
    }

    public function insertData($data)
    {
        $builder = $this->db->table($this->tabel);
        return $builder->insert($data);
    }



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
        $this->builder->where('deleted_at', null);
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

    public function get_by_id($id)
    {
        $iddiskon = decrypt($id);
        $builder = $this->db->table($this->tabel);
        $builder->where('md5(iddiskon)', $iddiskon);
        return $builder->get();
    }


    public function hapus($id)
    {
        $iddiskon = decrypt($id);

        $this->db->transBegin();

        $builder = $this->db->table($this->tabel);
        $builder->where('md5(iddiskon)', $iddiskon);
        $data = array(
            'deleted_at'     => date('Y-m-d H:i:s'),
        );
        $builder->update($data);
        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            return false;
        } else {
            $this->db->transCommit();
            return true;
        }
    }
}

/* End of file Pengguna_model.php */
/* Location: ./application/models/Pengguna_model.php */