<?php
    class test_score_api
    { 
        private $client;
    
        function __construct() {
            $this->client = new SiftClient(array('api_key' => getenv("api_key"), 'account_id' => getenv("account_id")));
        }

        function user_score()
        {
           return $this->client->get_user_score($GLOBALS['user_id'],
                array('abuse_types' => array('payment_abuse')));
        }
      
    }
?>
