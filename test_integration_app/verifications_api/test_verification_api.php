<?php

    class test_verification_api
    { 
        private $client;
    
        function __construct() {
            $this->client = new SiftClient(array('api_key' => getenv("api_key")));
        }

        function send()
        {
            $send_properties = [
                '$user_id' => "billy_jones_301@example.com",
                '$send_to' => "billy_jones_301@example.com",
                '$verification_type' => '$email',
                '$brand_name' => "all",
                '$language' => "en",
                '$event' => [
                    '$session_id' => "gigtleqddo84l8cm15qe4il",
                    '$verified_event' => '$login',
                    '$reason' => '$automated_rule',
                    '$ip' => "192.168.1.1",
                    '$browser' => [
                        '$user_agent' =>
                            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36",
                    ],
                ],
            ];
            
            return $this->client->send($send_properties);
        }

        function resend()
        {
            $resend_properties = [
                '$user_id' => "billy_jones_301@example.com",
                '$send_to' => "billy_jones_301@example.com",
                '$verification_type' => '$email',
                '$brand_name' => "MyTopBrand",
                '$language' => "en",
                '$event' => [
                    '$session_id' => "gigtleqddo84l8cm15qe4il",
                    '$verified_event' => '$login',
                    '$reason' => '$automated_rule',
                    '$ip' => "192.168.1.1",
                    '$browser' => [
                        '$user_agent' =>
                            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36",
                    ],
                ],
            ];

            return $this->client->resend($resend_properties);
        }

        function check()
        {
            $check_properties = [
                '$user_id' => "billy_jones_301@example.com",
                '$code' => "404482",
                    '$verified_event' => '$login',
                    '$browser' => [
                        '$user_agent' =>
                            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36",
                    ],
            ];

            return $this->client->check($check_properties);
        }
    }

?>
