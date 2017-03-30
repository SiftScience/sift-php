<?php

class SiftClient {
    const API_ENDPOINT = 'https://api.siftscience.com';
    const API3_ENDPOINT = 'https://api3.siftscience.com';

    // Must be kept in sync with composer.json
    const API_VERSION = '204';

    const API3_VERSION = '3';

    const DEFAULT_TIMEOUT = 2;

    private $api_key;
    private $account_id;
    private $timeout;
    private $version;


    /**
     * SiftClient constructor.
     *
     * @param array $opts  Array of optional parameters for this request:
     *     - string 'api_key': The API key associated with your Sift Science account.  By default,
     *           Sift::$api_key.
     *     - string 'account_id': The ID associated with your Sift Science account.  By default,
     *           Sift::$account_id.
     *     - int 'timeout': The number of seconds to wait before failing a request.  By default, 2.
     *     - string 'version': The version of Sift Science's API to call.  By default, '204'.
     */
    function  __construct($opts = array()) {
        $this->mergeArguments($opts, array(
            'api_key' => Sift::$api_key,
            'account_id' => Sift::$account_id,
            'timeout' => self::DEFAULT_TIMEOUT,
            'version' => self::API_VERSION
        ));

        $this->validateArgument($opts['api_key'], 'api key', 'string');

        $this->api_key = $opts['api_key'];
        $this->account_id = $opts['account_id'];
        $this->timeout = $opts['timeout'];
        $this->version = $opts['version'];
    }


    /**
     * Tracks an event and associated properties through the Sift Science API.
     *
     * See https://siftscience.com/resources/references/events_api.html for valid $event values
     * and $properties fields.
     *
     * @param string $event  The type of the event to send. This can be either a reserved event name,
     *     like $transaction or $label or a custom event name (that does not start with a $). This
     *     parameter is required.
     *
     * @param array $properties An array of name-value pairs that specify the event-specific
     *     attributes to track.  This parameter is required.
     *
     * @param array $opts  Array of optional parameters for this request:
     *     - bool 'return_score': Whether to return the user's score as part of the API response.  The
     *           score will include the posted event.
     *     - bool 'return_action': Whether to return any actions triggered by this event as part of the
     *           API response.
     *     - bool 'return_workflow_status': Whether to return the status of any workflow run as a
     *           result of the posted event in the API response.
     *     - array 'abuse_types': List of abuse types, specifying for which abuse types a score
     *          should be returned (if scores were requested).  If not specified, a score will
     *          be returned for every abuse_type to which you are subscribed.
     *     - int 'timeout': By default, this client's timeout is used.
     *     - string 'version': By default, this client's version is used.
     *     - string 'path': The URL path to use for this call.  By default, the path for the requested
     *           version of the Events API is used.
     *
     * @return null|SiftResponse
     */
    public function track($event, $properties, $opts = array()) {
        $this->mergeArguments($opts, array(
            'return_score' => false,
            'return_action' => false,
            'return_workflow_status' => false,
            'abuse_types' => array(),
            'path' => NULL,
            'timeout' => $this->timeout,
            'version' => $this->version
        ));
        $this->validateArgument($event, 'event', 'string');
        $this->validateArgument($properties, 'properties', 'array');

        $path = $opts['path'];
        if (!$path) {
            $path = self::restApiUrl($opts['version']);
        }

        $properties['$api_key'] = $this->api_key;
        $properties['$type'] = $event;

        $params = array();
        if ($opts['return_score']) $params['return_score'] = 'true';
        if ($opts['return_action']) $params['return_action'] = 'true';
        if ($opts['return_workflow_status']) $params['return_workflow_status'] = 'true';
        if ($opts['abuse_types']) $params['abuse_types'] = implode(',', $opts['abuse_types']);

        try {
            $request = new SiftRequest(
                $path, SiftRequest::POST, $opts['timeout'], $opts['version'], array(
                    'body' => $properties,
                    'params' => $params
                ));
            return $request->send();
        } catch (Exception $e) {
            return null;
        }
    }


    /**
     * Retrieves a user's score(s) from the Sift Science API.
     *
     * @param string $userId  A user's id. This id should be the same as the user_id used in event
     *     calls.  This parameter is required.
     *
     * @param array $opts  Array of optional parameters for this request:
     *     - array 'abuse_types': List of abuse types, specifying for which abuse types a score
     *           should be returned.  If not specified, a score will be returned for every abuse
     *           type to which you are subscribed.
     *     - int 'timeout': By default, this client's timeout is used.
     *     - string 'version': By default, this client's version is used.
     *
     * @return null|SiftResponse
     */
    public function score($userId, $opts = array()) {
        $this->mergeArguments($opts, array(
            'abuse_types' => array(),
            'timeout' => $this->timeout,
            'version' => $this->version
        ));

        $this->validateArgument($userId, 'user id', 'string');

        $params = array('api_key' => $this->api_key);
        if ($opts['abuse_types']) $params['abuse_types'] = implode(',', $opts['abuse_types']);

        try {
            $request = new SiftRequest(self::userScoreApiUrl($userId, $opts['version']),
                SiftRequest::GET, $opts['timeout'], $opts['version'], array('params' => $params));
            return $request->send();
        } catch (Exception $e) {
            return null;
        }
    }


    /**
     * Labels a user as either good or bad through the Sift Science API.
     *
     * See https://siftscience.com/resources/references/labels_api.html for valid $properties
     * fields.
     *
     * @param string $userId  The ID of a user.  This parameter is required.
     *
     * @param $properties An array of name-value pairs that specify the label attributes. This
     *     parameter is required.
     *
     * @param array $opts  Array of optional parameters for this request:
     *     - int 'timeout': By default, this client's timeout is used.
     *     - string 'version': By default, this client's version is used.
     *
     * @return null|SiftResponse
     */
    public function label($userId, $properties, $opts = array()) {
        $this->mergeArguments($opts, array(
            'timeout' => $this->timeout,
            'version' => $this->version
        ));

        $this->validateArgument($userId, 'user id', 'string');
        $this->validateArgument($properties, 'properties', 'array');

        return $this->track('$label', $properties, array(
            'timeout' => $opts['timeout'],
            'version' => $opts['version'],
            'path' => $this->userLabelApiUrl($userId, $opts['version'])
        ));
    }


    /**
     * Removes a label from a user
     *
     * @param string $userId  The ID of a user.  This parameter is required.
     *
     * @param array $opts  Array of optional parameters for this request:
     *     - string 'abuse_type': The abuse type for which the user should be unlabeled.
     *           If omitted, the user is unlabeled for all abuse types.
     *     - int 'timeout': By default, this client's timeout is used.
     *     - string 'version': By default, this client's version is used.
     *
     * @return null|SiftResponse
     */
    public function unlabel($userId, $opts = array()) {
        $this->mergeArguments($opts, array(
            'abuse_type' => null,
            'timeout' => $this->timeout,
            'version' => $this->version
        ));

        $this->validateArgument($userId, 'user id', 'string');

        $params = array('api_key' => $this->api_key);
        if ($opts['abuse_type']) $params['abuse_type'] = $opts['abuse_type'];

        try {
            $request = new SiftRequest(self::userLabelApiUrl($userId, $opts['version']),
                SiftRequest::DELETE, $opts['timeout'], $opts['version'], array('params' => $params));
            return $request->send();
        } catch (Exception $e) {
            return null;
        }
    }


    /**
     * Gets the status of a workflow run.
     *
     * @param string $run_id  The ID of a workflow run.
     *
     * @param array $opts  Array of optional parameters for this request:
     *     - string 'account_id': by default, this client's account ID is used.
     *     - int 'timeout': By default, this client's timeout is used.
     */
    public function getWorkflowStatus($run_id, $opts = array()) {
        $this->mergeArguments($opts, array(
            'account_id' => $this->account_id,
            'timeout' => $this->timeout
        ));

        $this->validateArgument($run_id, 'run id', 'string');

        $url = (self::API3_ENDPOINT . '/v3/accounts/'
                . $opts['account_id'] . '/workflows/runs/' . $run_id);

        try {
            $request = new SiftRequest($url, SiftRequest::GET, $opts['timeout'], self::API3_VERSION,
                                       array('auth' => $this->api_key . ':'));
            return $request->send();
        } catch (Exception $e) {
            return null;
        }
    }


    /**
     * Gets the status of a workflow run.
     *
     * @param string $run_id  The ID of a workflow run.
     *
     * @param array $opts  Array of optional parameters for this request:
     *     - string 'account_id': by default, this client's account ID is used.
     *     - int 'timeout': By default, this client's timeout is used.
     */
    public function getUserDecisions($user_id, $opts = array()) {
        $this->mergeArguments($opts, array(
            'account_id' => $this->account_id,
            'timeout' => $this->timeout
        ));

        $this->validateArgument($user_id, 'user id', 'string');

        $url = (self::API3_ENDPOINT . '/v3/accounts/'
                . $opts['account_id'] . '/users/' . $user_id . '/decisions');

        try {
            $request = new SiftRequest($url, SiftRequest::GET, $opts['timeout'], self::API3_VERSION,
                                       array('auth' => $this->api_key . ':'));
            return $request->send();
        } catch (Exception $e) {
            return null;
        }
    }


    /**
     * Gets the latest decision for a user for each abuse type.
     *
     * @param string $order_id  The ID of an order.
     *
     * @param array $opts  Array of optional parameters for this request:
     *     - string 'account_id': by default, this client's account ID is used.
     *     - int 'timeout': By default, this client's timeout is used.
     */
    public function getOrderDecisions($order_id, $opts = array()) {
        $this->mergeArguments($opts, array(
            'account_id' => $this->account_id,
            'timeout' => $this->timeout
        ));

        $this->validateArgument($order_id, 'order id', 'string');

        $url = (self::API3_ENDPOINT . '/v3/accounts/'
                . $opts['account_id'] . '/orders/' . $order_id . '/decisions');

        try {
            $request = new SiftRequest($url, SiftRequest::GET, $opts['timeout'], self::API3_VERSION,
                                       array('auth' => $this->api_key . ':'));
            return $request->send();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Gets a list of configured decisions.
     *
     * @param array $opts  Array of optional parameters for this request:
     *     - string 'account_id': by default, this client's account ID is used.
     *     - int 'timeout': By default, this client's timeout is used.
     *     - array 'abuse_types': filters decisions which can be appiled to
     *       listed abuse types
     *     - string 'entity_type': filters on decisions which can be applied to
     *       a specified entity_type
     *     - string 'next_ref': url that will fetch the next page of decisions
     *     - int 'limit': sets the max number of decisions returned
     *     - int 'from': will return the next decision from the index given up
     *       to the limit.
     */
    public function getDecisions($opts = array()) {
        $this->mergeArguments($opts, array(
            'account_id' => $this->account_id,
            'timeout' => $this->timeout,
            'abuse_types' => null,
            'entity_type' => null,
            'next_ref' => null,
            'limit' => null,
            'from' => null
        ));

        $params = array();

        if ($opts['next_ref']) {
            $url = $opts['next_ref'];
        } else {
            $url = (self::API3_ENDPOINT . '/v3/accounts/' . $opts['account_id'] . '/decisions');

            if ($opts['abuse_types']) $params['abuse_types'] = implode(',', $opts['abuse_types']);
            if ($opts['entity_type']) $params['entity_type'] = $opts['entity_type'];
            if ($opts['limit']) $params['limit'] = $opts['limit'];
            if ($opts['from']) $params['from'] = $opts['from'];
        }

        try {
            $request = new SiftRequest($url, SiftRequest::GET, $opts['timeout'],
                self::API3_VERSION,
                array('auth' => $this->api_key . ':'));

            return $request->send();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Apply a decision to a user. Builds url to apply decision to user and
     * delegates to applyDecision
     *
     * @param string $user_id the id of the user that will get this decision
     * @param string $decision_id The decision that will be applied to a user
     * @param string $source the source of the decision, i.e. MANUAL_REVIEW,
     *     AUTOMATED_RULE, CHARGEBACK
     * @param array $opts  Array of optional parameters for this request:
     *     - string 'account_id': by default, this client's account ID is used.
     *     - int 'timeout': By default, this client's timeout is used.
     *     - string 'analyst': when the source is MANUAL_REVIEW, an analyst
     *     identifier must be passed.
     *     - string 'description': free form text adding context to why this
     *     decision is being applied.
     *     - int 'time': Timestamp of when a decision was applied, mainly used
     *     for backfilling
     */
    public function applyDecisionToUser($user_id, $decision_id, $source, $opts = array()) {
        $this->mergeArguments($opts, array(
            'account_id' => $this->account_id,
            'timeout' => $this->timeout,
            'decision_id' => $decision_id,
            'source' => $source,
            'analyst' => null,
            'description' => null,
            'time' => null
        ));

        $this->validateArgument($user_id, 'user_id', 'string');

        $url = (self::API3_ENDPOINT .
            '/v3/accounts/' . $opts['account_id'] .
            '/users/'. $user_id .
            '/decisions');

        return $this->applyDecision($url, $opts);
    }

    /**
     * Apply a decision to a user. Validates presence of order_id and builds
     * the url to apply a decision to an order and delegates to applyDecision.
     *
     * @param string $user_id the id of order's user id
     * @param string $order_id the id of the order which the decision will be
     * applied
     * @param string $decision_id The decision that will be applied to a user
     * @param string $source the source of the decision, i.e. MANUAL_REVIEW,
     * @param array $opts  Array of optional parameters for this request:
     *     AUTOMATED_RULE, CHARGEBACK
     *     - string 'account_id': by default, this client's account ID is used.
     *     - int 'timeout': By default, this client's timeout is used.
     *     - string 'analyst': when the source is MANUAL_REVIEW, an analyst
     *     identifier must be passed.
     *     - string 'description': free form text adding context to why this
     *     decision is being applied.
     *     - int 'time': Timestamp of when a decision was applied, mainly used
     *     for backfilling
     */
    public function applyDecisionToOrder($user_id, $order_id, $decision_id, $source, $opts = array()) {
        $this->mergeArguments($opts, array(
            'account_id' => $this->account_id,
            'timeout' => $this->timeout,
            'decision_id' => $decision_id,
            'source' => $source,
            'analyst' => null,
            'description' => null,
            'time' => null
        ));

        $this->validateArgument($order_id, 'order_id', 'string');
        $this->validateArgument($user_id, 'user_id', 'string');

        $url = (self::API3_ENDPOINT .
            '/v3/accounts/' . $opts['account_id'] .
            '/users/' . $user_id .
            '/orders/' . $order_id .
            '/decisions');

        return $this->applyDecision($url, $opts);
    }

    private function applyDecision($url, $opts = array()) {
        $this->validateArgument($opts['decision_id'], 'decision_id', 'string');
        $this->validateArgument($opts['source'], 'source', 'string');

        $body = array(
            'decision_id' => $opts['decision_id'],
            'source' => $opts['source']
        );

        if ($opts['analyst']) $body['analyst'] = $opts['analyst'];
        if ($opts['description']) $body['description'] = $opts['description'];
        if ($opts['time']) $body['time'] = $opts['time'];

        try {
            $request = new SiftRequest(
                $url,
                SiftRequest::POST,
                $opts['timeout'],
                self::API3_VERSION,
                array(
                    'auth' => $this->api_key . ':',
                    'body' => $body
                )
            );

            return $request->send();
        } catch (Exception $e) {
            return null;
        }
    }


    /**
     * Merges a function's default parameter values into an array of arguments.
     *
     * In particular, this method:
     *  1. Validates that $opts only contains allowed keys -- i.e., those in $defaults.
     *  2. Modifies $opts in-place by $opts += $defaults.
     *
     * @param array &$opts  The array of arguments passed to a function.
     *
     * @param array $defaults  The array of default parameter values for a function.
     *
     * @throws InvalidArgumentException if $opts contains any keys not in $defaults.
     */
    private function mergeArguments(&$opts, $defaults) {
        if (!is_array($opts)) {
            throw new InvalidArgumentException("Argument 'opts' must be an array.");
        }
        foreach ($opts as $key => $value) {
            if (!array_key_exists($key, $defaults)) {
                throw new InvalidArgumentException("${key} is not a valid argument.");
            }
        }
        $opts += $defaults;
    }


    private function validateArgument($arg, $name, $type) {
        // Validate type
        if (gettype($arg) != $type)
            throw new InvalidArgumentException("${name} must be a ${type}.");

        // Check if empty
        if (empty($arg))
            throw new InvalidArgumentException("${name} cannot be empty.");
    }

    private static function restApiUrl($version) {
        return self::urlPrefix($version) . '/events';
    }

    private static function userLabelApiUrl($userId, $version) {
        return self::urlPrefix($version) . '/users/' . urlencode($userId) . '/labels';
    }

    private static function userScoreApiUrl($userId, $version) {
        return self::urlPrefix($version) . '/score/' . urlencode($userId);
    }

    private static function urlPrefix($version) {
        return self::API_ENDPOINT . '/v' . $version;
    }
}
