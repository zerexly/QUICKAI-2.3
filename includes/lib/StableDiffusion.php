<?php

class StableDiffusion
{
    private string $engine = "stable-diffusion-512-v2-1";
    private array $headers;
    private array $contentTypes;
    private int $timeout = 0;
    private string $customUrl = "";
    private string $proxy = "";
    private array $curlInfo = [];
    public const BASE_URL = 'https://api.stability.ai/v1/generation/';

    public function __construct($API_KEY)
    {
        $this->contentTypes = [
            "application/json" => "Content-Type: application/json",
            "multipart/form-data" => "Content-Type: multipart/form-data",
        ];

        $this->headers = [
            $this->contentTypes["application/json"],
            "Authorization: Bearer $API_KEY",
        ];

        // setup proxy
        if($proxy = get_api_proxy()){
            $this->setProxy($proxy);
        }
    }

    /**
     * @param $opts
     * @return bool|string
     */
    public function image($opts)
    {
        $url = self::BASE_URL . $this->engine . '/text-to-image';

        return $this->sendRequest($url, 'POST', $opts);
    }

    /**
     * @return bool|string
     */
    public function listEngines()
    {
        $url = 'https://api.stability.ai/v1/engines/list';

        return $this->sendRequest($url, 'GET');
    }

    /**
     * @param  string  $proxy
     */
    public function setProxy(string $proxy)
    {
        if ($proxy && strpos($proxy, '://') === false) {
            $proxy = 'https://'.$proxy;
        }
        $this->proxy = $proxy;
    }

    /**
     * @return array
     */
    public function getCURLInfo()
    {
        return $this->curlInfo;
    }

    /**
     * @param  string  $url
     * @param  string  $method
     * @param  array   $opts
     * @return bool|string
     */
    private function sendRequest(string $url, string $method, array $opts = [])
    {
        $post_fields = json_encode($opts);

        if (array_key_exists('file', $opts) || array_key_exists('image', $opts)) {
            $this->headers[0] = $this->contentTypes["multipart/form-data"];
            $post_fields      = $opts;
        } else {
            $this->headers[0] = $this->contentTypes["application/json"];
        }
        $curl_info = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_POSTFIELDS     => $post_fields,
            CURLOPT_HTTPHEADER     => $this->headers,
        ];

        if ($opts == []) {
            unset($curl_info[CURLOPT_POSTFIELDS]);
        }

        if (!empty($this->proxy)) {
            $curl_info[CURLOPT_PROXY] = $this->proxy;
        }

        $curl = curl_init();

        curl_setopt_array($curl, $curl_info);
        $response = curl_exec($curl);

        $info           = curl_getinfo($curl);
        $this->curlInfo = $info;

        curl_close($curl);

        return $response;
    }

}