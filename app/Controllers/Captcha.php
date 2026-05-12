<?php namespace App\Controllers;

class Captcha extends BaseController
{
    public function gate()
    {
        return view('captcha_gate', [
            'siteKey' => getenv('TURNSTILE_SITE_KEY'),
            'next'    => $this->request->getGet('next') ?? site_url('/'),
        ]);
    }

    public function verify()
    {
        $token = $this->request->getPost('cf-turnstile-response');
        $secret = getenv('TURNSTILE_SECRET_KEY');
        $next = $this->request->getPost('next') ?? site_url('/');

        if (!$token) return redirect()->back()->with('error', 'Token kosong');

        // Verifikasi ke Cloudflare
        $ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $this->request->getIPAddress(),
            ]),
            CURLOPT_TIMEOUT => 10,
        ]);
        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) return redirect()->back()->with('error', 'Verifikasi gagal: '.$err);

        $data = json_decode($result, true);
        if (!empty($data['success'])) {
            session()->set('human_verified', true);
            return redirect()->to($next);
        }

        return redirect()->back()->with('error', 'Captcha tidak valid');
    }
}
