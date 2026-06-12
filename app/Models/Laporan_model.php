<?php

namespace App\Models;

use CodeIgniter\Model;

class Laporan_model extends Model
{
    public function get_jurnal($tglawal, $tglakhir, $idperusahaan, $orderby = "desc")
    {
        $query = "select v_jurnaldetail.* from v_jurnaldetail 
				where tgljurnal between '" . $tglawal . "' and '" . $tglakhir . "' and idperusahaan='" . $idperusahaan . "' order by tgljurnal  $orderby, idjurnal desc";
        return $this->db->query($query);
    }

    public function get_bukubesar($tglawal, $tglakhir, $kdakun = '', $idp, $asc = 'desc')
    {
        $idperusahaan = decrypt($idp);
        if ($kdakun)
            $query = "select *, UNIX_TIMESTAMP(tglinsert) as intTgl from v_jurnaldetail where kdakun='" . $kdakun . "' and tgljurnal between '" . $tglawal . "' and '" . $tglakhir . "' and md5(idperusahaan)='" . $idperusahaan . "' order by tgljurnal $asc, intTgl $asc";
        else
            $query = "select *, UNIX_TIMESTAMP(tglinsert) as intTgl from v_jurnaldetail where tgljurnal between '" . $tglawal . "' and '" . $tglakhir . "' and md5(idperusahaan)='" . $idperusahaan . "' order by tgljurnal $asc, intTgl $asc";
        return $this->db->query($query);
    }

    public function get_bukubesar_saldoawal($tglawal, $tglakhir, $kdakun = '', $idperusahaan, $asc = 'desc')
    {
        if ($kdakun)
            $query = "select *, UNIX_TIMESTAMP(tglinsert) as intTgl from v_jurnaldetail where kdakun='" . $kdakun . "' and tgljurnal between '" . $tglawal . "' and '" . $tglakhir . "' and idperusahaan='" . $idperusahaan . "' order by tgljurnal $asc, intTgl $asc limit 1";
        else
            $query = "select *, UNIX_TIMESTAMP(tglinsert) as intTgl from v_jurnaldetail where tgljurnal between '" . $tglawal . "' and '" . $tglakhir . "' and idperusahaan='" . $idperusahaan . "' order by tgljurnal $asc, intTgl $asc limit 1";
        return $this->db->query($query);
    }

    public function get_lapposisikeuangan($tglawal, $tglakhir, $idperusahaan, $level ='')
    {
        $query = "
            WITH akun_index AS (
                SELECT 
                    a.keyakun,
                    a.kdakun,
                    a.nmakun,
                    a.level,
                    a.saldonormal,
                    a.idperusahaan,
                    -- kdakun_index: gunakan full sesuai level (level4 = full kdakun)
                    CASE 
                        WHEN a.level = '1' THEN LEFT(a.kdakun, 1)
                        WHEN a.level = '2' THEN LEFT(a.kdakun, 2)
                        WHEN a.level = '3' THEN LEFT(a.kdakun, 3)
                        WHEN a.level = '4' THEN a.kdakun
                    END AS kdakun_index,
                    -- kdakun_parent: untuk level4 parent = prefix 3 (level3 kode)
                    CASE 
                        WHEN a.level = '1' THEN ''
                        WHEN a.level = '2' THEN LEFT(a.kdakun, 1)
                        WHEN a.level = '3' THEN LEFT(a.kdakun, 2)
                        WHEN a.level = '4' THEN LEFT(a.kdakun, 3)
                    END AS kdakun_parent
                FROM akun a
                WHERE a.status='0'
                  AND a.idperusahaan=?
                  $level
                  AND LEFT(a.kdakun,1) IN ('1','2','3')
            ),
            
            -- raw_trans: aggregate per full kdakun (so level4 totals available)
            raw_trans AS (
                SELECT
                    p.idperusahaan,
                    c.kdakun AS kdakun_full,            -- full kode akun (level4 leaf or other)
                    LEFT(c.kdakun,1) AS lvl1,
                    LEFT(c.kdakun,2) AS lvl2,
                    LEFT(c.kdakun,3) AS lvl3,
                    SUM(jd.debet) AS tdebet,
                    SUM(jd.kredit) AS tkredit
                FROM jurnaldetail jd
                JOIN jurnal j ON j.idjurnal = jd.idjurnal
                JOIN akun c ON c.keyakun = jd.keyakun
                JOIN pengguna p ON p.idpengguna = j.idpengguna
                WHERE c.status='0'
                  AND p.idperusahaan=?
                  AND j.tgljurnal BETWEEN ? AND ?
                GROUP BY c.kdakun, p.idperusahaan
            ),
            
            -- realisasi: sediakan semua level (lvl1..lvl4) sehingga dapat join tepat
            realisasi AS (
                SELECT
                    idperusahaan,
                    lvl1,
                    lvl2,
                    lvl3,
                    kdakun_full AS lvl4,
                    SUM(tdebet) AS total_debet,
                    SUM(tkredit) AS total_kredit
                FROM raw_trans
                GROUP BY idperusahaan, lvl1, lvl2, lvl3, kdakun_full
            )
            
            SELECT 
                ai.keyakun,
                ai.kdakun,
                ai.nmakun,
                ai.level,
                ai.saldonormal,
                ai.idperusahaan,
                ai.kdakun_index,
                ai.kdakun_parent,
                -- hitung sesuai saldonormal akun pada level itu
                SUM(
                    CASE ai.saldonormal
                        WHEN 'D' THEN COALESCE(r.total_debet,0) - COALESCE(r.total_kredit,0)
                        ELSE COALESCE(r.total_kredit,0) - COALESCE(r.total_debet,0)
                    END
                ) AS jumlah
            FROM akun_index ai
            LEFT JOIN realisasi r 
                   ON (ai.level = 1 AND r.lvl1 = ai.kdakun_index AND r.idperusahaan = ai.idperusahaan)
                   OR (ai.level = 2 AND r.lvl2 = ai.kdakun_index AND r.idperusahaan = ai.idperusahaan)
                   OR (ai.level = 3 AND r.lvl3 = ai.kdakun_index AND r.idperusahaan = ai.idperusahaan)
                   OR (ai.level = 4 AND r.lvl4 = ai.kdakun_index AND r.idperusahaan = ai.idperusahaan)
            GROUP BY 
                ai.keyakun, ai.kdakun, ai.nmakun, ai.level,
                ai.kdakun_index, ai.kdakun_parent, ai.saldonormal, ai.idperusahaan
            ORDER BY ai.kdakun ASC";
         return $this->db->query($query, [$idperusahaan, $idperusahaan, $tglawal, $tglakhir]);
    }

    public function get_laplabarugi($tglawal, $tglakhir, $idperusahaan, $level = '')
    {
        $query = "
            WITH akun_index AS (
                SELECT 
                    a.keyakun,
                    a.kdakun,
                    a.nmakun,
                    a.level,
                    a.saldonormal,
                    a.idperusahaan,
                    -- kdakun_index: gunakan full sesuai level (level4 = full kdakun)
                    CASE 
                        WHEN a.level = '1' THEN LEFT(a.kdakun, 1)
                        WHEN a.level = '2' THEN LEFT(a.kdakun, 2)
                        WHEN a.level = '3' THEN LEFT(a.kdakun, 3)
                        WHEN a.level = '4' THEN a.kdakun
                    END AS kdakun_index,
                    -- kdakun_parent: untuk level4 parent = prefix 3 (level3 kode)
                    CASE 
                        WHEN a.level = '1' THEN ''
                        WHEN a.level = '2' THEN LEFT(a.kdakun, 1)
                        WHEN a.level = '3' THEN LEFT(a.kdakun, 2)
                        WHEN a.level = '4' THEN LEFT(a.kdakun, 3)
                    END AS kdakun_parent
                FROM akun a
                WHERE a.status='0'
                  AND a.idperusahaan=?
                  $level
                  AND LEFT(a.kdakun,1) IN ('4','5','6', '7')
            ),
            
            -- raw_trans: aggregate per full kdakun (so level4 totals available)
            raw_trans AS (
                SELECT
                    p.idperusahaan,
                    c.kdakun AS kdakun_full,            -- full kode akun (level4 leaf or other)
                    LEFT(c.kdakun,1) AS lvl1,
                    LEFT(c.kdakun,2) AS lvl2,
                    LEFT(c.kdakun,3) AS lvl3,
                    SUM(jd.debet) AS tdebet,
                    SUM(jd.kredit) AS tkredit
                FROM jurnaldetail jd
                JOIN jurnal j ON j.idjurnal = jd.idjurnal
                JOIN akun c ON c.keyakun = jd.keyakun
                JOIN pengguna p ON p.idpengguna = j.idpengguna
                WHERE c.status='0'
                  AND p.idperusahaan=?
                  AND j.tgljurnal BETWEEN ? AND ?
                GROUP BY c.kdakun, p.idperusahaan
            ),
            
            -- realisasi: sediakan semua level (lvl1..lvl4) sehingga dapat join tepat
            realisasi AS (
                SELECT
                    idperusahaan,
                    lvl1,
                    lvl2,
                    lvl3,
                    kdakun_full AS lvl4,
                    SUM(tdebet) AS total_debet,
                    SUM(tkredit) AS total_kredit
                FROM raw_trans
                GROUP BY idperusahaan, lvl1, lvl2, lvl3, kdakun_full
            )
            
            SELECT 
                ai.keyakun,
                ai.kdakun,
                ai.nmakun,
                ai.level,
                ai.saldonormal,
                ai.idperusahaan,
                ai.kdakun_index,
                ai.kdakun_parent,
                -- hitung sesuai saldonormal akun pada level itu
                SUM(
                    CASE ai.saldonormal
                        WHEN 'D' THEN COALESCE(r.total_debet,0) - COALESCE(r.total_kredit,0)
                        ELSE COALESCE(r.total_kredit,0) - COALESCE(r.total_debet,0)
                    END
                ) AS jumlah
            FROM akun_index ai
            LEFT JOIN realisasi r 
                   ON (ai.level = 1 AND r.lvl1 = ai.kdakun_index AND r.idperusahaan = ai.idperusahaan)
                   OR (ai.level = 2 AND r.lvl2 = ai.kdakun_index AND r.idperusahaan = ai.idperusahaan)
                   OR (ai.level = 3 AND r.lvl3 = ai.kdakun_index AND r.idperusahaan = ai.idperusahaan)
                   OR (ai.level = 4 AND r.lvl4 = ai.kdakun_index AND r.idperusahaan = ai.idperusahaan)
            GROUP BY 
                ai.keyakun, ai.kdakun, ai.nmakun, ai.level,
                ai.kdakun_index, ai.kdakun_parent, ai.saldonormal, ai.idperusahaan
            ORDER BY ai.kdakun ASC";
            return $this->db->query($query, [$idperusahaan, $idperusahaan, $tglawal, $tglakhir]);
    }


    public function get_laprasio($tglawal, $tglakhir, $idperusahaan)
    {
        $query = "
            WITH akun_index AS (
                SELECT 
                    a.keyakun,
                    a.kdakun,
                    a.nmakun,
                    a.level,
                    a.saldonormal,
                    a.idperusahaan,
                    -- kdakun_index: gunakan full sesuai level (level4 = full kdakun)
                    CASE 
                        WHEN a.level = '1' THEN LEFT(a.kdakun, 1)
                        WHEN a.level = '2' THEN LEFT(a.kdakun, 2)
                        WHEN a.level = '3' THEN LEFT(a.kdakun, 3)
                        WHEN a.level = '4' THEN a.kdakun
                    END AS kdakun_index,
                    -- kdakun_parent: untuk level4 parent = prefix 3 (level3 kode)
                    CASE 
                        WHEN a.level = '1' THEN ''
                        WHEN a.level = '2' THEN LEFT(a.kdakun, 1)
                        WHEN a.level = '3' THEN LEFT(a.kdakun, 2)
                        WHEN a.level = '4' THEN LEFT(a.kdakun, 3)
                    END AS kdakun_parent
                FROM akun a
                WHERE a.status='0'
                  AND a.idperusahaan=?
            ),
            
            -- raw_trans: aggregate per full kdakun (so level4 totals available)
            raw_trans AS (
                SELECT
                    p.idperusahaan,
                    c.kdakun AS kdakun_full,            -- full kode akun (level4 leaf or other)
                    LEFT(c.kdakun,1) AS lvl1,
                    LEFT(c.kdakun,2) AS lvl2,
                    LEFT(c.kdakun,3) AS lvl3,
                    SUM(jd.debet) AS tdebet,
                    SUM(jd.kredit) AS tkredit
                FROM jurnaldetail jd
                JOIN jurnal j ON j.idjurnal = jd.idjurnal
                JOIN akun c ON c.keyakun = jd.keyakun
                JOIN pengguna p ON p.idpengguna = j.idpengguna
                WHERE c.status='0'
                  AND p.idperusahaan=?
                  AND j.tgljurnal BETWEEN ? AND ?
                GROUP BY c.kdakun, p.idperusahaan
            ),
            
            -- realisasi: sediakan semua level (lvl1..lvl4) sehingga dapat join tepat
            realisasi AS (
                SELECT
                    idperusahaan,
                    lvl1,
                    lvl2,
                    lvl3,
                    kdakun_full AS lvl4,
                    SUM(tdebet) AS total_debet,
                    SUM(tkredit) AS total_kredit
                FROM raw_trans
                GROUP BY idperusahaan, lvl1, lvl2, lvl3, kdakun_full
            )
            
            SELECT 
                ai.keyakun,
                ai.kdakun,
                ai.nmakun,
                ai.level,
                ai.saldonormal,
                ai.idperusahaan,
                ai.kdakun_index,
                ai.kdakun_parent,
                -- hitung sesuai saldonormal akun pada level itu
                SUM(
                    CASE ai.saldonormal
                        WHEN 'D' THEN COALESCE(r.total_debet,0) - COALESCE(r.total_kredit,0)
                        ELSE COALESCE(r.total_kredit,0) - COALESCE(r.total_debet,0)
                    END
                ) AS jumlah
            FROM akun_index ai
            LEFT JOIN realisasi r 
                   ON (ai.level = 1 AND r.lvl1 = ai.kdakun_index AND r.idperusahaan = ai.idperusahaan)
                   OR (ai.level = 2 AND r.lvl2 = ai.kdakun_index AND r.idperusahaan = ai.idperusahaan)
                   OR (ai.level = 3 AND r.lvl3 = ai.kdakun_index AND r.idperusahaan = ai.idperusahaan)
                   OR (ai.level = 4 AND r.lvl4 = ai.kdakun_index AND r.idperusahaan = ai.idperusahaan)
            GROUP BY 
                ai.keyakun, ai.kdakun, ai.nmakun, ai.level,
                ai.kdakun_index, ai.kdakun_parent, ai.saldonormal, ai.idperusahaan
            ORDER BY ai.kdakun ASC";
            return $this->db->query($query, [$idperusahaan, $idperusahaan, $tglawal, $tglakhir]);
    }

    public function get_lapkoreksifiskal($tglawal, $tglakhir, $idperusahaan, $level = '')
    {
        $query = "
            WITH akun_index AS (
                SELECT 
                    a.keyakun,
                    a.kdakun,
                    a.nmakun,
                    a.level,
                    a.saldonormal,
                    a.idperusahaan,
                    -- kdakun_index: gunakan full sesuai level (level4 = full kdakun)
                    CASE 
                        WHEN a.level = '1' THEN LEFT(a.kdakun, 1)
                        WHEN a.level = '2' THEN LEFT(a.kdakun, 2)
                        WHEN a.level = '3' THEN LEFT(a.kdakun, 3)
                        WHEN a.level = '4' THEN a.kdakun
                    END AS kdakun_index,
                    -- kdakun_parent: untuk level4 parent = prefix 3 (level3 kode)
                    CASE 
                        WHEN a.level = '1' THEN ''
                        WHEN a.level = '2' THEN LEFT(a.kdakun, 1)
                        WHEN a.level = '3' THEN LEFT(a.kdakun, 2)
                        WHEN a.level = '4' THEN LEFT(a.kdakun, 3)
                    END AS kdakun_parent
                FROM akun a
                WHERE a.status='0'
                  AND a.idperusahaan=?
                  $level
                  AND LEFT(a.kdakun,1) IN ('4','5','6', '7')
            ),
            
            -- raw_trans: aggregate per full kdakun (so level4 totals available)
            raw_trans AS (
                SELECT
                    p.idperusahaan,
                    c.kdakun AS kdakun_full,            -- full kode akun (level4 leaf or other)
                    LEFT(c.kdakun,1) AS lvl1,
                    LEFT(c.kdakun,2) AS lvl2,
                    LEFT(c.kdakun,3) AS lvl3,
                    SUM(jd.debet) AS tdebet,
                    SUM(jd.kredit) AS tkredit,
                    SUM(jd.koreksi_positif) AS tkoreksi_positif,
                    SUM(jd.koreksi_negatif) AS tkoreksi_negatif
                FROM jurnaldetail jd
                JOIN jurnal j ON j.idjurnal = jd.idjurnal
                JOIN akun c ON c.keyakun = jd.keyakun
                JOIN pengguna p ON p.idpengguna = j.idpengguna
                WHERE c.status='0'
                  AND p.idperusahaan=?
                  AND j.tgljurnal BETWEEN ? AND ?
                  AND jd.fiskal != '0' -- Filter tambahan untuk menampilkan yang fiskalnya selain 0
                GROUP BY c.kdakun, p.idperusahaan
            ),
            
            -- realisasi: sediakan semua level (lvl1..lvl4) sehingga dapat join tepat
            realisasi AS (
                SELECT
                    idperusahaan,
                    lvl1,
                    lvl2,
                    lvl3,
                    kdakun_full AS lvl4,
                    SUM(tdebet) AS total_debet,
                    SUM(tkredit) AS total_kredit,
                    SUM(tkoreksi_positif) AS total_koreksi_positif, -- TAMBAHAN
                    SUM(tkoreksi_negatif) AS total_koreksi_negatif  -- TAMBAHAN
                FROM raw_trans
                GROUP BY idperusahaan, lvl1, lvl2, lvl3, kdakun_full
            )
            
            SELECT 
                ai.keyakun,
                ai.kdakun,
                ai.nmakun,
                ai.level,
                ai.saldonormal,
                ai.idperusahaan,
                ai.kdakun_index,
                ai.kdakun_parent,
                -- hitung sesuai saldonormal akun pada level itu
                SUM(
                    CASE ai.saldonormal
                        WHEN 'D' THEN COALESCE(r.total_debet,0) - COALESCE(r.total_kredit,0)
                        ELSE COALESCE(r.total_kredit,0) - COALESCE(r.total_debet,0)
                    END
                ) AS jumlah,
                SUM(COALESCE(r.total_koreksi_positif, 0)) AS koreksi_positif, -- TAMBAHAN
                SUM(COALESCE(r.total_koreksi_negatif, 0)) AS koreksi_negatif  -- TAMBAHAN
            FROM akun_index ai
            LEFT JOIN realisasi r 
                   ON (ai.level = 1 AND r.lvl1 = ai.kdakun_index AND r.idperusahaan = ai.idperusahaan)
                   OR (ai.level = 2 AND r.lvl2 = ai.kdakun_index AND r.idperusahaan = ai.idperusahaan)
                   OR (ai.level = 3 AND r.lvl3 = ai.kdakun_index AND r.idperusahaan = ai.idperusahaan)
                   OR (ai.level = 4 AND r.lvl4 = ai.kdakun_index AND r.idperusahaan = ai.idperusahaan)
            GROUP BY 
                ai.keyakun, ai.kdakun, ai.nmakun, ai.level,
                ai.kdakun_index, ai.kdakun_parent, ai.saldonormal, ai.idperusahaan
            ORDER BY ai.kdakun ASC";
            
        return $this->db->query($query, [$idperusahaan, $idperusahaan, $tglawal, $tglakhir]);
    }

    public function get_lapobjekpajak($tglawal, $tglakhir, $idperusahaan, $level = '')
    {
        $query = "
            WITH akun_index AS (
                SELECT 
                    a.keyakun,
                    a.kdakun,
                    a.nmakun,
                    a.level,
                    a.saldonormal,
                    a.idperusahaan,
                    -- kdakun_index: gunakan full sesuai level (level4 = full kdakun)
                    CASE 
                        WHEN a.level = '1' THEN LEFT(a.kdakun, 1)
                        WHEN a.level = '2' THEN LEFT(a.kdakun, 2)
                        WHEN a.level = '3' THEN LEFT(a.kdakun, 3)
                        WHEN a.level = '4' THEN a.kdakun
                    END AS kdakun_index,
                    -- kdakun_parent: untuk level4 parent = prefix 3 (level3 kode)
                    CASE 
                        WHEN a.level = '1' THEN ''
                        WHEN a.level = '2' THEN LEFT(a.kdakun, 1)
                        WHEN a.level = '3' THEN LEFT(a.kdakun, 2)
                        WHEN a.level = '4' THEN LEFT(a.kdakun, 3)
                    END AS kdakun_parent
                FROM akun a
                WHERE a.status='0'
                  AND a.idperusahaan=?
                  $level
                  AND LEFT(a.kdakun,1) IN ('4','5','6', '7')
            ),
            
            -- raw_trans: aggregate per full kdakun (so level4 totals available)
            raw_trans AS (
                SELECT
                    p.idperusahaan,
                    c.kdakun AS kdakun_full,            -- full kode akun (level4 leaf or other)
                    LEFT(c.kdakun,1) AS lvl1,
                    LEFT(c.kdakun,2) AS lvl2,
                    LEFT(c.kdakun,3) AS lvl3,
                    SUM(jd.debet) AS tdebet,
                    SUM(jd.kredit) AS tkredit,
                    SUM(jd.objek_pajak) AS tobjek_pajak
                FROM jurnaldetail jd
                JOIN jurnal j ON j.idjurnal = jd.idjurnal
                JOIN akun c ON c.keyakun = jd.keyakun 
                JOIN pengguna p ON p.idpengguna = j.idpengguna
                WHERE c.status='0'
                  AND p.idperusahaan=?
                  AND j.tgljurnal BETWEEN ? AND ?
                  AND jd.objek != '0' -- Filter tambahan untuk menampilkan yang objeknya selain 0
                GROUP BY c.kdakun, p.idperusahaan
            ),
            
            -- realisasi: sediakan semua level (lvl1..lvl4) sehingga dapat join tepat
            realisasi AS (
                SELECT
                    idperusahaan,
                    lvl1,
                    lvl2,
                    lvl3,
                    kdakun_full AS lvl4,
                    SUM(tdebet) AS total_debet,
                    SUM(tkredit) AS total_kredit,
                    SUM(tobjek_pajak) AS total_objek_pajak  -- TAMBAHAN
                FROM raw_trans
                GROUP BY idperusahaan, lvl1, lvl2, lvl3, kdakun_full
            )
            
            SELECT 
                ai.keyakun,
                ai.kdakun,
                ai.nmakun,
                ai.level,
                ai.saldonormal,
                ai.idperusahaan,
                ai.kdakun_index,
                ai.kdakun_parent,
                -- hitung sesuai saldonormal akun pada level itu
                SUM(
                    CASE ai.saldonormal
                        WHEN 'D' THEN COALESCE(r.total_debet,0) - COALESCE(r.total_kredit,0)
                        ELSE COALESCE(r.total_kredit,0) - COALESCE(r.total_debet,0)
                    END
                ) AS jumlah,
                SUM(COALESCE(r.total_objek_pajak, 0)) AS objek_pajak  -- TAMBAHAN
            FROM akun_index ai
            LEFT JOIN realisasi r 
                   ON (ai.level = 1 AND r.lvl1 = ai.kdakun_index AND r.idperusahaan = ai.idperusahaan)
                   OR (ai.level = 2 AND r.lvl2 = ai.kdakun_index AND r.idperusahaan = ai.idperusahaan)
                   OR (ai.level = 3 AND r.lvl3 = ai.kdakun_index AND r.idperusahaan = ai.idperusahaan)
                   OR (ai.level = 4 AND r.lvl4 = ai.kdakun_index AND r.idperusahaan = ai.idperusahaan)
            GROUP BY 
                ai.keyakun, ai.kdakun, ai.nmakun, ai.level,
                ai.kdakun_index, ai.kdakun_parent, ai.saldonormal, ai.idperusahaan
            ORDER BY ai.kdakun ASC";
            
        return $this->db->query($query, [$idperusahaan, $idperusahaan, $tglawal, $tglakhir]);
    }


    public function get_fetch($tglawal, $tglakhir, $idperusahaan, $limit, $start)
    {
        $query = "select v_jurnaldetail.* from v_jurnaldetail 
				where tgljurnal between '" . $tglawal . "' and '" . $tglakhir . "' and idperusahaan='" . $idperusahaan . "'  order by tgljurnal desc, tglinsert asc LIMIT $limit OFFSET $start";
        return $this->db->query($query);
    }

    public function get_totalFetch($tglawal, $tglakhir, $idperusahaan)
    {
        $query = "select sum(debet) as debet, sum(kredit) as kredit from v_jurnaldetail 
				where tgljurnal between '" . $tglawal . "' and '" . $tglakhir . "' and idperusahaan='" . $idperusahaan . "'";
        return $this->db->query($query);
    }

    public function get_fetchBukubesar($tglawal, $tglakhir, $kdakun = '', $idp, $asc = 'desc', $limit, $start)
    {
        $idperusahaan = decrypt($idp);
        if ($kdakun)
            $query = "select *, UNIX_TIMESTAMP(tglinsert) as intTgl from v_jurnaldetail where kdakun='" . $kdakun . "' and tgljurnal between '" . $tglawal . "' and '" . $tglakhir . "' and md5(idperusahaan)='" . $idperusahaan . "' order by tgljurnal $asc, intTgl $asc  LIMIT $limit OFFSET $start";
        else
            $query = "select *, UNIX_TIMESTAMP(tglinsert) as intTgl from v_jurnaldetail where tgljurnal between '" . $tglawal . "' and '" . $tglakhir . "' and md5(idperusahaan)='" . $idperusahaan . "' order by tgljurnal $asc, intTgl $asc LIMIT $limit OFFSET $start";
        return $this->db->query($query);
    }

    public function get_totalFetchBukubesar($tglawal, $tglakhir, $kdakun = '', $idperusahaan)
    {
        if ($kdakun)
            $query = "select sum(debet) as debet, sum(kredit) as kredit from v_jurnaldetail where kdakun='" . $kdakun . "' and tgljurnal between '" . $tglawal . "' and '" . $tglakhir . "' and idperusahaan='" . $idperusahaan . "'";
        else
            $query = "select sum(debet) as debet, sum(kredit) as kredit from v_jurnaldetail where tgljurnal between '" . $tglawal . "' and '" . $tglakhir . "' and idperusahaan='" . $idperusahaan . "'";
        return $this->db->query($query);
    }
    
    public function tglindonesialengkap($tanggal)
    {
        $ntgl = date('d', strtotime($tanggal));
        $nbln = date('m', strtotime($tanggal));
        $nthn = date('Y', strtotime($tanggal));

        switch ($nbln) {
            case '01':
                $cBln = 'Januari';
                break;
            case '02':
                $cBln = 'Februari';
                break;
            case '03':
                $cBln = 'Maret';
                break;
            case '04':
                $cBln = 'April';
                break;
            case '05':
                $cBln = 'Mei';
                break;
            case '06':
                $cBln = 'Juni';
                break;
            case '07':
                $cBln = 'Juli';
                break;
            case '08':
                $cBln = 'Agustus';
                break;
            case '09':
                $cBln = 'September';
                break;
            case '10':
                $cBln = 'Oktober';
                break;
            case '11':
                $cBln = 'November';
                break;
            default:
                $cBln = 'Desember';
                break;
        }

        return $ntgl . ' ' . $cBln . ' ' . $nthn;
    }
}
