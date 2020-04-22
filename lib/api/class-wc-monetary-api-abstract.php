<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class WC_Monetary_API_Abstract
 */
abstract class WC_Monetary_API_Abstract
{
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const CONTENT_TYPE_JSON = 'application/json';
    const CONTENT_TYPE_XML = 'application/xml';
    const CONTENT_TYPE_FORM_ENCODE = 'application/x-www-form-encoded';

    /**
     * @var string
     */
    protected $uri = '';

    /**
     * @var string
     */
    protected $method = '';

    /**
     * @var array
     */
    protected $body = array();

    /**
     * @var array
     */
    protected $headers = array();

    /**
     * @var array
     */
    protected $query = array();

    /**
     * @var array
     */
    protected $urlParameters = array();

    /**
     * @var string
     */
    protected $responseClass = '';

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @param array $params
     * @return string
     */
    public function getUri($params = array())
    {
        return $this->buildUri($this->uri, array_merge($this->getUrlParameters() ?: array(), $params));
    }

    /**
     * @param $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * @param string $uri
     * @param array $params
     * @return string
     */
    protected function buildUri($uri, $params = array())
    {
        foreach ($params as $key => $value) {
            // replace possible variables in url
            $uri = str_replace('{' . $key . '}', $value, $uri);
        }

        return $uri;
    }

    /**
     * @return array
     */
    public function getUrlParameters()
    {
        return $this->urlParameters;
    }

    /**
     * @param array $urlParameters
     */
    public function setUrlParameters($urlParameters)
    {
        $this->urlParameters = $urlParameters;
    }

    /**
     * @param $key
     * @param $value
     */
    public function addUrlParameter($key, $value)
    {
        $this->urlParameters[$key] = $value;
    }

    /**
     * @param $key
     */
    public function removeUrlParameter($key)
    {
        unset($this->urlParameters[$key]);
    }

    /**
     * @return array
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param array $parameters
     */
    public function setQuery($parameters)
    {
        $this->query = $parameters;
    }

    /**
     * @param $param
     * @param $value
     */
    public function addQuery($param, $value)
    {
        $this->query[$param] = $value;
    }

    /**
     * @param $param
     */
    public function removeQuery($param)
    {
        unset($this->query[$param]);
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        // If it's a string, assume it's a query string. Parse it out and put it in the body.
        if (is_string($body)) {
            parse_str($body, $this->body);
            return;
        }

        $this->body = $body;
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasBodyKey($key)
    {
        return isset($this->body[$key]);
    }

    /**
     * @param $key
     * @return null
     */
    public function getBodyKey($key)
    {
        return ($this->hasBodyKey($key) ? $this->body[$key] : null);
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setBodyKey($key, $value)
    {
         $this->body[$key] = $value;
         return $this;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     * @return void
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @param string $name name, ex. "Location"
     * @param string $value value ex. "http://google.com"
     * @return void
     */
    public function addHeader($name, $value)
    {
        $this->headers[$name] = $value;
    }

    /**
     * @return string
     */
    public function getResponseClass()
    {
        return $this->responseClass;
    }

    /**
     * @param $class
     */
    public function setResponseClass($class)
    {
        $this->responseClass = $class;
    }

    /**
     * @param string $name
     * @return void
     */
    public function removeHeader($name)
    {
        unset($this->headers[$name]);
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

        if ($prefix === 'set') {
            $this->setBodyKey($property, $arguments[0]);
            return null;
        }

        if ($prefix === 'has') {
            return $this->getBodyKey($property) !== null;
        }
    }
}