<?php

class SiftRequest {
    public static $GET = 1;
    public static $POST = 2;
    protected static $mockResponse = null;

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
        if (self::$mockResponse) return self::$mockResponse;

        // Build properties string
        $properties_string = ""; $and = "";
        foreach($this->properties as $key=>$value) {
            $properties_string .= $and.$key.'='.$value;
            $and="&";
        }

        // Open and configure curl connection
        $ch = curl_init();
        if ($this->method == self::$GET) {
            curl_setopt($ch, CURLOPT_URL, $this->url."?".$properties_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        } else if ($this->method == self::$POST) {
            curl_setopt($ch, CURLOPT_URL, $this->url);
            curl_setopt($ch, CURLOPT_POST, count($this->properties));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $properties_string);
        }
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

        // Send the request using curl and parse result
        $result = curl_exec($ch);
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close the curl connection
        curl_close($ch);

        return new SiftResponse($result, $httpStatusCode, $this);
    }

    public static function setMockResponse($mock) {
        self::$mockResponse = $mock;
    }

    public static function clearMockResponse() {
        self::$mockResponse = null;
    }
}
