<?php

namespace App\Models;

use CodeIgniter\Model;

class Jurnal_model extends Model
{

    // ------------------------- >   Ubah Data Disini Aja

    var $tabelview = 'v_jurnal';
    var $tabel     = 'jurnal';
    var $idjurnal = 'idjurnal';

    var $column_order = array(null, 'idjurnal', 'tgljurnal', 'keterangan', 'tag', 'jumlah', null); //set nama field yang bisa diurutkan
    var $column_search = array('idjurnal', 'tgljurnal', 'keterangan', "referensi", 'tag'); //set nama field yang akan di cari
    var $order = array('tgljurnal' => 'desc', 'instTgl' => 'asc'); // default order 

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
            if ($idperusahaan != "") {
                $this->builder->where('idperusahaan', $idperusahaan);
            }
        }
        
        if (session()->get('idpengguna') != '8888888888') {
            if(session('level') == 1){
                $this->builder->where('idperusahaan', session()->get('idperusahaan'));
            }elseif(session('level_super') == 3){
                $this->builder->where('idperusahaan', session()->get('idperusahaan'));
            }else{
                $this->builder->where('idpengguna', session()->get('idpengguna'));
            }
        }
        
        if($_POST['status_approve'] != '2'){
            if ($_POST['tahun'] != '') {
                $this->builder->where('YEAR(tgljurnal)', $_POST['tahun']);
            }
            
            if ($_POST['bulan'] != '') {
                $this->builder->where('MONTH(tgljurnal)', $_POST['bulan']);
            }
        }
        
        if ($_POST['status_approve'] != '') {
            if ($_POST['status_approve'] === 'all') {
                $this->builder
                    ->groupStart()
                        ->where('approve', '2')
                        ->orGroupStart()
                            ->where('approve', '1')
                            ->where('keterangan_approve IS NOT NULL', null, false)
                            ->where('keterangan_approve !=', '')
                        ->groupEnd()
                    ->groupEnd();
            } else {
                $this->builder->where('approve', $_POST['status_approve']);
                session()->set('status_approve', $_POST['status_approve']);
            }
        }
        
        $cari = $_POST['cari'];
        if ($cari != "") {
            $tgl = date('Y-m-d', strtotime($cari));

            $this->builder->groupStart()
                ->like('keterangan', $cari)
                ->orLike('idjurnal', $cari)
                ->orLike('referensi', $cari)
                ->orLike('tgljurnal', $tgl)
            ->groupEnd();
        }
        
        foreach ($this->column_search as $item) {
            if (!empty($_POST['search']['value'])) {
                if ($i === 0) {
                    $this->builder->groupStart();
                    $this->builder->like($item, $_POST['search']['value']);
                } else {
                    $this->builder->orLike($item, $_POST['search']['value']);
                }
                if (count($this->column_search) - 1 == $i)
                    $this->builder->groupEnd();
            }
            $i++;
        }

        // -------------------------> Proses Order by        
        if (isset($_POST['order'])) {
            $this->builder->orderBy($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            
            // =========================================================================================
            // PERUBAHAN DI SINI: MENGURUTKAN JURNAL TIDAK BALANCE KE PALING ATAS
            // Logic: Jika Debet == Kredit beri nilai 1. Jika tidak, beri nilai 0. Urutkan dari 0 (ASC)
            // =========================================================================================
            $subquery_balance = '(SELECT CASE WHEN COALESCE(SUM(debet), 0) = COALESCE(SUM(kredit), 0) THEN 1 ELSE 0 END FROM jurnaldetail WHERE jurnaldetail.idjurnal = ' . $this->tabelview . '.idjurnal)';
            
            // Parameter ke-3 (false) sangat penting agar CI4 tidak menambahkan tanda backtick (`) yang merusak query
            $this->builder->orderBy($subquery_balance, 'ASC', false);
            
            // Urutan selanjutnya baru berdasarkan tanggal terbaru
            $this->builder->orderBy('tgljurnal', 'desc');
            $this->builder->orderBy('tglinsert', 'desc');
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
        $builder = $this->db->table('jurnal');
        return $builder->countAllResults();
    }

    public function get_all()
    {
        $builder = $this->db->table($this->tabelview);
        return $builder->get();
    }

    public function get_by_id($id)
    {
        $idjurnal = decrypt($id);
        $builder = $this->db->table($this->tabelview);
        $builder->where('md5(idjurnal)', $idjurnal);
        if (session()->get('idpengguna') != '8888888888') {
            $builder->where('idperusahaan', session()->get('idperusahaan'));
        }
        return $builder->get();
    }
    
    public function get_validasi_by_id($id)
    {
        $idjurnal = decrypt($id);
        $builder = $this->db->table($this->tabelview);
        $builder->where('md5(idjurnal)', $idjurnal);
        return $builder->get();
    }



    public function getJurnalFile($id)
    {
        $idjurnal = decrypt($id);
        $builder = $this->db->table('jurnalfile');
        $builder->where('md5(idjurnal)', $idjurnal);
        return $builder->get();
    }

    public function getJurnal($id)
    {
        $idjurnal = decrypt($id);
        $builder = $this->db->table('jurnal a');
        $builder->select('a.*, (select count(b.id) from jurnalfile b where b.idjurnal= a.idjurnal) as total');
        $builder->where('md5(a.idjurnal)', $idjurnal);
        return $builder->get();
    }

    public function get_detail_by_id($id)
    {
        $idjurnal = decrypt($id);
        $builder = $this->db->table('v_jurnaldetail');
        $builder->where('md5(idjurnal)', $idjurnal);
        return $builder->get();
    }


    public function hapus($id)
    {
        $idjurnal = decrypt($id);
        $this->db->transBegin();
        $builder = $this->db->table('jurnaldetail');

        $builder->where('md5(idjurnal)', $idjurnal);
        $builder->delete();

        $builder2 = $this->db->table('jurnal');
        $builder2->where('md5(idjurnal)', $idjurnal);
        $builder2->delete();

        $builder3 = $this->db->table('jurnalfile');
        $builder3->where('md5(idjurnal)', $idjurnal);
        $builder3->delete();

        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            return false;
        } else {
            $this->db->transCommit();
            return true;
        }
    }

    public function simpanfile($data)
    {
        $this->db->transBegin();

        $builder = $this->db->table('jurnalfile');
        $builder->insert($data);

        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            return false;
        } else {
            $this->db->transCommit();
            return true;
        }
    }

    public function simpan($data, $arrDetail, $idjurnal)
    {
        $this->db->transBegin();

        $builder = $this->db->table('jurnal');
        $builder->insert($data);
        $this->db->query('delete from jurnaldetail where idjurnal="' . $idjurnal . '"');
        $builder2 = $this->db->table('jurnaldetail');
        $builder2->insertBatch($arrDetail);

        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            return false;
        } else {
            $this->db->transCommit();
            return true;
        }
    }

    public function updateWhere($data, $arrDetail, $idjurnal)
    {

        $this->db->transBegin();

        $builder = $this->db->table('jurnal');
        // $this->db->start_cache();
        $builder->where('idjurnal', $idjurnal);
        // $this->db->stop_cache();

        $builder->update($data);

        // $this->db->flush_cache();
        $builder2 = $this->db->table('jurnaldetail');
        $this->db->query('delete from jurnaldetail where idjurnal="' . $idjurnal . '"');
        $builder2->insertBatch($arrDetail);

        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            return false;
        } else {
            $this->db->transCommit();
            return true;
        }
    }

    public function updateApprove($id, $data)
    {

        $this->db->transBegin();
        
        $builders = $this->db->table('jurnal');
        $builders->select('approve');
        $builders->where('md5(idjurnal)', decrypt($id));
        $cekJurnal = $builders->get()->getRowObject();
        if(!empty($cekJurnal)){
           
            $builder = $this->db->table('jurnal');
            // $this->db->start_cache();
            $builder->where('md5(idjurnal)', decrypt($id));
            // $this->db->stop_cache();
    
            $builder->update($data);
        }
        


        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            return false;
        } else {
            $this->db->transCommit();
            return true;
        }
    }

    public function deleteFile($data, $idjurnal)
    {

        $this->db->transBegin();

        $builder = $this->db->table('jurnal');
        // $this->db->start_cache();
        $builder->where('md5(idjurnal)', decrypt($idjurnal));
        // $this->db->stop_cache();

        $builder->update($data);
        if ($this->db->transStatus() === FALSE) {
            $this->db->transRollback();
            return false;
        } else {
            $this->db->transCommit();
            return true;
        }
    }

    public function deleteFileNew($idjurnal)
    {
        $builder = $this->db->table('jurnalfile');

        $builder->where('id', $idjurnal);
        $builder->delete();
    }

    public function get_jurnal_cetak($id)
    {
        $idjurnal = decrypt($id);
        $query = "select v_jurnaldetail.* from v_jurnaldetail 
                where md5(idjurnal)='" . $idjurnal . "' order by tgljurnal desc, idjurnal desc, nourut asc";
        return $this->db->query($query);
    }
    
    public function get_jurnal()
    {
        if(session()->get('idpengguna') != '8888888888'){
            if(session('level') == 1 || session('level_super') == 3){
                $query = "select YEAR(a.tgljurnal) as tgljurnal from jurnal a join pengguna b on a.idpengguna=b.idpengguna 
                        where b.idperusahaan='" . session('idperusahaan') . "' group by YEAR(a.tgljurnal) order by a.tgljurnal desc";
            }else{
                 $query = "select YEAR(a.tgljurnal) as tgljurnal from jurnal a join pengguna b on a.idpengguna=b.idpengguna 
                        where a.idpengguna='" . session('idpengguna') . "' group by YEAR(a.tgljurnal) order by a.tgljurnal desc";
            }
        }else{
            $query = "select YEAR(a.tgljurnal) as tgljurnal from jurnal a join pengguna b on a.idpengguna=b.idpengguna group by YEAR(a.tgljurnal) order by a.tgljurnal desc";
        }
        return $this->db->query($query);
    }
    
    public function get_jurnal_bulan()
    {
        if(session()->get('idpengguna') != '8888888888'){
            if(session('level') == 1 || session('level_super') == 3){
                $query = "select MONTH(a.tgljurnal) as bulan, YEAR(a.tgljurnal) as tahun from jurnal a join pengguna b on a.idpengguna=b.idpengguna 
                        where b.idperusahaan='" . session('idperusahaan') . "'  order by a.tgljurnal desc limit 1";
            }else{
                 $query = "select MONTH(a.tgljurnal) as bulan, YEAR(a.tgljurnal) as tahun from jurnal a join pengguna b on a.idpengguna=b.idpengguna 
                        where a.idpengguna='" . session('idpengguna') . "' order by a.tgljurnal desc limit 1";
            }
        }else{
            $query = "select MONTH(a.tgljurnal) as bulan, YEAR(a.tgljurnal) as tahun from jurnal a join pengguna b on a.idpengguna=b.idpengguna order by a.tgljurnal desc limit 1";
        }
        return $this->db->query($query);
    }
}

/* End of file Jurnal_model.php */
/* Location: ./application/models/Jurnal_model.php */