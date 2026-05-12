<?php

namespace App\Models;

use CodeIgniter\Model;

class Harga_model extends Model
{
    var $tabel              = 'langganan';



    public function getDiskon()
    {
        $this->builder = $this->db->table('diskon');
        $this->builder->where('deleted_at', null);
        return $this->builder->get()->getResult();
    }

    public function getKomisi()
    {
        $this->builder = $this->db->table('komisi');
        $this->builder->where('deleted_at', null);
        return $this->builder->get()->getResult();
    }

    public function get_by_id($id)
    {
        $idlangganan = decrypt($id);
        $builder = $this->db->table($this->tabel);
        $builder->where('md5(idlangganan)', $idlangganan);
        return $builder->get();
    }


    public function simpan($data)
    {
        $builder = $this->db->table($this->tabel);
        return $builder->insert($data);
    }
    public function updateWhere($data, $id)
    {
        $idlangganan = decrypt($id);
        $builder = $this->db->table($this->tabel);
        $builder->where('md5(idlangganan)', $idlangganan);
        return $builder->update($data);
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
        $this->builder = $this->db->table('langganan a');
        $this->builder->select('a.*, b.diskon, c.komisi');
        $this->builder->join('diskon b', 'a.iddiskon=b.iddiskon');
        $this->builder->join('komisi c', 'a.idkomisi=c.idkomisi');
        $this->builder->where('a.deleted_at', null);
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
        $this->_get_datatables_query();
        return $this->builder->countAllResults();
    }

    public function hapus($id)
    {
        $idlangganan = decrypt($id);
        $this->db->transBegin();

        $builder = $this->db->table($this->tabel);
        $builder->where('md5(idlangganan)', $idlangganan);
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