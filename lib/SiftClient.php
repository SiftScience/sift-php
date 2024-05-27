<?php

class SiftClient {

    const API_ENDPOINT = 'https://api.sift.com';
    // Must be kept in sync with composer.json
    const API_VERSION = '205';
    const API3_VERSION = '3';
    const API_VERIFICATION = '1.1';
    const DEFAULT_TIMEOUT = 2;

    private $api_key;
    private $account_id;
    private $timeout;
    private $version;
    private $api_endpoint;
    private $curl_opts;

    /**
     * @var null|\Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function setLogger(\Psr\Log\LoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     * @param string $message
     * @param array  $context
     */
    private function logError($message, array $context = []) {
        if ($this->logger) {
            $this->logger->error($message, $context);
        }
    }

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
     *     - string 'api_endpoint': The backend api to send requests to.  By default,
     *           'https://api.sift.com'.
     *     - array 'curl_opts': Array with key-value pairs corresponding to options to pass to
     *           curl_setopt().  Options override any options set by the SiftClient, so use with
     *           caution.  By default, array().
     */
    function __construct($opts = []) {
        $this->mergeArguments($opts, [
            'api_key' => Sift::$api_key,
            'account_id' => Sift::$account_id,
            'timeout' => self::DEFAULT_TIMEOUT,
            'version' => self::API_VERSION,
            'api_endpoint' => self::API_ENDPOINT,
            'curl_opts' => [],
        ]);

        $this->validateArgument($opts['api_key'], 'api key', 'string');

        $this->api_key = $opts['api_key'];
        $this->account_id = $opts['account_id'];
        $this->timeout = $opts['timeout'];
        $this->version = $opts['version'];
        $this->api_endpoint = $opts['api_endpoint'];
        $this->curl_opts = $opts['curl_opts'];
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
     *     - bool 'force_workflow_run': Whether to request an asynchronous workflow run if the 
     *           workflow is configured to only run with API request.      
     *     - array 'abuse_types': List of abuse types, specifying for which abuse types a score
     *          should be returned (if scores were requested).  If not specified, a score will
     *          be returned for every abuse_type to which you are subscribed.
     *     - int 'timeout': By default, this client's timeout is used.
     *     - string 'version': By default, this client's version is used.
     *     - string 'path': The URL path to use for this call.  By default, the path for the requested
     *           version of the Events API is used.
     *     - include_score_percentiles(optional) : Whether to add new parameter in the query parameter.
     *     - if include_score_percentiles is true then add a new parameter called fields in the query parameter
     *     - include_warnings(optional) : Whether to add list of warnings (if any) to response.
     *     - if include_warnings is true then add 'warnings' to the 'fields' query parameter.
     *
     * @return null|SiftResponse
     */
    public function track($event, $properties, $opts = []) {
        $this->mergeArguments($opts, [
            'return_score' => false,
            'return_action' => false,
            'return_workflow_status' => false,
            'return_route_info' => false,
            'force_workflow_run' => false,
            'abuse_types' => [],
            'path' => null,
            'timeout' => $this->timeout,
            'version' => $this->version,
            'include_score_percentiles' => false,
            'include_warnings' => false
        ]);

        $this->validateArgument($event, 'event', 'string');
        $this->validateArgument($properties, 'properties', 'array');

        $path = $opts['path'];
        if (!$path) {
            $path = self::restApiUrl($opts['version']);
        }

        $properties['$api_key'] = $this->api_key;
        $properties['$type'] = $event;

        $params = [];
        if ($opts['return_score'])
            $params['return_score'] = 'true';
        if ($opts['return_action'])
            $params['return_action'] = 'true';
        if ($opts['return_workflow_status'])
            $params['return_workflow_status'] = 'true';
        if ($opts['return_route_info'])
            $params['return_route_info'] = 'true';
        if ($opts['force_workflow_run'])
            $params['force_workflow_run'] = 'true';
        if ($opts['abuse_types'])
            $params['abuse_types'] = implode(',', $opts['abuse_types']);
        if ($opts['include_score_percentiles'] || $opts['include_warnings']) {
            $fields = [];
            if ($opts['include_score_percentiles']) {
                $fields[] = 'SCORE_PERCENTILES';
            }
            if ($opts['include_warnings']) {
                $fields[] = 'WARNINGS';
            }
            $params['fields'] = implode(',', $fields);
        }
            
        try {
            $request = new SiftRequest(
                 $path, SiftRequest::POST, $opts['timeout'], $opts['version'], [
                      'body' => $properties,
                      'params' => $params
                ],
                $this->curl_opts
            );
            return $request->send();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
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
     *     - include_score_percentiles(optional) : Whether to add new parameter in the query parameter.
     *     - if include_score_percentiles is true then add a new parameter called fields in the query parameter
     *
     * @return null|SiftResponse
     */
    public function score($userId, $opts = []) {
        $this->mergeArguments($opts, [
            'abuse_types' => [],
            'timeout' => $this->timeout,
            'version' => $this->version,
            'include_score_percentiles' => false
        ]);

        $this->validateArgument($userId, 'user id', 'string');

        $params = ['api_key' => $this->api_key];
        if ($opts['abuse_types'])
            $params['abuse_types'] = implode(',', $opts['abuse_types']);

        if($opts['include_score_percentiles'])
            $params['fields'] = 'SCORE_PERCENTILES';

        try {
            $request = new SiftRequest(self::scoreApiUrl($userId, $opts['version']), SiftRequest::GET, $opts['timeout'], $opts['version'], ['params' => $params]);
            return $request->send();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            return null;
        }
    }

    /**
     * Fetches the latest score(s) computed for the specified user and abuse types from the Sift Science API.
     *
     * As opposed to client.score() and client.rescore_user(), this *does not* compute a new score for the
     * user; it simply fetches the latest score(s) which have computed. These scores may be arbitrarily old.
     * See https://siftscience.com/developers/docs/php/score-api/get-score for more details.
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
     *     - include_score_percentiles(optional) : Whether to add new parameter in the query parameter.
     *     - if include_score_percentiles is true then add a new parameter called fields in the query parameter
     * 
     * @return null|SiftResponse
     */
    public function get_user_score($userId, $opts = []) {
        $this->mergeArguments($opts, [
            'abuse_types' => [],
            'timeout' => $this->timeout,
            'version' => $this->version,
            'include_score_percentiles' => false
        ]);

        $this->validateArgument($userId, 'user id', 'string');

        $params = ['api_key' => $this->api_key];
        if ($opts['abuse_types'])
            $params['abuse_types'] = implode(',', $opts['abuse_types']);

        if($opts['include_score_percentiles'])
            $params['fields'] = 'SCORE_PERCENTILES';

        try {
            $request = new SiftRequest(self::userScoreApiUrl($userId, $opts['version']), SiftRequest::GET, $opts['timeout'], $opts['version'], ['params' => $params]);
            return $request->send();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            return null;
        }
    }

    /**
     * Rescores the specified user for the specified abuse types and returns the resulting score(s).
     *
     * See https://siftscience.com/developers/docs/php/score-api/rescore for more details.
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
    public function rescore_user($userId, $opts = []) {
        $this->mergeArguments($opts, [
            'abuse_types' => [],
            'timeout' => $this->timeout,
            'version' => $this->version
        ]);

        $this->validateArgument($userId, 'user id', 'string');

        $params = ['api_key' => $this->api_key];
        if ($opts['abuse_types'])
            $params['abuse_types'] = implode(',', $opts['abuse_types']);

        try {
            $request = new SiftRequest(self::userScoreApiUrl($userId, $opts['version']), SiftRequest::POST, $opts['timeout'], $opts['version'], ['params' => $params]);
            return $request->send();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
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
     * @param array $properties An array of name-value pairs that specify the label attributes.
     *     This parameter is required.
     *
     * @param array $opts  Array of optional parameters for this request:
     *     - int 'timeout': By default, this client's timeout is used.
     *     - string 'version': By default, this client's version is used.
     *
     * @return null|SiftResponse
     */
    public function label($userId, $properties, $opts = []) {
        $this->mergeArguments($opts, [
            'timeout' => $this->timeout,
            'version' => $this->version
        ]);

        $this->validateArgument($userId, 'user id', 'string');
        $this->validateArgument($properties, 'properties', 'array');

        return $this->track('$label', $properties, [
                    'timeout' => $opts['timeout'],
                    'version' => $opts['version'],
                    'path' => $this->userLabelApiUrl($userId, $opts['version'])
        ]);
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
    public function unlabel($userId, $opts = []) {
        $this->mergeArguments($opts, [
            'abuse_type' => null,
            'timeout' => $this->timeout,
            'version' => $this->version
        ]);

        $this->validateArgument($userId, 'user id', 'string');

	$params = ['api_key' => $this->api_key];
        if ($opts['abuse_type'])
            $params['abuse_type'] = $opts['abuse_type'];

        try {
            $request = new SiftRequest(self::userLabelApiUrl($userId, $opts['version']), SiftRequest::DELETE, $opts['timeout'], $opts['version'], ['params' => $params]);
            return $request->send();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
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
    public function getWorkflowStatus($run_id, $opts = []) {
        $this->mergeArguments($opts, [
            'account_id' => $this->account_id,
            'timeout' => $this->timeout
        ]);

        $this->validateArgument($run_id, 'run id', 'string');

        $url = ($this->api_endpoint .
                '/v3/accounts/' . rawurlencode($opts['account_id']) .
                '/workflows/runs/' . rawurlencode($run_id));

        try {
            $request = new SiftRequest($url, SiftRequest::GET, $opts['timeout'], self::API3_VERSION, ['auth' => $this->api_key . ':']);
            return $request->send();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
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
    public function getUserDecisions($user_id, $opts = []) {
        $this->mergeArguments($opts, [
            'account_id' => $this->account_id,
            'timeout' => $this->timeout
        ]);

        $this->validateArgument($user_id, 'user id', 'string');

        $url = ($this->api_endpoint .
                '/v3/accounts/' . rawurlencode($opts['account_id']) .
                '/users/' . rawurlencode($user_id) .
                '/decisions');

        try {
            $request = new SiftRequest($url, SiftRequest::GET, $opts['timeout'], self::API3_VERSION, ['auth' => $this->api_key . ':']);
            return $request->send();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
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
    public function getOrderDecisions($order_id, $opts = []) {
        $this->mergeArguments($opts, [
            'account_id' => $this->account_id,
            'timeout' => $this->timeout
        ]);

        $this->validateArgument($order_id, 'order id', 'string');

        $url = ($this->api_endpoint .
                '/v3/accounts/' . rawurlencode($opts['account_id']) .
                '/orders/' . rawurlencode($order_id) .
                '/decisions');

        try {
            $request = new SiftRequest($url, SiftRequest::GET, $opts['timeout'], self::API3_VERSION, ['auth' => $this->api_key . ':']);
            return $request->send();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            return null;
        }
    }

    /**
     * Gets the latest decision for a session.
     *
     * @param string $user_id     The ID of session's user.
     * @param string $session_id  The ID of a session.
     *
     * @param array $opts  Array of optional parameters for this request:
     *     - string 'account_id': by default, this client's account ID is used.
     *     - int 'timeout': By default, this client's timeout is used.
     */
    public function getSessionDecisions($user_id, $session_id, $opts = []) {
        $this->mergeArguments($opts, [
            'account_id' => $this->account_id,
            'timeout' => $this->timeout
        ]);

        $this->validateArgument($session_id, 'session id', 'string');

        $url = ($this->api_endpoint .
                '/v3/accounts/' . rawurlencode($opts['account_id']) .
                '/users/' . rawurlencode($user_id) .
                '/sessions/' . rawurlencode($session_id) .
                '/decisions');

        try {
            $request = new SiftRequest($url, SiftRequest::GET, $opts['timeout'], self::API3_VERSION, ['auth' => $this->api_key . ':']);
            return $request->send();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            return null;
        }
    }

    /**
     * Gets the latest decision for a piece of content.
     *
     * @param string $content_id  The ID of a piece of content.
     * @param string $user_id     The ID of the owner of the content.
     *
     * @param array $opts  Array of optional parameters for this request:
     *     - string 'account_id': by default, this client's account ID is used.
     *     - int 'timeout': By default, this client's timeout is used.
     */
    public function getContentDecisions($user_id, $content_id, $opts = []) {
        $this->mergeArguments($opts, [
            'account_id' => $this->account_id,
            'timeout' => $this->timeout
        ]);

        $this->validateArgument($content_id, 'content id', 'string');

        $url = ($this->api_endpoint .
                '/v3/accounts/' . rawurlencode($opts['account_id']) .
                '/users/' . rawurlencode($user_id) .
                '/content/' . rawurlencode($content_id) .
                '/decisions');

        try {
            $request = new SiftRequest($url, SiftRequest::GET, $opts['timeout'], self::API3_VERSION, ['auth' => $this->api_key . ':']);
            return $request->send();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
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
    public function getDecisions($opts = []) {
        $this->mergeArguments($opts, [
            'account_id' => $this->account_id,
            'timeout' => $this->timeout,
            'abuse_types' => null,
            'entity_type' => null,
            'next_ref' => null,
            'limit' => null,
            'from' => null
        ]);

        $params = [];

        if ($opts['next_ref']) {
            $url = $opts['next_ref'];
        } else {
            $url = ($this->api_endpoint .
                    '/v3/accounts/' . rawurlencode($opts['account_id']) .
                    '/decisions');

            if ($opts['abuse_types'])
                $params['abuse_types'] = implode(',', $opts['abuse_types']);
            if ($opts['entity_type'])
                $params['entity_type'] = $opts['entity_type'];
            if ($opts['limit'])
                $params['limit'] = $opts['limit'];
            if ($opts['from'])
                $params['from'] = $opts['from'];
        }

        try {

            $request = new SiftRequest($url, SiftRequest::GET, $opts['timeout'], self::API3_VERSION, ['auth' => $this->api_key . ':', 'params' => $params]);
          
            return $request->send();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
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
    public function applyDecisionToUser($user_id, $decision_id, $source, $opts = []) {
        $this->mergeArguments($opts, [
            'account_id' => $this->account_id,
            'timeout' => $this->timeout,
            'decision_id' => $decision_id,
            'source' => $source,
            'analyst' => null,
            'description' => null,
            'time' => null
        ]);

        $this->validateArgument($user_id, 'user_id', 'string');

        $url = ($this->api_endpoint .
                '/v3/accounts/' . rawurlencode($opts['account_id']) .
                '/users/' . rawurlencode($user_id) .
                '/decisions');

        return $this->applyDecision($url, $opts);
    }

    /**
     * Apply a decision to an order. Validates presence of order_id and builds
     * the url to apply a decision to an order and delegates to applyDecision.
     *
     * @param string $user_id the id of order's user id
     * @param string $order_id the id of the order which the decision will be
     * applied
     * @param string $decision_id The decision that will be applied to the order
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
    public function applyDecisionToOrder($user_id, $order_id, $decision_id, $source, $opts = []) {
        $this->mergeArguments($opts, [
            'account_id' => $this->account_id,
            'timeout' => $this->timeout,
            'decision_id' => $decision_id,
            'source' => $source,
            'analyst' => null,
            'description' => null,
            'time' => null
        ]);

        $this->validateArgument($order_id, 'order_id', 'string');
        $this->validateArgument($user_id, 'user_id', 'string');

        $url = ($this->api_endpoint .
                '/v3/accounts/' . rawurlencode($opts['account_id']) .
                '/users/' . rawurlencode($user_id) .
                '/orders/' . rawurlencode($order_id) .
                '/decisions');

        return $this->applyDecision($url, $opts);
    }

    /**
     * Apply a decision to a piece of content. Validates presence of content_id
     * and builds the url to apply a decision to a piece of content and delegates
     * to applyDecision.
     *
     * @param string $user_id the id of content's user id
     * @param string $content_id the id of the content which the decision will
     * be applied
     * @param string $decision_id The decision that will be applied to the order
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
    public function applyDecisionToContent($user_id, $content_id, $decision_id, $source, $opts = []) {
        $this->mergeArguments($opts, [
            'account_id' => $this->account_id,
            'timeout' => $this->timeout,
            'decision_id' => $decision_id,
            'source' => $source,
            'analyst' => null,
            'description' => null,
            'time' => null
        ]);

        $this->validateArgument($content_id, 'content_id', 'string');
        $this->validateArgument($user_id, 'user_id', 'string');
        $url = ($this->api_endpoint .
                '/v3/accounts/' . $opts['account_id'] .
                '/users/' . rawurlencode($user_id) .
                '/content/' . rawurlencode($content_id) .
                '/decisions');

        return $this->applyDecision($url, $opts);
    }

    /**
     * Apply a decision to a session. Validates presence of order_id and builds
     * the url to apply a decision to a session and delegates to applyDecision.
     *
     * @param string $user_id the id of session's user id
     * @param string $session_id the id of the session which the decision will be
     * applied
     * @param string $decision_id The decision that will be applied to the session
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
    public function applyDecisionToSession($user_id, $session_id, $decision_id, $source, $opts = []) {
        $this->mergeArguments($opts, [
            'account_id' => $this->account_id,
            'timeout' => $this->timeout,
            'decision_id' => $decision_id,
            'source' => $source,
            'analyst' => null,
            'description' => null,
            'time' => null
        ]);

        $this->validateArgument($session_id, 'session_id', 'string');
        $this->validateArgument($user_id, 'user_id', 'string');

        $url = ($this->api_endpoint .
                '/v3/accounts/' . rawurlencode($opts['account_id']) .
                '/users/' . rawurlencode($user_id) .
                '/sessions/' . rawurlencode($session_id) .
                '/decisions');

        return $this->applyDecision($url, $opts);
    }

    private function applyDecision($url, $opts = []) {
        $this->validateArgument($opts['decision_id'], 'decision_id', 'string');
        $this->validateArgument($opts['source'], 'source', 'string');

        $body = [
            'decision_id' => $opts['decision_id'],
            'source' => $opts['source']
        ];

        if ($opts['analyst'])
            $body['analyst'] = $opts['analyst'];
        if ($opts['description'])
            $body['description'] = $opts['description'];
        if ($opts['time'])
            $body['time'] = $opts['time'];

        try {
            $request = new SiftRequest(
                    $url, 
                    SiftRequest::POST, 
                    $opts['timeout'], 
                    self::API3_VERSION, 
                    [
                       'auth' => $this->api_key . ':',
                       'body' => $body
                    ]
            );

            return $request->send();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            return null;
        }
    }

    /**
     * Check user OTP and decides whether the user should be able to proceed or not. 
     * See https://sift.com/developers/docs/curl/verification-api/check for valid $event values
     * and $properties fields.
     * 
     * @param array $properties An array of name-value pairs that specify the event-specific
     *     attributes to check.  This parameter is required.
     *
     * @param array $opts  Array of optional parameters for this request:
     *     - int 'timeout': By default, this client's timeout is used.
     *     - string 'version': By default, this client's version is used.
     *
     * @return null|SiftResponse
     */
    public function check($properties, $opts = []) {
        $this->mergeArguments($opts, [
            'timeout' => $this->timeout,
            'version' => self::API_VERIFICATION
        ]);
        $this->validateArgument($properties, 'properties', 'array');
        $curl_opts['CURLOPT_HTTPHEADER'] = ['Authorization: Basic ' . base64_encode($this->api_key . ':')];
        try {
            $request = new SiftRequest(
                self::checkApiUrl($opts['version']), 
                SiftRequest::POST, 
                $opts['timeout'], 
                $opts['version'], 
                [
                    'body' => $properties
                ],
                $curl_opts);
                
            return $request->send();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            return null;
        }
    }

    /**
     * Genaerate OTP code that is stored by Sift and emails the code to the user. 
     * See https://sift.com/developers/docs/curl/verification-api/send for valid $event values
     * and $properties fields.
     * 
     * @param array $properties An array of name-value pairs .  This parameter is required.
     *
     * @param array $opts  Array of optional parameters for this request:
     *     - int 'timeout': By default, this client's timeout is used.
     *     - string 'version': By default, this client's version is used.
     *
     * @return null|SiftResponse
     */
    public function send($parameters, $opts = []) {
        $this->mergeArguments($opts, [
            'timeout' => $this->timeout,
            'version' => self::API_VERIFICATION,
        ]);

        $this->validateArgument($parameters['$user_id'], 'user id', 'string');
        $curl_opts['CURLOPT_HTTPHEADER'] = ['Authorization: Basic ' . base64_encode($this->api_key . ':')];
        try {
            $request = new SiftRequest(
                self::userSendApiUrl($opts['version']), 
                SiftRequest::POST, 
                $opts['timeout'], 
                $opts['version'], 
                [
                  'body' => $parameters,
                ], 
                $curl_opts
            );
            return $request->send();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            return null;
        }
    }

    /**
     * Verification Api- resend
     * Regenerate new OTP and send it to user. 
     * See https://sift.com/developers/docs/curl/verification-api/resend for valid $event values
     * and $properties fields.
     * 
     * @param array $properties An array of name-value pairs .  This parameter is required.
     *
     * @param array $opts  Array of optional parameters for this request:
     *     - int 'timeout': By default, this client's timeout is used.
     *     - string 'version': By default, this client's version is used.
     *
     * @return null|SiftResponse
     */
    public function resend($parameters, $opts = []) {
        $this->mergeArguments($opts, [
            'timeout' => $this->timeout,
            'version' => self::API_VERIFICATION,
        ]);

        $this->validateArgument($parameters['$user_id'], 'user id', 'string');
        $curl_opts['CURLOPT_HTTPHEADER'] = ['Authorization: Basic ' . base64_encode($this->api_key . ':')];
        try {
            $request = new SiftRequest(
                self::userResendApiUrl($opts['version']), 
                SiftRequest::POST, 
                $opts['timeout'], 
                $opts['version'], 
                [
                   'body' => $parameters,
                ], 
                $curl_opts
            );

            return $request->send();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            return null;
        }
    }


     /**
     * Get a list of PSP Merchant profiles. 
     * @param array $parameters An array of name-value pairs .  
     *
     * @param array $opts  Array of optional parameters for this request:
     *     - string 'account_id': by default, this client's account ID is used.
     *     - int 'timeout': By default, this client's timeout is used.
     *
     * @return null|SiftResponse
     */
    public function merchants($parameters, $opts = []) {
        $this->mergeArguments($opts, [
            'account_id' => $this->account_id,
            'timeout' => $this->timeout,
        ]);
        if (isset($parameters['batch_token']))
            $this->validateArgument($parameters['batch_token'], 'batch_token', 'string');
        if (isset($parameters['batch_size']))
            $this->validateArgument($parameters['batch_size'], 'batch_size', 'integer');
        $this->validateArgument($this->account_id, 'accountId', 'string');

        $url = ($this->api_endpoint .
                '/v3/accounts/' . rawurlencode($opts['account_id']) .
                '/psp_management/merchants');

        try {
            $request = new SiftRequest($url, SiftRequest::GET, $opts['timeout'], self::API3_VERSION, array('auth' => $this->api_key . ':', 'params' => $parameters));
            return $request->send();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            return null;
        }
    }

    /**
     * Create a new PSP Merchant profile. 
     * @param array $parameters An array of merchant profile data .  
     *
     * @param array $opts  Array of optional parameters for this request:
     *     - string 'account_id': by default, this client's account ID is used.
     *     - int 'timeout': By default, this client's timeout is used.
     *
     * @return null|SiftResponse
     */
    public function postMerchant($parameters, $opts = []) {
        $this->mergeArguments($opts, [
            'timeout' => $this->timeout,
            'account_id' => $this->account_id,
        ]);

        $this->validateArgument($parameters, 'properties', 'array');
        $this->validateMerchantArgument($parameters);
        $url = ($this->api_endpoint .
                '/v3/accounts/' . rawurlencode($opts['account_id']) .
                '/psp_management/merchants');
        try {
            $request = new SiftRequest($url, SiftRequest::POST, $opts['timeout'], self::API3_VERSION, ['auth' => $this->api_key . ':',
                'body' => $parameters,
            ]);

            return $request->send();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            return null;
        }
    }

    /**
     *  Get a PSP Merchant profile.. 
     * @param array $merchant_id :Merchant ID .  
     *
     * @param array $opts  Array of optional parameters for this request:
     *     - string 'account_id': by default, this client's account ID is used.
     *     - int 'timeout': By default, this client's timeout is used.
     *
     * @return null|SiftResponse
     */
    public function getMerchant($merchant_id, $opts = []) {
        $this->mergeArguments($opts, [
            'account_id' => $this->account_id,
            'timeout' => $this->timeout,
        ]);

        $this->validateArgument($this->account_id, 'accountId', 'string');
        $this->validateArgument($merchant_id, 'merchantId', 'string');

        $url = ($this->api_endpoint .
                '/v3/accounts/' . rawurlencode($opts['account_id']) .
                '/psp_management/merchants/' . rawurlencode($merchant_id));

        try {
            $request = new SiftRequest($url, SiftRequest::GET, $opts['timeout'], self::API3_VERSION, ['auth' => $this->api_key . ':']);
            return $request->send();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            return null;
        }
    }

    /**
     *  Update a PSP Merchant profile.. 
     * @param array $parameters An array of merchant profile data .    
     *
     * @param array $opts  Array of optional parameters for this request:
     *     - string 'account_id': by default, this client's account ID is used.
     *     - int 'timeout': By default, this client's timeout is used.
     *
     * @return null|SiftResponse
     */
    public function putMerchant($merchant_id, $parameters, $opts = []) {
        $this->mergeArguments($opts, [
            'timeout' => $this->timeout,
            'account_id' => $this->account_id,
        ]);

        $this->validateArgument($parameters, 'properties', 'array');
        $this->validateArgument($merchant_id, 'merchantId', 'string');
        $this->validateMerchantArgument($parameters);

        $url = ($this->api_endpoint .
                '/v3/accounts/' . rawurlencode($opts['account_id']) .
                '/psp_management/merchants/' . rawurlencode($merchant_id));
        try {
            $request = new SiftRequest($url, SiftRequest::PUT, $opts['timeout'], self::API3_VERSION, ['auth' => $this->api_key . ':',
                'body' => $parameters,
            ]);
            return $request->send();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            return null;
        }
    }

    /**
     * Webhooks to receive notifications about particular events in Sift.
     *
     * See https://sift.com/developers/docs/php/webhooks-api/create for valid $properties fields
     *
     * @param array $opts  Array of optional parameters for this request:
     *     - string 'account_id': by default, this client's account ID is used.
     *     - int 'timeout': By default, this client's timeout is used.
     *
     * @return null|SiftResponse
     */
    public function postWebhooks($parameters, $opts = array()) {
        $this->mergeArguments($opts, [
            'timeout' => $this->timeout,
            'version' => self::API3_VERSION,
            'account_id' => $this->account_id
        ]);

        $this->validateArgument($parameters, 'properties', 'array');
        $this->validateArgument($this->account_id, 'account_id', 'string');
        $this->validateWebhookArgument($parameters);

        try {
            $request = new SiftRequest(self::webhookApiUrl($opts['version'], $opts['account_id']), 
             SiftRequest::POST, $opts['timeout'],
               self::API3_VERSION,
               ['auth' => $this->api_key . ':',
                'body' => $parameters,
            ]);

            return $request->send();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            return null;
        }
    }

     /**
     *  Retrieves a webhook when given an ID. 
     * @param integer $webhook_id :Webhook ID .  
     *
     * @param array $opts  Array of optional parameters for this request:
     *     - string 'account_id': by default, this client's account ID is used.
     *     - int 'timeout': By default, this client's timeout is used.
     *
     * @return null|SiftResponse
     */
    public function retrieveWebhook($webhook_id, $opts = []) {
        $this->mergeArguments($opts, [
            'account_id' => $this->account_id,
            'version' => self::API3_VERSION,
            'timeout' => $this->timeout
        ]);

        $this->validateArgument($this->account_id, 'account_id', 'string');
        $this->validateArgument($webhook_id, 'webhook_id', 'string');

        $url = ($this->webhookApiUrl($opts['version'], $opts['account_id']). '/'.rawurlencode($webhook_id));

        try {
            $request = new SiftRequest($url, 
                SiftRequest::GET, $opts['timeout'],
                self::API3_VERSION,
                ['auth' => $this->api_key . ':'
            ]);

            return $request->send();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            return null;
        }
    }

    /**
     *  Returns a list of all webhooks. 
     *
     * @param array $opts  Array of optional parameters for this request:
     *     - string 'account_id': by default, this client's account ID is used.
     *     - int 'timeout': By default, this client's timeout is used.
     *
     * @return null|SiftResponse
     */
    public function listAllWebhooks($opts = []) {
        $this->mergeArguments($opts, [
            'account_id' => $this->account_id,
            'version' => self::API3_VERSION,
            'timeout' => $this->timeout
        ]);

        $this->validateArgument($this->account_id, 'account_id', 'string');

        try {
            $request = new SiftRequest(self::webhookApiUrl($opts['version'], $opts['account_id']), 
                SiftRequest::GET, $opts['timeout'],
                self::API3_VERSION,
                ['auth' => $this->api_key . ':'
            ]);

            return $request->send();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            return null;
        }
    }

    /**
     * Updates a webhook when given an ID. This will overwrite the entire existing webhook object.
     * @param integer $webhook_id :Webhook ID .    
     *  
     * See https://sift.com/developers/docs/php/webhooks-api/update for valid $properties fields
     * @param array $properties An array of name-value pairs.  This parameter is required.
     * 
     * @param array $opts  Array of optional parameters for this request:
     *     - string 'account_id': by default, this client's account ID is used.
     *     - int 'timeout': By default, this client's timeout is used.
     *
     * @return null|SiftResponse
     */
    public function updateWebhook($webhook_id, $parameters, $opts = []) {
        $this->mergeArguments($opts, [
            'timeout' => $this->timeout,
            'version' => self::API3_VERSION,
            'account_id' => $this->account_id
        ]);

        $this->validateArgument($parameters, 'properties', 'array');
        $this->validateArgument($webhook_id, 'webhook_id', 'string');
        $this->validateArgument($this->account_id, 'account_id', 'string');
        $this->validateWebhookArgument($parameters);

        $url = ($this->webhookApiUrl($opts['version'], $opts['account_id']). '/'.rawurlencode($webhook_id));
        try {
            $request = new SiftRequest($url, SiftRequest::PUT, $opts['timeout'], self::API3_VERSION, ['auth' => $this->api_key . ':',
                'body' => $parameters,
            ]);
            return $request->send();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            return null;
        }
    }

    /**
     * Deletes a webhook when given an ID.
     * @param integer $webhook_id :Webhook ID .    
     *  
     * @param array $opts  Array of optional parameters for this request:
     *     - string 'account_id': by default, this client's account ID is used.
     *     - int 'timeout': By default, this client's timeout is used.
     *
     * @return null|SiftResponse
     */
    public function deleteWebhook($webhook_id, $opts = []) {
        $this->mergeArguments($opts, [
            'timeout' => $this->timeout,
            'version' => self::API3_VERSION,
            'account_id' => $this->account_id
        ]);

        $this->validateArgument($webhook_id, 'webhook_id', 'string');
        $this->validateArgument($this->account_id, 'account_id', 'string');

        $url = ($this->webhookApiUrl($opts['version'], $opts['account_id']). '/'.rawurlencode($webhook_id));
        try {
            $request = new SiftRequest($url, SiftRequest::DELETE, $opts['timeout'],
                 self::API3_VERSION, ['auth' => $this->api_key . ':'
            ]);
            return $request->send();
        } catch (Exception $e) {
            $this->logError($e->getMessage());
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
                throw new InvalidArgumentException("{$key} is not a valid argument.");
            }
        }
        $opts += $defaults;
    }

    private function validateArgument($arg, $name, $type) {
        // Validate type
        if (gettype($arg) != $type)
            throw new InvalidArgumentException("{$name} must be a {$type}.");

        // Check if empty
        if (empty($arg))
            throw new InvalidArgumentException("{$name} cannot be empty.");
    }

    private function validateMerchantArgument($parameters) {
        $this->validateArgument($parameters['id'], 'ID', 'string');
        $this->validateArgument($parameters['name'], 'name', 'string');
        $this->validateArgument($parameters['address']['address_1'], 'address_1', 'string');
        $this->validateArgument($parameters['address']['address_2'], 'address_2', 'string');
        $this->validateArgument($parameters['address']['city'], 'city', 'string');
        $this->validateArgument($parameters['address']['region'], 'region', 'string');
        $this->validateArgument($parameters['address']['country'], 'country', 'string');
        $this->validateArgument($parameters['address']['zipcode'], 'zipcode', 'string');
        $this->validateArgument($parameters['address']['phone'], 'phone', 'string');
        $this->validateArgument($parameters['category'], 'category', 'string');
        $this->validateArgument($parameters['service_level'], 'service_level', 'string');
        $this->validateArgument($parameters['status'], 'status', 'string');
        $this->validateArgument($parameters['risk_profile']['level'], 'level', 'string');
        $this->validateArgument($parameters['risk_profile']['score'], 'Score', 'integer');
        if (($parameters['risk_profile']['level'] != 'low') && ($parameters['risk_profile']['level'] != 'medium') && ($parameters['risk_profile']['level'] != 'high'))
            throw new InvalidArgumentException("Invalid Level");
        if ($parameters['status'] != 'churned' && $parameters['status'] != 'active' && $parameters['status'] != 'inactive' && $parameters['status'] != 'paused')
            throw new InvalidArgumentException("Invalid Status");
    }

    private function restApiUrl($version) {
        return self::urlPrefix($version) . '/events';
    }

    private function userLabelApiUrl($userId, $version) {
        return self::urlPrefix($version) . '/users/' . rawurlencode($userId) . '/labels';
    }

    private function scoreApiUrl($userId, $version) {
        return self::urlPrefix($version) . '/score/' . rawurlencode($userId);
    }

    private function userScoreApiUrl($userId, $version) {
        return self::urlPrefix($version) . '/users/' . urlencode($userId) . '/score';
    }

    private function checkApiUrl($version) {
        return self::urlPrefix($version) . '/verification/check';
    }

    private function userSendApiUrl($version) {
        return self::urlPrefix($version) . '/verification/send';
    }

    private function userResendApiUrl($version) {
        return self::urlPrefix($version) . '/verification/resend';
    }

    private function urlPrefix($version) {
        return $this->api_endpoint . '/v' . $version;
    }

    private function webhookApiUrl($version, $account_id) {       
        return  $this->urlPrefix($version) . '/accounts/'. rawurlencode($account_id). '/webhooks';
    }

    private function validateWebhookArgument($parameters) {
        $this->validateArgument($parameters['payload_type'], 'payload_type', 'string');
        $this->validateArgument($parameters['status'], 'status', 'string');
        $this->validateArgument($parameters['url'], 'url', 'string');
        $this->validateArgument($parameters['enabled_events'], 'enabled_events', 'array');
        if ($parameters['enabled_events'][0] != '$create_order' && $parameters['enabled_events'][0] != '$update_order' 
        && $parameters['enabled_events'][0] != '$order_status' && $parameters['enabled_events'][0] != '$transaction'
        && $parameters['enabled_events'][0] != '$chargeback')
            throw new InvalidArgumentException("Invalid Enabled Events");
        if ($parameters['status'] != 'draft' && $parameters['status'] != 'active' )
            throw new InvalidArgumentException("Invalid Status");
    }

}
