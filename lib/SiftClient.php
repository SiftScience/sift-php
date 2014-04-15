<?php

class SiftClient {
    const API_ENDPOINT = 'https://api.siftscience.com';
    const API_VERSION = 203;
    const DEFAULT_TIMEOUT = 2;

    private $apiKey;

    /**
     * SiftClient constructor
     *
     * @param $apiKey The SiftScience API key associated with your account. This cannot be null or blank.
     */
    function  __construct($apiKey) {
        $this->validateArgument($apiKey, 'api key', 'string');
        $this->apiKey = $apiKey;
    }

    /**
     * Tracks an event and associated properties through the Sift Science API.
     *
     * @param $event The name of the event to send. This can be either a reserved event name, like $transaction
     * or $label or a custom event name (that does not start with a $). This parameter is required.
     * @param $properties An array of name-value pairs that specify the event-specific attributes to track.
     * This parameter is required.
     * @param $returnScore Whether to return the user's score as part of the API 
     * response.  The score will include the posted event.
     * @param $timeout (optional) The number of seconds to wait before failing the request. By default this is
     * configured to 2 seconds (see above).
     * @param $path (optional) Overrides the default API path with a different URL.
     * @return null|SiftResponse
     */
    public function track($event, $properties, $timeout = self::DEFAULT_TIMEOUT, $path = null, $returnScore = False) {
        $this->validateArgument($event, 'event', 'string');
        $this->validateArgument($properties, 'properties', 'array');

        if (!$path) $path = self::restApiUrl($returnScore);
        $properties['$api_key'] = $this->apiKey;
        $properties['$type'] = $event;
        try {
            $request = new SiftRequest($path, SiftRequest::POST, $properties, $timeout, $returnScore);
            return $request->send();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Retrieves a user's fraud score from the Sift Science API.
     *
     * @param $userId A user's id. This id should be the same as the user_id used in event calls.
     * This parameter is required.
     * @param $timeout (optional) The number of seconds to wait before failing the request. By default this is
     * configured to 2 seconds (see above).
     * @return null|SiftResponse
     */
    public function score($userId, $timeout = self::DEFAULT_TIMEOUT) {
        $this->validateArgument($userId, 'user id', 'string');

        $properties = array('api_key' => $this->apiKey);
        try {
            $request = new SiftRequest(self::userScoreApiUrl($userId), SiftRequest::GET, $properties, $timeout);
            return $request->send();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Labels a user as either good or bad through the Sift Science API.
     *
     * @param $userId A user's id. This id should be the same as the user_id used in event calls.
     * This parameter is required.
     * @param $properties An array of name-value pairs that specify the label attributes. This parameter is required.
     * @param $timeout (optional) The number of seconds to wait before failing the request. By default this is
     * configured to 2 seconds (see above).
     * @return null|SiftResponse
     */
    public function label($userId, $properties, $timeout = self::DEFAULT_TIMEOUT) {
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

    private static function restApiUrl($returnScore) {
        return self::urlPrefix() . '/events?return_score=' . ($returnScore ? 'true' : 'false');
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

