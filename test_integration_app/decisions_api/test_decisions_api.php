<?php
    class test_decisions_api
    { 
        private $client;
    
        function __construct() {
            $this->client = new SiftClient(array('api_key' => getenv("API_KEY"), 'account_id' => getenv("ACCOUNT_ID")));
        }

        function getUserDecisions()
        {
            return $this->client->getUserDecisions($GLOBALS['user_id']);
        }

        function getOrderDecisions()
        {
            return $this->client->getOrderDecisions('ORDER-28168441');
        }

        function getContentDecisions()
        {
            return $this->client->getContentDecisions($GLOBALS['user_id'], 'message-23412');
        }

        function getSessionDecisions()
        {
            return $this->client->getSessionDecisions($GLOBALS['user_id'], $GLOBALS['session_id']);
        }

        function getDecisions()
        {
            $options = array(
                'abuse_types' => array('payment_abuse', 'legacy'),
                'entity_type' => 'user',
                'from' => 5,
                'limit' => 10
            );

            return $this->client->getDecisions($options);
        }

        function apply_decision_to_user()
        {
            $options = array(
                'analyst' => 'analyst@example.com',
                'description' => 'User linked to three other payment abusers and ordering high value items'
            );

            return $this->client->applyDecisionToUser($GLOBALS['user_id'],
            'integration_app_watch_account_abuse',
            'MANUAL_REVIEW',
            $options);
        }

        function apply_decision_to_order()
        {
            $options = array(
                'analyst' => 'analyst@example.com',
                'description' => 'applied via the high priority queue, queued user because their risk score exceeded 85'
            );

            return $this->client->applyDecisionToOrder($GLOBALS['user_id'],
            'ORDER-28168441',
            'block_order_payment_abuse',
            'MANUAL_REVIEW',
            $options);
        }

        function apply_decision_to_session()
        {
            $options = array(
                'analyst' => 'analyst@example.com',
                'description' => 'compromised account reported to customer service
            ');

            return $this->client->applyDecisionToSession($GLOBALS['user_id'],
            $GLOBALS['session_id'],
            'integration_app_watch_account_takeover',
            'MANUAL_REVIEW',
            $options);
        }

        function apply_decision_to_content()
        {
            $options = array(
                'analyst' => 'analyst@example.com',
                'description' => 'compromised account reported to customer service'
            );

            return $this->client->applyDecisionToContent($GLOBALS['user_id'],
            'message-23412',
            'integration_app_watch_content_abuse',
            'MANUAL_REVIEW',
            $options);
        }

    }

?>
