<?php
function cek_langganan()
{
    $db      = \Config\Database::connect();
    $builder = $db->table('perusahaan_langganan');
    $hasil =  $builder->where(array('status' => 'B', 'tgl_akhir <' => date('Y-m-d')))->get()->getResult();
    if ($hasil) {
        foreach ($hasil as $rows) {
            $data = array(
                'status' => 'S'
            );
            $builder1 = $db->table('perusahaan_langganan');
            $builder1->where(array('idpl' => $rows->idpl));
            $builder1->update($data);
        }
    }

    //untuk memberikan kondisi langganan atau tidaknya
    $builder1 = $db->table('perusahaan_langganan');
    $hasil1 =  $builder1->where(array('status' => 'B', 'idlangganan' => 1, 'idperusahaan' => session()->get('idperusahaan')))->get()->getRow();

    $builder2 = $db->table('perusahaan_langganan');
    $hasil2 =  $builder2->where(array('status' => 'B', 'tgl_akhir >=' => date('Y-m-d'), 'idperusahaan' => session()->get('idperusahaan')))->get()->getRow();


    if ($hasil1) {
        $data1 = array(
            'hitPengguna' => 2,
            'hitAkun' => 500,
            'hitJurnal' => 500,
            'hitLanggana' => false,
            'hitAlert' => 'free',
            'hitFileJurnal' => false,
        );
        session()->set($data1);
        return true;
    } elseif ($hasil2) {
        $data1 = array(
            'hitPengguna' => 1000000,
            'hitAkun' => 1000000,
            'hitJurnal' => 10000000,
            'hitLanggana' => true,
            'hitAlert' => 'aktif',
            'hitFileJurnal' => true,
        );
        session()->set($data1);
        return false;
    } else {
        $data1 = array(
            'hitPengguna' => 2,
            'hitAkun' => 500,
            'hitJurnal' => 500,
            'hitLanggana' => true,
            'hitAlert' => 'tidak',
            'hitFileJurnal' => false,
        );
        session()->set($data1);
        return true;
    }
}
function cek_pengguna()
{
    $db      = \Config\Database::connect();
    $builder = $db->table('pengguna');
    $builder->where('idperusahaan', session()->get('idperusahaan'));
    $hasil =  $builder->countAllResults();
    $data = array(
        'databaseHitPengguna' => $hasil,
    );
    session()->set($data);
}

function cek_akun()
{
    $db      = \Config\Database::connect();
    $builder = $db->table('akun');
    $builder->where('idperusahaan', session()->get('idperusahaan'));
    $hasil =  $builder->countAllResults();
    $data = array(
        'databaseHitAkun' => $hasil,
    );
    session()->set($data);
}
function cek_jurnal()
{
    $db      = \Config\Database::connect();
    $builder = $db->table('jurnal a');
    $builder->join('pengguna b', 'a.idpengguna = b.idpengguna');
    $builder->where('b.idperusahaan', session()->get('idperusahaan'));
    $hasil =  $builder->countAllResults();
    $data = array(
        'databaseHitJurnal' => $hasil,
    );
    session()->set($data);
}


if (!function_exists('active_link')) {
    function activate_menu($controller, $menu)
    {
        return ($menu == $controller) ? 'active' : '';
    }
}

function encrypt($data)
{
    $md5 = md5($data);
    return rtrim(strtr(base64_encode($md5), '+/', '-_'), '=');
}

function decrypt($data)
{
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}


function img_lazy(string $src, string $alt = '', array $attrs = []): string
{
    $filePath = FCPATH . ltrim($src, '/');

        $width = $attrs['width'] ?? null;
        $height = $attrs['height'] ?? null;

        // deteksi ukuran asli jika ada di server
        if (file_exists($filePath) && (empty($width) || empty($height))) {
            $size = @getimagesize($filePath);
            if ($size) {
                $width = $width ?? $size[0];
                $height = $height ?? $size[1];
            }
        }

        // fallback jika ukuran tidak ketemu
        $width = $width ?: 200;
        $height = $height ?: 150;

        // placeholder shimmer animasi (SVG)
        $placeholder = 'data:image/svg+xml;utf8,' . rawurlencode('
        <svg xmlns="http://www.w3.org/2000/svg" width="'.$width.'" height="'.$height.'" preserveAspectRatio="none">
          <rect width="100%" height="100%" fill="#eeeeee"/>
          <rect width="100%" height="100%" fill="url(#g)">
            <animate attributeName="x" from="-'.$width.'" to="'.$width.'" dur="1.2s" repeatCount="indefinite" />
          </rect>
          <defs>
            <linearGradient id="g">
              <stop stop-color="#eeeeee" offset="20%" />
              <stop stop-color="#dddddd" offset="50%" />
              <stop stop-color="#eeeeee" offset="70%" />
            </linearGradient>
          </defs>
        </svg>');
            
    $default = [
        'src'      => $placeholder, // placeholder
        'data-src' => base_url($src),
        'alt'      => $alt,
        'class'    => 'lazy',
        'loading'  => 'lazy'
    ];

    // jika user kasih class tambahan → gabungkan dengan default
    if (isset($attrs['class'])) {
        $attrs['class'] = $default['class'] . ' ' . $attrs['class'];
        unset($default['class']);
    }

    $allAttrs = array_merge($default, $attrs);

    $attrString = '';
    foreach ($allAttrs as $key => $val) {
        $attrString .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($val) . '"';
    }

    return '<img' . $attrString . '>';
}

function ringkas_teks($text, $limit = 10)
{
    // jadikan huruf kecil + bersihkan HTML
    $text = mb_strtolower(strip_tags($text), 'UTF-8');

    $words = preg_split('/\s+/', $text);

    if (count($words) <= $limit) {
        return [
            'short' => $text,
            'full'  => ''
        ];
    }

    return [
        'short' => implode(' ', array_slice($words, 0, $limit)),
        'full'  => $text
    ];
}

