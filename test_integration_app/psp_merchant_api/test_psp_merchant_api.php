<?php

    class test_psp_merchant_api
    { 
        private $client;
    
        function __construct() {
            $this->client = new SiftClient(array('api_key' => getenv("api_key"), 'account_id' => getenv("account_id")));
        }

        function create_merchant()
        {
            $merchant_id = rand(1, 1000000); 
            $merchantObject = array(
                'id' => "merchant-id-php-".$merchant_id,
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

        function update_merchant()
        {
            $merchantObject = array(
                'id' => "merchant-id-php-0002",
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
            return $this->client->putMerchant("merchant-id-php-0002", $merchantObject);
        }

        function get_merchant()
        {   
            return $this->client->getMerchant("merchant-id-php-0001");;
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

