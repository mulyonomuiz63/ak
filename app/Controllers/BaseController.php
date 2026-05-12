<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\Akun_model;
use App\Models\Jurnal_model;
use App\Models\Perusahaan_model;
use App\Models\Laporan_model;
use App\Models\Login_model;
use App\Models\Pengguna_model;
use App\Models\Login_model_affiliator;
use App\Models\Marketer_model;
use App\Models\Diskon_model;
use App\Models\Dokumen_model;
use App\Models\Komisi_model;
use App\Models\Harga_model;
use App\Models\Histori_model;
use App\Models\KomisiMarketer_model;
use App\Models\Iklan_model;
use App\Models\Event_model;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['form', 'file', 'url', 'html', 'download', 'm_helper'];
    protected $akun_model;
    protected $jurnal_model;
    protected $perusahaan_model;
    protected $laporan_model;
    protected $login_model;
    protected $pengguna_model;
    protected $login_model_affiliator;
    protected $marketer_model;
    protected $diskon_model;
    protected $komisi_model;
    protected $harga_model;
    protected $histori_model;
    protected $komisimarketer_model;
    protected $dokumen_model;
    protected $iklan_model;
    protected $event_model;

    protected $session;
    protected $db;
    protected $encrypt;
    protected $email;
    protected $image;
    protected $uri;
    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        //preload library
        $config = new \Config\Encryption();
        $config->key = 'M1pT3zx500uYVodaysN68IiNYhV0KdCb';
        $config->driver = 'OpenSSL';
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
        $this->encrypt = \Config\Services::encrypter($config);
        $this->email = \Config\Services::email();
        $this->image = \Config\Services::image();
        $this->uri = service('uri');

        //preload model
        $this->akun_model = new Akun_model();
        $this->jurnal_model = new Jurnal_model();
        $this->perusahaan_model = new Perusahaan_model();
        $this->laporan_model = new Laporan_model();
        $this->login_model = new Login_model();
        $this->pengguna_model = new Pengguna_model();
        $this->login_model_affiliator = new Login_model_affiliator();
        $this->marketer_model = new marketer_model();
        $this->diskon_model = new Diskon_model();
        $this->komisi_model = new Komisi_model();
        $this->harga_model = new Harga_model();
        $this->histori_model = new Histori_model();
        $this->komisimarketer_model = new KomisiMarketer_model();
        $this->dokumen_model = new Dokumen_model();
        $this->iklan_model = new Iklan_model();
        $this->event_model = new Event_model();
    }
}
