<?php
    class test_workflows_api
    { 
        private $client;
    
        function __construct() {
            $this->client = new SiftClient(array('api_key' => getenv("api_key"), 'account_id' => getenv("account_id")));
        }

        function synchronous_workflows()
        {
            $properties = array(
                '$user_id' => $GLOBALS['user_id'], 
                '$user_email' => $GLOBALS['user_email']
            );
            $opts = array(
                'return_workflow_status' => true,
                'return_route_info' => true,
                'abuse_types' =>  array(
                    'payment_abuse'
                )
            );

            return $this->client->track('$create_order', $properties, $opts);       
        }

    }

?>
