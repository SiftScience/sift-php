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
                '$user_id' => 'billy_jones_301@example.com', 
                '$user_email' => 'billy_jones_301@example.com'
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
