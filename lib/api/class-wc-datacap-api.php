<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_DATACAP_PLUGIN_DIR . "/lib/api/class-wc-datacap-api-response.php");

/**
 * Class WC_Datacap_API
 */
class WC_Datacap_API
{
	const CERT_ENDPOINT = 'https://pay-cert.dcap.com/';
    const PROD_ENDPOINT = 'https://pay.dcap.com/';
    const CERT_REPORTS_ENDPOINT = 'https://reporting-cert.dcap.com';
    const PROD_REPORTS_ENDPOINT = 'https://reporting.dcap.com';

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
	protected $payApiVersion = 'v1';

	/**
	 * @var string
	 */
	protected $payApiv2Key = '';


    /**
     * @var string
     */
    protected $userAgent = 'Datacap WooCommerce Module v' . WC_DATACAP_VERSION;

    /**
     * @param bool $isSandbox
     * @return $this
     */
    public function setIsSandbox($isSandbox, $payApiVersion)
    {
        $this->isSandbox = $isSandbox;
        $this->endpoint = ($this->isSandbox ? self::CERT_ENDPOINT : self::PROD_ENDPOINT) . $payApiVersion;
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
	 * @param string $version
	 */
	public function setPayApiVersion($version)
	{
		$this->payApiVersion = $version;
	}

	/**
	 * @param string $key
	 */
	public function setPayApiV2Key($key)
	{
		$this->payApiv2Key = $key;
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
     * @param WC_Datacap_API_Abstract $request
     * @param array $response
     * @return WC_Datacap_API_Response
     */
    public function createResponse($request, $response)
    {
        if (!($responseClass = $request->getResponseClass())) {
            $responseClass = 'WC_Datacap_API_Response';
        }

        return new $responseClass($response);
    }

    /**
     * @param WC_Datacap_API_Abstract $request
     * @param array $params
     * @return string
     */
    protected function getUrl($request, $params = array())
    {
        /** @var WC_Datacap_API_Abstract $request */
        if ($request instanceof WC_Datacap_API_Reports_Abstract) {
            return ($this->isSandbox ? self::CERT_REPORTS_ENDPOINT : self::PROD_REPORTS_ENDPOINT) . $request->getUri($params);
        }
	    return ($this->isSandbox ? self::CERT_ENDPOINT : self::PROD_ENDPOINT) . $this->payApiVersion . $request->getUri($params);
    }

    /**
     * @param $request
     * @param array $params
     * @return WC_Datacap_API_Response
     */
    public function send($request, $params = array())
    {
        /** @var WC_Datacap_API_Abstract $request */

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
		if ($this->payApiVersion === "v2") {
			$authString = $this->secretKey . ':' . $this->payApiv2Key;
			$headerValue = 'Basic ' . base64_encode($authString);
			$headers['Authorization'] = $headerValue;
		}
		else {
			$headers['Authorization'] = $this->secretKey;
		}

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