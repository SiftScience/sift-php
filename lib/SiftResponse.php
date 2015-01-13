<?php

class SiftResponse {
    public $body;
    public $httpStatusCode;
    public $apiStatus;
    public $apiErrorMessage;
    public $request;
    public $rawResponse;

    public function __construct($result, $httpStatusCode, $request) {
        if (function_exists('json_decode')) {
            $this->body = json_decode($result, true);
        } else {
            require_once(dirname(__FILE__) . '/Services_JSON-1.0.3/JSON.php');
            $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
            $this->body = $json->decode($result);
        }
        $this->httpStatusCode = $httpStatusCode;
        $this->apiStatus = intval($this->body['status']);
        $this->apiErrorMessage = $this->body['error_message'];
        $this->request = $request;
        $this->rawResponse = $result;
    }

    public function __get($name)
    {
        if ($name === "originalRequest") {
            trigger_error("The member variable 'originalRequest' is deprecated.  Please use 'request' instead.\n",
             E_USER_WARNING);
            return $this->request;
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_WARNING);
        return null;
    }

    public function isOk() {
        return $this->apiStatus === 0;
    }
}
