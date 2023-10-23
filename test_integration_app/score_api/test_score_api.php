<?php
    class test_score_api
    { 
        private $client;
    
        function __construct() {
            $this->client = new SiftClient(array('api_key' => getenv("API_KEY"), 'account_id' => getenv("ACCOUNT_ID")));
        }

        function user_score()
        {
           return $this->client->get_user_score($GLOBALS['user_id'],
                array('abuse_types' => array('payment_abuse')));
        }
      
    }
?>
