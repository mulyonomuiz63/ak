<?php
namespace App\Libraries;


class PDFS extends TCPDF
{
    function __construct() {
    	include_once APPPATH . '/ThirdParty/TCPDF-6.3.5/tcpdf.php';
    	include_once APPPATH . '/ThirdParty/fpdi/src/autoload.php';
    	include_once APPPATH . '/ThirdParty/fpdf/fpdf.php';
    }
}
