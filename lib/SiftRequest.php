<?php

class SiftRequest {
    const GET = 'GET';
    const POST = 'POST';
    const DELETE = 'DELETE';

    private static $mock = null;

    private $url;
    private $method;
    private $properties;
    private $timeout;

    /**
     * SiftRequest constructor
     *
     * @param $url Url of the HTTP request
     * @param $method Method of the HTTP request
     * @param $properties Parameters to send along with the request
     * @param $timeout HTTP request timeout
     */
    function __construct($url, $method, $properties, $timeout) {
        $this->url = $url;
        $this->method = $method;
        $this->properties = $properties;
        $this->timeout = $timeout;
    }

    /**
     * Send the HTTP request via cURL
     *
     * @return SiftResponse
     */
    public function send() {
        $propertiesString = http_build_query($this->properties);
        $curlUrl = $this->url;
        if ($this->method == self::GET || $this->method == self::DELETE) $curlUrl .= '?' . $propertiesString;

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
        if ($this->method == self::POST) {
            if (function_exists('json_encode')) {
                $jsonString = json_encode($this->properties);
            } else {
                require_once(dirname(__FILE__) . '/Services_JSON-1.0.3/JSON.php');
                $json = new Services_JSON();
                $jsonString = $json->encodeUnsafe($this->properties);
            }
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonString);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($jsonString),
                    'User-Agent: SiftScience/v' . SiftClient::API_VERSION . ' sift-php/' . Sift::VERSION)
            );
        }
        else if ($this->method == self::DELETE) curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");


        // Send the request using curl and parse result
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
