<?php

namespace App\Models;

use CodeIgniter\Model;

class Akun_model extends Model
{

    // ------------------------- >   Ubah Data Disini Aja

    var $tabelview = 'v_akun';
    var $tabel     = 'akun';
    var $keyakun = 'keyakun';

    var $column_order = array(null, 'kdakun', 'nmakun', 'level', 'saldonormal2'); //set nama field yang bisa diurutkan
    var $column_search = array('kdakun', 'nmakun', 'level', 'saldonormal2'); //set nama field yang akan di cari
    var $order = array('kdakun' => 'asc', 'level' => 'asc'); // default order 

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
        
        $i = 0;
        
        if (session()->get("idpengguna") == "8888888888") {
            $idperusahaan = $_POST['idperusahaan'];
            $this->builder->where('idperusahaan', $idperusahaan);
        }else{
            $this->builder->where('idperusahaan', session()->get('idperusahaan'));
        }

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
        $builder->where('idperusahaan', session()->get('idperusahaan'));
        return $builder->get();
    }

    public function get_by_id($id = null, $idp = null)
    {
        $keyakun = decrypt($id);
        $builder = $this->db->table($this->tabelview);
        if ($id != null) {
            $builder->where('md5(keyakun)', $keyakun);
        }
        // if (session()->get('idpengguna') != '8888888888') {
        // $builder->where('idperusahaan', session()->get('idperusahaan'));
        if ($idp != null) {
            $idperusahaan = decrypt($idp);
            $builder->where('md5(idperusahaan)', $idperusahaan);
        }
        // }
        return $builder->get();
    }

    public function hapus($id)
    {
        $keyakun = decrypt($id);
        $builder1 = $this->db->table('jurnaldetail')->where('md5(keyakun)', $keyakun)->get()->getNumRows();
        if ($builder1 < 1) {
            $builder = $this->db->table($this->tabel);
            $builder->where('md5(keyakun)', $keyakun);
            return $builder->delete();
        } else {
            return false;
        }
    }

    public function simpan($data)
    {
        $builder = $this->db->table($this->tabel);
        return $builder->insert($data);
    }

    public function updateWhere($data, $keyakun)
    {
        $id = decrypt($keyakun);
        $builder = $this->db->table($this->tabel);
        $builder->where('md5(keyakun)', $id);
        return $builder->update($data);
    }
    public function cekakun($keyakun)
    {
        $id = decrypt($keyakun);
        $builder = $this->db->table("jurnaldetail");
        $builder->where('md5(keyakun)', $id);
        return $builder->get()->getResult();
    }
}

/* End of file Akun_model.php */
/* Location: ./application/models/Akun_model.php */