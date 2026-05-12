<?php

namespace App\Models;

use CodeIgniter\Model;

class Marketer_model extends Model
{
    var $tabel     = 'marketer';
    var $tabeltarikdana     = 'tarikdana';
    var $column_order = array(null, 'idtd', 'nama'); //set nama field yang bisa diurutkan
    var $column_search = array('idtd', 'nama'); //set nama field yang akan di cari
    var $order = array('idtd' => 'desc'); // default order 

    var $column_orderlist = array(null, 'idmarketer', 'nama'); //set nama field yang bisa diurutkan
    var $column_searchlist = array('idmarketer', 'nama'); //set nama field yang akan di cari
    var $orderlist = array('idmarketer' => 'desc'); // default order 


    public function getMarketer()
    {
        $builder = $this->db->table($this->tabel);
        $builder->where('idmarketer', session()->get('idmarketer'));
        return $builder->get()->getFirstRow();
    }

    public function getTarikDana()
    {
        $id =  session()->get('idmarketer');
        $builder = $this->db->table($this->tabeltarikdana);
        $builder->where('idmarketer', $id);
        return $builder->get()->getResult();
    }
    public function updateWhere($data, $id)
    {
        $builder = $this->db->table($this->tabel);
        $builder->where('idmarketer', $id);
        return $builder->update($data);
    }

    public function insertTarikdana($data)
    {
        $builder1 = $this->db->table($this->tabel);
        $builder1->where('idmarketer', session()->get('idmarketer'));
        $data1 = array(
            'saldo'           => 0,
        );
        $builder1->update($data1);
        $builder = $this->db->table($this->tabeltarikdana);
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
        $this->builder->select('a.*, b.nama');
        $this->builder->join('marketer b', 'a.idmarketer=b.idmarketer');
        $this->builder->where('a.idmarketer', session()->get('idmarketer'));
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
        $builder = $this->db->table($this->tabeltarikdana);
        return $builder->countAllResults();
    }

    function get_datatableslist()
    {
        $this->_get_datatables_querylist();
        if (!empty($_POST['length']) && $_POST['length'] != -1)
            $this->builder->limit($_POST['length'], $_POST['start']);
        return $this->builder->get();
    }

    private function _get_datatables_querylist()
    {
        $this->builder = $this->db->table('marketer');
        $this->builder->select('*');
        $i = 0;

        foreach ($this->column_searchlist as $item) {
            if (!empty($_POST['search']['value'])) {
                if ($i === 0) {
                    $this->builder->groupStart(); // Untuk Menggabung beberapa kondisi "AND"
                    $this->builder->like($item, $_POST['search']['value']);
                } else {
                    $this->builder->orLike($item, $_POST['search']['value']);
                }
                if (count($this->column_searchlist) - 1 == $i) //last loop
                    $this->builder->groupEnd();
            }
            $i++;
        }

        // -------------------------> Proses Order by        
        if (isset($_POST['order'])) {
            $this->builder->orderBy($this->column_orderlist[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->orderlist)) {
            $order = $this->orderlist;
            $this->builder->orderBy(key($order), $order[key($order)]);
        }
    }

    function count_filteredlist()
    {
        $this->_get_datatables_querylist();
        $this->builder->select('count(*) as jlh');
        $query = $this->builder->get();
        return $query->getRow()->jlh;
    }

    public function count_alllist()
    {
        $builder = $this->db->table('marketer');
        return $builder->countAllResults();
    }
}

/* End of file Pengguna_model.php */
/* Location: ./application/models/Pengguna_model.php */