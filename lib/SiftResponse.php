<?php

class SiftResponse {
    public $body;
    public $httpStatusCode;
    public $apiStatus;
    public $apiErrorMessage;
    public $originalRequest;

    public function __construct($result, $httpStatusCode, $request) {
        $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
        $this->body = $json->decode($result);
        $this->httpStatusCode = $httpStatusCode;
        $this->apiStatus = intval($this->body['status']);
        $this->apiErrorMessage = $this->body['error_message'];
        $this->originalRequest = $this;
    }

    public function isOk() {
        return $this->apiStatus == 0;
    }
}