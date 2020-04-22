<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class WC_Datacap_API_Response
 */
class WC_Datacap_API_Response
{
    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var array
     */
    protected $headers;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var array
     */
    protected $decodedBody;

    /**
     * WC_Datacap_API_Response constructor.
     * @param array $response
     */
    public function __construct($response)
    {
        /** @var Requests_Utility_CaseInsensitiveDictionary $headers */
        $headers = $response['headers'];

        $this->setHeaders($headers->getAll());
        $this->setBody($response['body']);
        $this->setStatusCode($response['response']['code']);
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param string $header
     * @return string
     */
    public function getHeader($header)
    {
        return $this->headers[$header];
    }

    /**
     * @return string
     */
    public function getBodyAsString()
    {
        return $this->body;
    }

    /**
     * @return array|string
     */
    public function getBody()
    {
        return $this->getDecodedBody() !== null ? $this->getDecodedBody() : $this->__toString();
    }

    /**
     * @return array
     */
    public function getDecodedBody()
    {
        return $this->decodedBody;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @param $body
     */
    public function setBody($body)
    {
        $this->body = $body;
        $this->decodedBody = json_decode($body, true);
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasBodyKey($key)
    {
        return isset($this->decodedBody[$key]);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getBodyKey($key)
    {
        return ($this->hasBodyKey($key) ? $this->decodedBody[$key] : null);
    }

    /**
     * @param $functionName
     * @param $arguments
     * @return bool|null
     */
    public function __call($functionName, $arguments)
    {
        $prefix = substr($functionName, 0, 3);
        $property = substr($functionName, 3);

        if (!$property) {
            trigger_error("Call to undefined method " . $functionName . " on " . get_class($this), E_USER_ERROR);
            return null;
        }

        if ($prefix === 'get') {
            return $this->getBodyKey($property);
        }

        if ($prefix === 'has') {
            return $this->getBodyKey($property) !== null;
        }

        trigger_error("Call to undefined method " . $functionName . " on " . get_class($this), E_USER_ERROR);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->body;
    }
}