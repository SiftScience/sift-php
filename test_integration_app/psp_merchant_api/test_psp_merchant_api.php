<?php
    class test_psp_merchant_api
    { 
        private $client;
    
        function __construct() {
            $this->client = new SiftClient(array('api_key' => getenv("API_KEY"), 'account_id' => getenv("ACCOUNT_ID")));
        }

        function create_merchant($merchant_id)
        {
            $merchantObject = array(
                'id' => $merchant_id,
                'name' => "Watson and Holmes",
                'description' => "An example of a PSP Merchant. Illustrative.",
                'address'=> array(
                    'name' => "Dr Watson",
                    'address_1' => "221B, Baker street",
                    'address_2' => "apt., 1",
                    'city' => "London",
                    'region' => "London",
                    'country' => "GB",
                    'zipcode' => "00001",
                    'phone' => "0122334455"
                ),
                'category' => "1002",
                'service_level' => "Platinum",
                'status' => "active",
                'risk_profile' => array(
                    'level' => "low",
                    'score' => 10
                )
            );
            return $this->client->postMerchant($merchantObject);
        }

        function update_merchant($merchant_id)
        {
            $merchantObject = array(
                'id' => $merchant_id,
                'name' => "Watson and Holmes updated",
                'description' => "An example of a PSP Merchant. Illustrative.",
                'address'=> array(
                    'name' => "Dr Watson updated",
                    'address_1' => "221B, Baker street",
                    'address_2' => "apt., 1",
                    'city' => "London",
                    'region' => "London",
                    'country' => "GB",
                    'zipcode' => "00001",
                    'phone' => "0122334455"
                ),
                'category' => "1002",
                'service_level' => "Platinum",
                'status' => "active",
                'risk_profile' => array(
                    'level' => "low",
                    'score' => 10
                )
            );
            return $this->client->putMerchant($merchant_id, $merchantObject);
        }

        function get_merchant($merchant_id)
        {
            return $this->client->getMerchant($merchant_id);
        }

        function get_all_merchants()
        {   
            $merchant_properties = array(
                'batch_token' => NULL,
                'batch_size' => 8,
            );
            return $this->client->merchants($merchant_properties);;
        }

    }

?>
