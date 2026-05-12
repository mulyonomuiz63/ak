<?php

namespace App\Models;

use CodeIgniter\Model;

class KomisiMarketer_model extends Model
{
    var $tabel              = 'tarikdana';
    var $column_order = array(null, 'nama'); //set nama field yang bisa diurutkan
    var $column_search = array('nama'); //set nama field yang akan di cari
    var $order = array('idtd' => 'desc'); // default order 
    public function simpan($data)
    {
        $builder = $this->db->table($this->tabel);
        return $builder->insert($data);
    }
    public function updateWhere($data, $id)
    {
        $builder = $this->db->table($this->tabel);
        $builder->where('iddiskon', $id);
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
        $this->builder = $this->db->table('tarikdana a');
        $this->builder->select('a.*, b.nama,b.norek,b.bank,b.nohp');
        $this->builder->join('marketer b', 'a.idmarketer=b.idmarketer');

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

    public function get_by_id($id)
    {
        $builder = $this->db->table('tarikdana a');
        $builder->select('a.*, b.nama,b.norek,b.bank,b.nohp');
        $builder->join('marketer b', 'a.idmarketer=b.idmarketer');
        $builder->where('a.idtd', $id);
        return $builder->get();
    }


    public function simpanApprove($idtd)
    {
        $this->db->transBegin();


        $data = [
            'status' => 'S',
            'tgl_pencairan' => date('Y-m-d H:i:s'),
        ];
        $builder = $this->db->table('tarikdana');
        $builder->where('idtd', $idtd);
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