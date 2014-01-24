<?php

class SiftClient {
    private static $API_ENDPOINT = "https://api.siftscience.com";
    protected static $API_VERSION = 203;

    private $apiKey;

    /**
     * Client constructor
     */
    function  __construct($apiKey) {
        $this->validateArgument($apiKey, "api key", "string");
        $this->apiKey = $apiKey;
    }

    /**
     * Tracks an event and associated properties through the Sift Science API.
     */
    public function track($event, $properties, $timeout = 2, $path = null) {
        $this->validateArgument($event, "event", "string");
        $this->validateArgument($properties, "properties", "array");

        if (!$path) $path = self::restApiUrl();
        $properties['$api_key'] = $this->apiKey;
        $properties['$type'] = $event;
        return (new SiftRequest($path, SiftRequest::$POST, $properties, $timeout))->send();
    }

    /**
     * Retrieves a user's fraud score from the Sift Science API.
     */
    public function score($userId, $timeout = 2) {
        $this->validateArgument($userId, "user id", "string");

        $properties = array("api_key" => $this->apiKey);
        return (new SiftRequest(self::userScoreApiUrl($userId), SiftRequest::$GET, $properties, $timeout))->send();
    }

    /**
     * Labels a user as either good or bad through the Sift Science API.
     */
    public function label($userId, $properties, $timeout = 2) {
        $this->validateArgument($userId, "user id", "string");
        $this->validateArgument($properties, "properties", "array");

        $this->track('$label', $properties, $timeout, $this->userLabelApiUrl($userId));
    }

    private function validateArgument($arg, $name, $type) {
        // Validate type
        if (gettype($arg) != $type)
            throw new InvalidArgumentException("${name} must be a ${type}.");

        // Check if empty
        if ((gettype($arg) == "string" && !strlen($arg)) || (gettype($arg) == "array" && !count($arg)))
            throw new InvalidArgumentException("${name} cannot be empty.");
    }

    private static function restApiUrl() {
        return self::$API_ENDPOINT."/v".self::$API_VERSION."/events";
    }

    private static function userLabelApiUrl($userId) {
        return self::$API_ENDPOINT."/v".self::$API_VERSION."/users/".urlencode($userId)."/labels";
    }

    private static function userScoreApiUrl($userId) {
        return self::$API_ENDPOINT."/v".self::$API_VERSION."/score/".urlencode($userId);
    }
}

