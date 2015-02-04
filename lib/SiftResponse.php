<?php

class SiftResponse {
    public $body;
    public $httpStatusCode;
    public $apiStatus;
    public $apiErrorMessage;
    public $request;
    public $rawResponse;

    public function __construct($result, $httpStatusCode, $request) {
        $this->body = null;
        $this->apiStatus = null;
        $this->apiErrorMessage = null;
        
        if (function_exists('json_decode')) {
            $this->body = json_decode($result, true);
        } else {
            require_once(dirname(__FILE__) . '/Services_JSON-1.0.3/JSON.php');
            $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
            $this->body = $json->decode($result);
        }
        $this->httpStatusCode = $httpStatusCode;
        $this->request = $request;
        $this->rawResponse = $result;

        // Only attempt to get our response body if the http status code expects a body
        if (!in_array($this->httpStatusCode, array(204,304))) {
            $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
            $this->body = $json->decode($result);
            $this->apiStatus = intval($this->body['status']);
            $this->apiErrorMessage = $this->body['error_message'];
        }
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
        // If there is no body, check the http status code only (should be 204)
        if (in_array($this->httpStatusCode, array(204,304))) {
            return 204 === $this->httpStatusCode;
        }

        // Otherwise expect http status 200 and api status 0
        return $this->apiStatus === 0 && 200 === $this->httpStatusCode;
    }
}
