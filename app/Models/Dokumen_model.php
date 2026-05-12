<?php

namespace App\Models;

use CodeIgniter\Model;

class Dokumen_model extends Model
{

    // ------------------------- >   Ubah Data Disini Aja

    var $tabel     = 'arsip';

    var $column_order = array(null, 'id', 'nama_file', 'status'); //set nama field yang bisa diurutkan
    var $column_search = array('id', 'nama_file', 'status'); //set nama field yang akan di cari
    var $order = array('created_at' => 'desc', 'nama_file' => 'asc'); // default order 

    // ----------------------------


    function get_datatables($status)
    {
        $this->_get_datatables_query($status);
        if (!empty($_POST['length']) && $_POST['length'] != -1)
            $this->builder->limit($_POST['length'], $_POST['start']);
        return $this->builder->get();
    }

    private function _get_datatables_query($status)
    {
        $this->builder = $this->db->table($this->tabel);
        $this->builder->where('deleted_at', null);
        $this->builder->where('status', $status);
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

    function count_filtered($status)
    {
        $this->_get_datatables_query($status);
        $this->builder->select('count(*) as jlh');
        $query = $this->builder->get();
        return $query->getRow()->jlh;
    }

    public function count_all()
    {
        $builder = $this->db->table($this->tabel);
        return $builder->countAllResults();
    }


    public function get_by_id($id = null)
    {
        $id = decrypt($id);
        $builder = $this->db->table($this->tabel);
        if ($id != null) {
            $builder->where('md5(id)', $id);
        }

        return $builder->get();
    }

    public function get_by_idperusahaan($id = null)
    {
        $id = decrypt($id);
        $builder = $this->db->table('perusahaan');
        if ($id != null) {
            $builder->where('md5(idperusahaan)', $id);
        }

        return $builder->get();
    }

    public function hapus($id)
    {
        $id = decrypt($id);
        $builder = $this->db->table($this->tabel);
        $builder->where('md5(id)', $id);
        return $builder->delete();
    }

    public function simpan($data)
    {
        $builder = $this->db->table($this->tabel);
        return $builder->insert($data);
    }

    public function updateWhere($data, $id)
    {
        $id = decrypt($id);
        $builder = $this->db->table($this->tabel);
        $builder->where('md5(id)', $id);
        return $builder->update($data);
    }
}

/* End of file Dokumen_model.php */
/* Location: ./application/models/Dokumen_model.php */