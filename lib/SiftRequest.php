<?php

class SiftRequest {
    const GET = 'GET';
    const POST = 'POST';

    private static $mock = null;

    private $url;
    private $method;
    private $properties;
    private $timeout;

    function __construct($url, $method, $properties, $timeout) {
        $this->url = $url;
        $this->method = $method;
        $this->properties = $properties;
        $this->timeout = $timeout;
    }

    public function send() {
        // Build properties string
        $properties_string = ""; $and = "";
        foreach($this->properties as $key=>$value) {
            $properties_string .= $and.$key.'='.$value;
            $and="&";
        }
        $curlUrl = $this->url;
        if ($this->method == self::GET) $curlUrl .= "?".$properties_string;

        if (self::$mock) {
            if (self::$mock["url"] == $curlUrl && self::$mock["method"] == $this->method) {
                return self::$mock["response"];
            }
            return null;
        }

        // Open and configure curl connection
        $ch = curl_init();
        if ($this->method == self::GET) {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        } else if ($this->method == self::POST) {
            curl_setopt($ch, CURLOPT_POST, count($this->properties));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $properties_string);
        }
        curl_setopt($ch, CURLOPT_URL, $curlUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

        // Send the request using curl and parse result
        $result = curl_exec($ch);
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close the curl connection
        curl_close($ch);

        return new SiftResponse($result, $httpStatusCode, $this);
    }

    public static function setMockResponse($url, $method, $response) {
        self::$mock = array(
            "url" => $url,
            "method" => $method,
            "response" => $response
        );
    }

    public static function clearMockResponse() {
        self::$mock = null;
    }
}
