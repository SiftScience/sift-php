<?php

class SiftClient {
    const API_ENDPOINT = 'https://api.siftscience.com';
    // Must be kept in sync with composer.json
    const API_VERSION = "203";
    const DEFAULT_TIMEOUT = 2;

    private $api_key;
    private $path;
    private $timeout;

    /**
     * SiftClient constructor
     *
     * @param   $apiKey The SiftScience API key associated with your account. If Sift::$api_key has been set you can instantiate the client without an $apiKey,
     *          If Sift::$api_key has not been set, this parameter is required and must not be null or an empty string.
     */
    function  __construct($apiKey = null, $path = self::API_ENDPOINT, $timeout = self::DEFAULT_TIMEOUT) {
        if (!$apiKey) {
            $apiKey = Sift::$api_key;
        }
        $this->validateArgument($apiKey, 'api key', 'string');
        $this->api_key = $apiKey;

        $this->validateArgument($path, 'path', 'string');
        $this->path = $path;

        $this->timeout = $timeout;
    }

    /**
     * Tracks an event and associated properties through the Sift Science API.
     * Check https://siftscience.com/resources/references/events_api.html for valid $event values and $properties fields.
     *
     * @param $event The name of the event to send. This can be either a reserved event name, like $transaction
     * or $label or a custom event name (that does not start with a $). This parameter is required.
     * @param $properties An array of name-value pairs that specify the event-specific attributes to track.
     * This parameter is required.
     * @param $returnScore (Deprecated -- please use $returnAction instead.) Whether to return the user's score as part of the API 
     * response.  The score will include the posted event. This feature must be
     * enabled for your account in order to use it.  Please contact
     * support@siftscience.com if you are interested in using this feature.
     * @param $returnAction Whether to return an action triggered by this event as part of the API 
     * response.  The score will include the posted event. This feature must be
     * enabled for your account in order to use it.  Please contact
     * support@siftscience.com if you are interested in using this feature.
     * @param $timeout (optional) The number of seconds to wait before failing the request. By default this is
     * configured to 2 seconds (see above).
     * @param $path (optional) Overrides the default API path with a different URL.
     * @return null|SiftResponse
     */
    public function track($event, $properties, $timeout = self::DEFAULT_TIMEOUT, $path = null, $returnScore = FALSE, $returnAction = FALSE) {
        $this->validateArgument($event, 'event', 'string');
        $this->validateArgument($properties, 'properties', 'array');

        if (!$path) $path = self::restApiUrl($returnScore, $returnAction);
        $properties['$api_key'] = $this->api_key;
        $properties['$type'] = $event;
        try {
            $request = new SiftRequest($path, SiftRequest::POST, $properties, $timeout);
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

        $properties = array('api_key' => $this->api_key);
        try {
            $request = new SiftRequest(self::userScoreApiUrl($userId), SiftRequest::GET, $properties, $timeout);
            return $request->send();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Labels a user as either good or bad through the Sift Science API.
     * Check https://siftscience.com/resources/references/labels_api.html for valid $properties fields.
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

    /**
     * Removes a label from a user
     *
     * @param $userId A user's id. This id should be the same as the user_id used in event calls.
     * This parameter is required.
     * @param $timeout (optional) The number of seconds to wait before failing the request. By default this is
     * configured to 2 seconds (see above).
     * @return null|SiftResponse
     */
    public function unlabel($userId, $timeout = self::DEFAULT_TIMEOUT) {
        $this->validateArgument($userId, 'user id', 'string');


        $properties = array('api_key' => $this->api_key);
        try {
            $request = new SiftRequest(self::userLabelApiUrl($userId), SiftRequest::DELETE, $properties, $timeout);
            return $request->send();
        } catch (Exception $e) {
            return null;
        }
    }

    private function validateArgument($arg, $name, $type) {
        // Validate type
        if (gettype($arg) != $type)
            throw new InvalidArgumentException("${name} must be a ${type}.");

        // Check if empty
        if (empty($arg))
            throw new InvalidArgumentException("${name} cannot be empty.");
    }

    private static function restApiUrl($returnScore, $returnAction) {
        $queryParams = array();
        if ($returnScore) array_push($queryParams, 'return_score=true');
        if ($returnAction) array_push($queryParams, 'return_action=true');
        $queryString = empty($queryParams) ? '' : '?' . join('&', $queryParams);

        return self::urlPrefix() . '/events' . $queryString;
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

