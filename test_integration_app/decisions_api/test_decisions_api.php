<?php
    class test_decisions_api
    { 
        private $client;
    
        function __construct() {
            $this->client = new SiftClient(array('api_key' => getenv("api_key"), 'account_id' => getenv("account_id")));
        }

        function getUserDecisions()
        {
            return $this->client->getUserDecisions('billy_jones_301');
        }

        function getOrderDecisions()
        {
            return $this->client->getOrderDecisions('ORDER-28168441');
        }

        function getContentDecisions()
        {
            return $this->client->getContentDecisions('billy_jones_301', 'message-23412');
        }

        function getSessionDecisions()
        {
            return $this->client->getSessionDecisions('billy_jones_301', 'gigtleqddo84l8cm15qe4il');
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

            return $this->client->applyDecisionToUser('billy_jones_301',
            'block_user_payment_abuse',
            'MANUAL_REVIEW',
            $options);
        }

        function apply_decision_to_order()
        {
            $options = array(
                'analyst' => 'analyst@example.com',
                'description' => 'applied via the high priority queue, queued user because their risk score exceeded 85'
            );

            return $this->client->applyDecisionToOrder('billy_jones_301',
            'ORDER-28168441',
            'user_looks_ok_payment_decision',
            'MANUAL_REVIEW',
            $options);
        }

        function apply_decision_to_session()
        {
            $options = array(
                'analyst' => 'analyst@example.com',
                'description' => 'compromised account reported to customer service
            ');

            return $this->client->applyDecisionToSession('billy_jones_301',
            'gigtleqddo84l8cm15qe4il',
            'session_looks_fraud_account_takeover',
            'MANUAL_REVIEW',
            $options);
        }

        function apply_decision_to_content()
        {
            $options = array(
                'analyst' => 'analyst@example.com',
                'description' => 'compromised account reported to customer service
            ');

            return $this->client->applyDecisionToContent('billy_jones_301',
            'message-23412',
            'content_looks_fraud_content_abuse',
            'MANUAL_REVIEW',
            $options);
        }

    }

?>