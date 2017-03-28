<?php

class SiftRequest {
    const GET = 'GET';
    const POST = 'POST';
    const DELETE = 'DELETE';

    private static $mock = null;

    private $url;
    private $method;
    private $timeout;
    private $version;
    private $body;
    private $params;
    private $auth;

    /**
     * SiftRequest constructor
     *
     * @param string $url  Url for the HTTP request
     * @param string $method  Method for the HTTP request
     * @param int $timeout  Request timeout
     * @param string $version  Version of the Sift Science API that is being called.
     * @param array $opts  Array of optional parameters for this request --
     *     - array 'params': URL query parameters for the request.
     *     - array 'body': HTTP body for the request.
     *     - string 'auth': Basic authorization for the request (i.e., "username:password").
     */
    function __construct($url, $method, $timeout, $version, $opts = array()) {
        $opts += array(
            'params' => array(),
            'body' => array(),
            'auth' => null
        );

        $this->url = $url;
        $this->method = $method;
        $this->timeout = $timeout;
        $this->version = $version;
        $this->body = $opts['body'];
        $this->params = $opts['params'];
        $this->auth = $opts['auth'];
    }

    /**
     * Send the HTTP request via cURL
     *
     * @return SiftResponse
     */
    public function send() {
        $curlUrl = $this->url;
        if ($this->params) {
            $queryString = http_build_query($this->params);
            $separator = parse_url($curlUrl, PHP_URL_QUERY) ? '&' : '?';
            $curlUrl .= $separator . $queryString;
        }

        // Mock the request if self::$mock exists
        if (self::$mock) {
            if (self::$mock['url'] == $curlUrl && self::$mock['method'] == $this->method) {
                return self::$mock['response'];
            }
            return null;
        }

        // Open and configure curl connection
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $curlUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        $headers = array(
            'User-Agent: SiftScience/v' . $this->version . ' sift-php/' . Sift::VERSION
        );
        if ($this->auth) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->auth);
        }

        // HTTP-method-specific configuration.
        if ($this->method == self::POST) {
            if (function_exists('json_encode')) {
                $jsonString = json_encode($this->body);
            } else {
                require_once(dirname(__FILE__) . '/Services_JSON-1.0.3/JSON.php');
                $json = new Services_JSON();
                $jsonString = $json->encodeUnsafe($this->body);
            }

            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonString);
            array_push($headers, 'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonString)
            );

        } else if ($this->method == self::DELETE) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        }

        // Send the request using curl and parse result
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close the curl connection
        curl_close($ch);

        return new SiftResponse($result, $httpStatusCode, $this);
    }

    public static function setMockResponse($url, $method, $response) {
        self::$mock = array(
            'url' => $url,
            'method' => $method,
            'response' => $response
        );
    }

    public static function clearMockResponse() {
        self::$mock = null;
    }
}
