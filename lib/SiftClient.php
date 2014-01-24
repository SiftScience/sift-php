<?php

class SiftClient {
    const API_ENDPOINT = 'https://api.siftscience.com';
    const API_VERSION = 203;

    private $apiKey;

    /**
     * Client constructor
     */
    function  __construct($apiKey) {
        $this->validateArgument($apiKey, 'api key', 'string');
        $this->apiKey = $apiKey;
    }

    /**
     * Tracks an event and associated properties through the Sift Science API.
     */
    public function track($event, $properties, $timeout = Sift::DEFAULT_TIMEOUT, $path = null) {
        $this->validateArgument($event, 'event', 'string');
        $this->validateArgument($properties, 'properties', 'array');

        if (!$path) $path = self::restApiUrl();
        $properties['$api_key'] = $this->apiKey;
        $properties['$type'] = $event;
        return (new SiftRequest($path, SiftRequest::POST, $properties, $timeout))->send();
    }

    /**
     * Retrieves a user's fraud score from the Sift Science API.
     */
    public function score($userId, $timeout = Sift::DEFAULT_TIMEOUT) {
        $this->validateArgument($userId, 'user id', 'string');

        $properties = array('api_key' => $this->apiKey);
        return (new SiftRequest(self::userScoreApiUrl($userId), SiftRequest::GET, $properties, $timeout))->send();
    }

    /**
     * Labels a user as either good or bad through the Sift Science API.
     */
    public function label($userId, $properties, $timeout = Sift::DEFAULT_TIMEOUT) {
        $this->validateArgument($userId, 'user id', 'string');
        $this->validateArgument($properties, 'properties', 'array');

        return $this->track('$label', $properties, $timeout, $this->userLabelApiUrl($userId));
    }

    private function validateArgument($arg, $name, $type) {
        // Validate type
        if (gettype($arg) != $type)
            throw new InvalidArgumentException("${name} must be a ${type}.");

        // Check if empty
        if (empty($arg))
            throw new InvalidArgumentException("${name} cannot be empty.");
    }

    private static function restApiUrl() {
        return self::urlPrefix() . '/events';
    }

    private static function userLabelApiUrl($userId) {
        return self::urlPrefix() . '/users/' . urlencode($userId) . '/labels';
    }

    private static function userScoreApiUrl($userId) {
        return self::urlPrefix() . '/score/' . urlencode($userId);
    }

    private static function urlPrefix() {
        return self::API_ENDPOINT . '/v' . self::API_VERSION;
    }
}

