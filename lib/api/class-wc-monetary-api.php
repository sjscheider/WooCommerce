<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_MONETARY_PLUGIN_DIR . "/lib/api/class-wc-monetary-api-response.php");

/**
 * Class WC_Monetary_API
 */
class WC_Monetary_API
{
    const CERT_ENDPOINT = 'https://pay-cert.monetary.co/v1';
    const PROD_ENDPOINT = 'https://pay.monetary.co/v1';
    const CERT_REPORTS_ENDPOINT = 'https://reporting-cert.monetary.co';
    const PROD_REPORTS_ENDPOINT = 'https://reporting.monetary.co';

    /**
     * @var string
     */
    protected $endpoint = '';

    /**
     * @var array
     */
    protected $headers = array();

    /**
     * @var bool
     */
    protected $isSandbox = false;

    /**
     * @var string
     */
    protected $publicKey;

    /**
     * @var string
     */
    protected $secretKey;

    /**
     * @var string
     */
    protected $userAgent = 'Monetary WooCommerce Module v' . WC_MONETARY_VERSION;

    /**
     * @param bool $isSandbox
     * @return $this
     */
    public function setIsSandbox($isSandbox)
    {
        $this->isSandbox = $isSandbox;
        $this->endpoint = $this->isSandbox ? self::CERT_ENDPOINT : self::PROD_ENDPOINT;
        return $this;
    }

    /**
     * @param string $key
     */
    public function setPublicKey($key)
    {
        $this->publicKey = $key;
    }

    /**
     * @param string $key
     */
    public function setSecretKey($key)
    {
        $this->secretKey = $key;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param $agent
     */
    public function setUserAgent($agent)
    {
        $this->userAgent = $agent;
    }

    /**
     * @param WC_Monetary_API_Abstract $request
     * @param array $response
     * @return WC_Monetary_API_Response
     */
    public function createResponse($request, $response)
    {
        if (!($responseClass = $request->getResponseClass())) {
            $responseClass = 'WC_Monetary_API_Response';
        }

        return new $responseClass($response);
    }

    /**
     * @param WC_Monetary_API_Abstract $request
     * @param array $params
     * @return string
     */
    protected function getUrl($request, $params = array())
    {
        /** @var WC_Monetary_API_Abstract $request */
        if ($request instanceof WC_Monetary_API_Reports_Abstract) {
            return ($this->isSandbox ? self::CERT_REPORTS_ENDPOINT : self::PROD_REPORTS_ENDPOINT) . $request->getUri($params);
        }

        return ($this->isSandbox ? self::CERT_ENDPOINT : self::PROD_ENDPOINT) . $request->getUri($params);
    }

    /**
     * @param $request
     * @param array $params
     * @return WC_Monetary_API_Response
     */
    public function send($request, $params = array())
    {
        /** @var WC_Monetary_API_Abstract $request */

        $url = $this->getUrl($request, $params);
        $method = $request->getMethod();
        $headers = $request->getHeaders();
        $body = $request->getBody();
        $getParameters = $request->getQuery();

        if (!empty($getParameters)) {
            $params = "";

            foreach ($getParameters as $key => $val) {
                $params .= $key . "=" . $val;
            }

            $url .= "?" . $params;
        }

        $headers['Accept'] = $request::CONTENT_TYPE_JSON;
        $headers['Content-Type'] = $request::CONTENT_TYPE_JSON;
        $headers['Authorization'] = $this->secretKey;
        $headers['User-Agent'] = $this->getUserAgent();

        $curlOptions = array(
            'method' => $method,
            'headers' => $headers,
            'timeout' => 60
        );

        if (in_array($request->getMethod(), array($request::POST, $request::PUT, $request::DELETE))) {
            $curlOptions['body'] = json_encode($body ?: new stdClass());
        }

        $http = _wp_http_get_object();
        $response = $http->request($url, $curlOptions);

        return $this->createResponse($request, $response);
    }
}