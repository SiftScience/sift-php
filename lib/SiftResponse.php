<?php

class SiftResponse {
    public $body;
    public $httpStatusCode;
    public $curlErrno;
    public $curlError;
    public $apiStatus;
    public $apiError;
    public $apiErrorMessage;
    public $apiDescription;
    public $request;
    public $rawResponse;

    public function __construct($result, $httpStatusCode, $request, $curlErrno = 0, $curlError = '') {
        $this->body = null;
        $this->apiStatus = null;
        $this->apiErrorMessage = null;
        $this->apiError = null;
        $this->apiDescription = null;
        $this->httpStatusCode = $httpStatusCode;
        $this->curlErrno = $curlErrno;
        $this->curlError = $curlError;
        $this->request = $request;
        $this->rawResponse = $result;

        // Only attempt to get our response body if the http status code expects a body
        if ($this->httpStatusCode >= 200 && !in_array($this->httpStatusCode, [204, 304])) {
            
            $this->body = json_decode($result, true);

            if (is_null($this->body)) {
                $this->apiErrorMessage = 'Invalid JSON received from Sift API';
            } elseif (is_array($this->body)) {
                // /v3 responses use error and description
                if (array_key_exists('error', $this->body)) {
                    $this->apiError = $this->body['error'];
                }
                if (array_key_exists('description', $this->body)) {
                    $this->apiDescription = $this->body['description'];
                }

                // /v2xx responses use status and error_message
                if (array_key_exists('status', $this->body)) {
                    $this->apiStatus = intval($this->body['status']);
                }
                if (array_key_exists('error_message', $this->body)) {
                    $this->apiErrorMessage = $this->body['error_message'];
                }
            }
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
        if (in_array($this->httpStatusCode, [204, 304])) {
            return 204 === $this->httpStatusCode;
        }

        // Otherwise expect http status 200 and api status 0.
        // NOTE: $this->apiStatus will be null for all /v3 responses.
        return ($this->apiStatus == 0) && (200 === $this->httpStatusCode);
    }
}
