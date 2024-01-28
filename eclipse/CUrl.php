<?php

namespace Eclipse;

class CUrl
{
    private $curl = null;
    private $headers = [];
    private $options = [];
    private $mobile = false;
    private $json_data = false;
    private $userAgent = '';
    private $cookie = null;
    private $UA = [
        'browser' => [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.71 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:96.0) Gecko/20100101 Firefox/96.0',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36 Edg/96.0.1054.43',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.2 Safari/605.1.15',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36 OPR/83.0.4254.57'
        ],
        'mobile' => [
            'Mozilla/5.0 (iPhone; CPU iPhone OS 15_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.2 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Linux; Android 11; SM-G991U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.152 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 12; Pixel 6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.101 Mobile Safari/537.36',
            'Mozilla/5.0 (iPad; CPU OS 15_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.2 Mobile/15E148 Safari/604.1',
        ]
    ];

    public function json_data()
    {
        $this->json_data = true;
    }

    public function mobile()
    {
        $this->mobile = true;
    }

    public function json()
    {
        $this->headers[] = 'Accept: application/json; charset=utf-8';
        if ($this->json_data) {
            $this->headers[] = 'Content-Type: application/json';
        } else {
            $this->headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=utf-8';
        }
    }

    public function setCookie($cookie) {
        $this->cookie = $cookie;
    }

    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    public function setOpt($key, $value)
    {
        $this->options[$key] = $value;
    }

    public function setHeader($header)
    {
        $this->headers[] = $header;
    }

    public function connect($method, $url, $data = null)
    {
        $this->curl = curl_init();

        curl_setopt($this->curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($this->curl, CURLOPT_URL, $url);
        if ($this->cookie) {
            curl_setopt($this->curl, CURLOPT_COOKIE, $this->cookie);
        }
        $this->userAgent();
        $this->detectSSL($url);
        if ($data) {
            $this->setHeader('Content-Length: ' . strlen(json_encode($data)));
        }
        $this->setHeaders();
        $this->setOptions();

        if ($data) {
            if ($this->json_data) {
                $data = json_encode($data);
            } else {
                $data = http_build_query($data);
            }
        }
        
        switch ($method) {
            case 'GET':
                break;
            case 'POST':
                curl_setopt($this->curl, CURLOPT_POST, true);
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
                break;
            case 'PUT':
                curl_setopt($this->curl, CURLOPT_PUT, true);
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
                break;
            case 'PATCH':
                curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
                break;
            case 'DELETE':
                curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
                break;
        }

        $response = curl_exec($this->curl);
        curl_close($this->curl);
        return $response;
    }

    public function reset()
    {
        $this->mobile = false;
        $this->json_data = false;
        $this->userAgent = '';
        $this->resetOpts();
        $this->resetHeaders();
    }

    private function detectSSL($url)
    {
        if (strpos($url, 'https://') !== false) {
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 2);
        }
    }

    private function setOptions()
    {
        if (count($this->options) > 0) {
            foreach ($this->options as $key => $value) {
                curl_setopt($this->curl, $key, $value);
            }
        }
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
    }

    private function setHeaders()
    {
        if (count($this->headers) > 0) {
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
        }
    }

    private function userAgent()
    {
        if (! $this->userAgent) {
            if ($this->mobile) {
                $this->userAgent = $this->UA['mobile'][rand(0, count($this->UA['mobile']) - 1)];
            } else {
                $this->userAgent = $this->UA['browser'][rand(0, count($this->UA['browser']) - 1)];
            }
        }
        curl_setopt($this->curl, CURLOPT_USERAGENT, $this->userAgent);
    }

    private function resetOpts()
    {
        $this->options = [];
    }

    private function resetHeaders()
    {
        $this->headers = [];
    }
}
