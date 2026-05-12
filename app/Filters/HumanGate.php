<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class HumanGate implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $uri     = service('uri');
        $path    = $uri->getPath();
        $ua      = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');

        // ✅ Whitelist bot agar meta tag terbaca (SEO & social share)
        $allowedBots = [
            'facebookexternalhit',
            'twitterbot',
            'linkedinbot',
            'slackbot',
            'whatsapp',
            'googlebot',
            'bingbot',
            'duckduckbot',
            'yandexbot'
        ];

        foreach ($allowedBots as $bot) {
            if (strpos($ua, $bot) !== false) {
                return; // biarkan bot akses tanpa captcha
            }
        }

        // ✅ Whitelist path penting (captcha, asset, robots, sitemap)
        if (
            $session->get('human_verified') === true ||
            strpos($path, 'captcha') === 0 ||
            strpos($path, 'assets') === 0 ||
            $path === 'robots.txt' ||
            $path === 'sitemap.xml'
        ) {
            return;
        }

        // ✅ Redirect ke gate untuk user manusia
        return redirect()->to(
            site_url('captcha/gate?next=' . urlencode(current_url()))
        );
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu aksi apapun setelah
    }
}
