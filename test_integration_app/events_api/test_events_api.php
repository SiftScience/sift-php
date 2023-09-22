<?php
    
    class test_events_api
    { 
        private $client;
        
        function __construct() {
            $this->client = new SiftClient(array('api_key' => getenv("api_key"), 'account_id' => getenv("account_id")));
        }

        function add_item_to_cart()
        {           
            // Sample $add_item_to_cart event
            $add_item_to_cart_properties = array(
                // Required Fields
                '$user_id'           => 'billy_jones_301',
            
                // Supported Fields
                '$session_id' => 'gigtleqddo84l8cm15qe4il',
                '$item'       => array(
                '$item_id'        => 'B004834GQO',
                '$product_title'  => 'The Slanket Blanket-Texas Tea',
                '$price'          => 39990000, // $39.99
                '$currency_code'  => 'USD',
                '$upc'            => '6786211451001',
                '$sku'            => '004834GQ',
                '$brand'          => 'Slanket',
                '$manufacturer'   => 'Slanket',
                '$category'       => 'Blankets & Throws',
                '$tags'           => array('Awesome', 'Wintertime specials'),
                '$color'          => 'Texas Tea',
                '$quantity'       => 16
                ),
                '$brand_name'   => 'sift',
                '$site_domain'  => 'sift.com',
                '$site_country' => 'US',
                // Send this information from a BROWSER client.
                '$browser'    => array(
                '$user_agent'       =>  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
                '$accept_language'  => 'en-US',
                '$content_language' => 'en-GB'
                )
            );

            return $this->client->track('$add_item_to_cart', $add_item_to_cart_properties);
        }

        function add_promotion()
        {
            // Sample $add_promotion event
            $add_promotion_properties = array(
                // Required fields.
                '$user_id'    => 'billy_jones_301',
            
                // Supported fields.
                '$promotions' => array(
                // Example of a promotion for monetary discounts off good or services
                    array(
                        '$promotion_id'     => 'NewRideDiscountMay2016',
                        '$status'           => '$success',
                        '$description'      => '$5 off your first 5 rides',
                        '$referrer_user_id' => 'elon-m93903',
                        '$discount'         => array(
                        '$amount'         => 5000000,  // $5
                        '$currency_code'  => 'USD'
                        )
                    )
                ),

                // Send this information an APP client.
                '$app'        => array(
                // Example for the iOS Calculator app.
                '$os'                  => 'iOS',
                '$os_version'          => '10.1.3',
                '$device_manufacturer' => 'Apple',
                '$device_model'        => 'iPhone 4,2',
                '$device_unique_id'    => 'A3D261E4-DE0A-470B-9E4A-720F3D3D22E6',
                '$app_name'            => 'Calculator',
                '$app_version'         => '3.2.7',
                '$client_language'     => 'en-US'
                )
            );

            return $this->client->track('$add_promotion', $add_promotion_properties);
        }

        function chargeback()
        {
            // Sample $chargeback event
            $chargeback_properties = array(
                // Required Fields
                '$order_id'          => 'ORDER-123124124',
                '$transaction_id'    => '719637215',
            
                // Recommended Fields
                '$user_id'           => 'billy_jones_301',
                '$chargeback_state'  => '$lost',
                '$chargeback_reason' => '$duplicate'
            );

            return $this->client->track('$chargeback', $chargeback_properties);
        }

        function content_status()
        {
            // Sample $content_status event
            $content_status_properties = array(
                // Required Fields
                '$user_id'    => 'billy_jones_301',
                '$content_id' => '9671500641',
                '$status'     => '$paused',

                // Send this information an APP client.
                '$app'        => array(
                    // Example for the iOS Calculator app.
                    '$os'                  => 'iOS',
                    '$os_version'          => '10.1.3',
                    '$device_manufacturer' => 'Apple',
                    '$device_model'        => 'iPhone 4,2',
                    '$device_unique_id'    => 'A3D261E4-DE0A-470B-9E4A-720F3D3D22E6',
                    '$app_name'            => 'Calculator',
                    '$app_version'         => '3.2.7',
                    '$client_language'     => 'en-US'
                )
            );
    
            return $this->client->track('$content_status', $content_status_properties);
        }
    
        function create_account()
        {
            // Sample $create_account event
            $create_account_properties = array(
                // Required Fields
                '$user_id'    => 'billy_jones_301',
            
                // Supported Fields
                '$session_id'       => 'gigtleqddo84l8cm15qe4il',
                '$user_email'       => 'billy_jones_301@example.com',
                '$name'             => 'Bill Jones',
                '$phone'            => '1-415-555-6040',
                '$referrer_user_id' => 'janejane101',
                '$payment_methods'  => array(
                    array(
                        '$payment_type'    => '$credit_card',
                        '$card_bin'        => '542486',
                        '$card_last4'      => '4444'
                    )
                ),
                '$billing_address'  => array(
                    '$name'         => 'Bill Jones',
                    '$phone'        => '1-415-555-6040',
                    '$address_1'    => '2100 Main Street',
                    '$address_2'    => 'Apt 3B',
                    '$city'         => 'New London',
                    '$region'       => 'New Hampshire',
                    '$country'      => 'US',
                    '$zipcode'      => '03257'
                ),
                '$shipping_address' => array(
                    '$name'         => 'Bill Jones',
                    '$phone'        => '1-415-555-6041',
                    '$address_1'    => '2100 Main Street',
                    '$address_2'    => 'Apt 3B',
                    '$city'         => 'New London',
                    '$region'       => 'New Hampshire',
                    '$country'      => 'US',
                    '$zipcode'      => '03257'
                ),
                '$promotions'       => array(
                    array(
                        '$promotion_id'     => 'FriendReferral',
                        '$status'           => '$success',
                        '$referrer_user_id' => 'janejane102',
                        '$credit_point'     => array(
                                '$amount'             => 100,
                                '$credit_point_type'  => 'account karma'
                            ) 
                        )
                ),
        
                '$social_sign_on_type'   => '$twitter',
                '$account_types'         => ['merchant', 'premium'],

                // Suggested Custom Fields
                'twitter_handle'          => 'billyjones',
                'work_phone'              => '1-347-555-5921',
                'location'                => 'New London, NH',
                'referral_code'           => 'MIKEFRIENDS',
                'email_confirmed_status'  => '$pending',
                'phone_confirmed_status'  => '$pending',
            
                // Send this information from a BROWSER client.
                '$browser'    => array(
                '$user_agent'       =>  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
                '$accept_language'  => 'en-US',
                '$content_language' => 'en-GB'
                )
            );

            return $this->client->track('$create_account', $create_account_properties);
        }

        function create_content_comment()
        {
            // Sample $create_content event for comments
            $comment_properties = array(
                // Required fields
                '$user_id' => 'fyw3989sjpqr71',
                '$content_id' => 'comment-23412',
            
                // Recommended fields
                '$session_id'           => 'a234ksjfgn435sfg',
                '$status'               => '$active',
                '$ip'                   => '255.255.255.0',
            
                // Required $comment object
                '$comment'              => array(
                    '$body'               => 'Congrats on the new role!',
                    '$contact_email'      => 'alex_301@domain.com',
                    '$parent_comment_id'  => 'comment-23407',
                    '$root_content_id'    => 'listing-12923213',
                    '$images'             => array(
                        array(
                            '$md5_hash'            => '0cc175b9c0f1b6a831c399e269772661',
                            '$link'                => 'https://www.domain.com/file.png',
                            '$description'         => 'An old picture'
                        )
                    )
                ),
                // Send this information from a BROWSER client.
                '$browser'    => array(
                    '$user_agent'       =>  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
                    '$accept_language'  => 'en-US',
                    '$content_language' => 'en-GB'
                )
            );
    
            return $this->client->track('$create_content', $comment_properties);
        }

        function create_content_listing()
        {
            // Sample $create_content event for listings
            $listing_properties = array(
                // Required fields
                '$user_id'              => 'fyw3989sjpqr71',
                '$content_id'           => 'listing-23412',
            
                // Supported fields
                '$session_id'           => 'a234ksjfgn435sfg',
                '$status'               => '$active',
                '$ip'                   => '255.255.255.0',
            
                // Required $listing object
                '$listing'              => array(
                '$subject'            => '2 Bedroom Apartment for Rent',
                    '$body'             => 'Capitol Hill Seattle brand new condo. 2 bedrooms and 1 full bath.',
                    '$contact_email'    => 'alex_301@domain.com',
                    '$contact_address'  => array(
                    '$name'           => 'Bill Jones',
                    '$phone'          => '1-415-555-6041',
                    '$city'           => 'New London',
                    '$region'         => 'New Hampshire',
                    '$country'        => 'US',
                    '$zipcode'        => '03257'
                ),
                '$locations'          => array(
                    array(
                    '$city'                => 'Seattle',
                    '$region'              => 'Washington',
                    '$country'             => 'US',
                    '$zipcode'             => '98112'
                    )
                ),
                '$listed_items'       => array(
                    array(
                    '$price'               => 2950000000, // $2950.00
                    '$currency_code'       => 'USD',
                    '$tags'                => array('heat', 'washer/dryer')
                    )
                ),
                '$images'             => array(
                    array(
                    '$md5_hash'            => '0cc175b9c0f1b6a831c399e269772661',
                    '$link'                => 'https://www.domain.com/file.png',
                    '$description'         => 'Billys picture'
                    )
                ),
                '$expiration_time'    => 1549063157000 // UNIX timestamp in milliseconds
                ),
                    
                // Send this information an APP client.
                '$app'        => array(
                // Example for the iOS Calculator app.
                '$os'                  => 'iOS',
                '$os_version'          => '10.1.3',
                '$device_manufacturer' => 'Apple',
                '$device_model'        => 'iPhone 4,2',
                '$device_unique_id'    => 'A3D261E4-DE0A-470B-9E4A-720F3D3D22E6',
                '$app_name'            => 'Calculator',
                '$app_version'         => '3.2.7',
                '$client_language'     => 'en-US'
                )
            );

            return $this->client->track('$create_content', $listing_properties);
        }

        function create_content_message()
        {
            // Sample $create_content event for messages
            $message_properties = array(
                // Required fields
                '$user_id'               => 'fyw3989sjpqr71',
                '$content_id'            => 'message-23412',
            
                // Recommended fields
                '$session_id'            => 'a234ksjfgn435sfg',
                '$status'                => '$active',
                '$ip'                    => '255.255.255.0',
            
                // Required $message object
                '$message'               => array(
                    '$body'                => 'Let’s meet at 5pm',
                    '$contact_email'       => 'alex_301@domain.com',
                    '$recipient_user_ids'  => array('fy9h989sjphh71'),
                    '$images'              => array(
                        array(
                        '$md5_hash'             => '0cc175b9c0f1b6a831c399e269772661',
                        '$link'                 => 'https://www.domain.com/file.png',
                        '$description'          => 'My hike today!'
                        )
                    )
                ),
                    
                // Send this information an APP client.
                '$app'        => array(
                // Example for the iOS Calculator app.
                '$os'                  => 'iOS',
                '$os_version'          => '10.1.3',
                '$device_manufacturer' => 'Apple',
                '$device_model'        => 'iPhone 4,2',
                '$device_unique_id'    => 'A3D261E4-DE0A-470B-9E4A-720F3D3D22E6',
                '$app_name'            => 'Calculator',
                '$app_version'         => '3.2.7',
                '$client_language'     => 'en-US'
                )
            );
    
            return $this->client->track('$create_content', $message_properties);
        }

        function create_content_post()
        {
            // Sample $create_content event for posts
            $post_properties = array(
                // Required fields
                '$user_id' => 'fyw3989sjpqr71',
                '$content_id' => 'post-23412',
            
                // Recommended fields
                '$session_id'         => 'a234ksjfgn435sfg',
                '$status'             => '$active',
                '$ip'                 => '255.255.255.0',
            
                // Required $post object
                '$post'               => array(
                    '$subject'          => 'My new apartment!',
                    '$body'             => 'Moved into my new apartment yesterday.',
                    '$contact_email'    => 'alex_301@domain.com',
                    '$contact_address'  => array(
                        '$name'                => 'Bill Jones',
                        '$city'                => 'New London',
                        '$region'              => 'New Hampshire',
                        '$country'             => 'US',
                        '$zipcode'             => '03257'
                    ),
                    '$locations'        => array(
                        array(
                        '$city'              => 'Seattle',
                        '$region'            => 'Washington',
                        '$country'           => 'US',
                        '$zipcode'           => '98112'
                        )
                    ),
                    '$categories'       => array('Personal'),
                    '$images'           => array(
                        array(
                        '$md5_hash'          => '0cc175b9c0f1b6a831c399e269772661',
                        '$link'              => 'https://www.domain.com/file.png',
                        '$description'       => 'View from the window!'
                        )
                    ),
                    '$expiration_time'  => 1549063157000
                ),

                // Send this information an APP client.
                '$app'        => array(
                // Example for the iOS Calculator app.
                '$os'                  => 'iOS',
                '$os_version'          => '10.1.3',
                '$device_manufacturer' => 'Apple',
                '$device_model'        => 'iPhone 4,2',
                '$device_unique_id'    => 'A3D261E4-DE0A-470B-9E4A-720F3D3D22E6',
                '$app_name'            => 'Calculator',
                '$app_version'         => '3.2.7',
                '$client_language'     => 'en-US'
                )
            );
        
            return $this->client->track('$create_content', $post_properties);
        }

        function create_content_profile()
        {
            // Sample $create_content event for profiles
            $profile_properties = array(
                // Required fields
                '$user_id'            => 'fyw3989sjpqr71',
                '$content_id'         => 'profile-23412',
            
                // Recommended fields
                '$session_id'         => 'a234ksjfgn435sfg',
                '$status'             => '$active',
                '$ip'                 => '255.255.255.0',
            
                // Required $profile object
                '$profile'            => array(
                    '$body'             => 'Hi! My name is Alex and I just moved to New London!',
                    '$contact_email'    => 'alex_301@domain.com',
                    '$contact_address'  => array(
                        '$name'           => 'Alex Smith',
                        '$phone'          => '1-415-555-6041',
                        '$city'           => 'New London',
                        '$region'         => 'New Hampshire',
                        '$country'        => 'US',
                        '$zipcode'        => '03257'
                    ),
                    '$images'           => array(
                        array(
                        '$md5_hash'          => '0cc175b9c0f1b6a831c399e269772661',
                        '$link'              => 'https://www.domain.com/file.png',
                        '$description'       => 'Alexs picture'
                        )
                    ),
                    '$categories'       => array(
                        'Friends',
                        'Long-term dating'
                    )
                ),

                // Send this information an APP client.
                '$app'        => array(
                // Example for the iOS Calculator app.
                '$os'                  => 'iOS',
                '$os_version'          => '10.1.3',
                '$device_manufacturer' => 'Apple',
                '$device_model'        => 'iPhone 4,2',
                '$device_unique_id'    => 'A3D261E4-DE0A-470B-9E4A-720F3D3D22E6',
                '$app_name'            => 'Calculator',
                '$app_version'         => '3.2.7',
                '$client_language'     => 'en-US'
                )
            );
    
            return $this->client->track('$create_content', $profile_properties);
        }
    
        function create_content_review()
        {
            // Sample $create_content event for reviews
            $review_properties = array(
                // Required fields
                '$user_id'                => 'fyw3989sjpqr71',
                '$content_id'             => 'review-23412',
            
                // Recommended fields
                '$session_id'             => 'a234ksjfgn435sfg',
                '$status'                 => '$active',
                '$ip'                     => '255.255.255.0',
            
                // Required $review object
                '$review'                 => array(
                    '$subject'              => 'Amazing Tacos!',
                    '$body'                 => 'I ate the tacos.',
                    '$contact_email'        => 'alex_301@domain.com',
                    '$locations'            => array(
                        array(
                        '$city'                 => 'Seattle',
                        '$region'               => 'Washington',
                        '$country'              => 'US',
                        '$zipcode'              => '98112'
                        )
                    ),
                    '$reviewed_content_id'  => 'listing-234234',
                    '$images'               => array(
                        array(
                        '$md5_hash'             => '0cc175b9c0f1b6a831c399e269772661',
                        '$link'                 => 'https://www.domain.com/file.png',
                        '$description'          => 'Calamari tacos.'
                        )
                    ),
                    '$rating'               => 4.5
                ),
            
                // Send this information an APP client.
                '$app'        => array(
                // Example for the iOS Calculator app.
                '$os'                  => 'iOS',
                '$os_version'          => '10.1.3',
                '$device_manufacturer' => 'Apple',
                '$device_model'        => 'iPhone 4,2',
                '$device_unique_id'    => 'A3D261E4-DE0A-470B-9E4A-720F3D3D22E6',
                '$app_name'            => 'Calculator',
                '$app_version'         => '3.2.7',
                '$client_language'     => 'en-US'
                )
            );

            return $this->client->track('$create_content', $review_properties);
        }

        function create_order()
        {
            // Sample $create_order event
            $properties = array(
                // Required Fields
                '$user_id'          => 'billy_jones_301',
                // Supported Fields
                '$session_id'       => 'gigtleqddo84l8cm15qe4il',
                '$order_id'         => 'ORDER-28168441',
                '$user_email'       => 'bill@gmail.com',
                '$verification_phone_number' => "+123456789012",
                '$amount'           => 115940000, // $115.94
                '$currency_code'    => 'USD',
                '$billing_address'  => array(
                    '$name'         => 'Bill Jones',
                    '$phone'        => '1-415-555-6041',
                    '$address_1'    => '2100 Main Street',
                    '$address_2'    => 'Apt 3B',
                    '$city'         => 'New London',
                    '$region'       => 'New Hampshire',
                    '$country'      => 'US',
                    '$zipcode'      => '03257'
                ),
                '$payment_methods'  =>array(
                    array(
                        '$payment_type'    => '$credit_card',
                        '$payment_gateway' => '$braintree',
                        '$card_bin'        => '542486',
                        '$card_last4'      => '4444'
                    )
                ),
                '$ordered_from' => array(
                    '$store_id'      => '123',
                    '$store_address' => array(
                        '$name'       => 'Bill Jones',
                        '$phone'      => '1-415-555-6040',
                        '$address_1'  => '2100 Main Street',
                        '$address_2'  => 'Apt 3B',
                        '$city'       => 'New London',
                        '$region'     => 'New Hampshire',
                        '$country'    => 'US',
                        '$zipcode'    => '03257'
                    )
                ),
                '$brand_name'   => 'sift',
                '$site_domain'  => 'sift.com',
                '$site_country' => 'US',
                '$shipping_address'  => array(
                    '$name'          => 'Bill Jones',
                    '$phone'         => '1-415-555-6041',
                    '$address_1'     => '2100 Main Street',
                    '$address_2'     => 'Apt 3B',
                    '$city'          => 'New London',
                    '$region'        => 'New Hampshire',
                    '$country'       => 'US',
                    '$zipcode'       => '03257'
                ),
                '$expedited_shipping'       => true,
                '$shipping_method'          => '$physical',
                '$shipping_carrier'         => 'UPS',
                '$shipping_tracking_numbers'=> array('1Z204E380338943508', '1Z204E380338943509'),
                '$items'                    => array(
                    // A list of items
                    array(
                        '$item_id'        => '12344321',
                        '$product_title'  => 'Microwavable Kettle Corn: Original Flavor',
                        '$price'          => 4990000, // $4.99
                        '$upc'            => '097564307560',
                        '$sku'            => '03586005',
                        '$brand'          => 'Peters Kettle Corn',
                        '$manufacturer'   => 'Peters Kettle Corn',
                        '$category'       => 'Food and Grocery',
                        '$tags'           => array('Popcorn', 'Snacks', 'On Sale'),
                        '$quantity'       => 4
                    ),
                    array(
                        '$item_id'        => 'B004834GQO',
                        '$product_title'  => 'The Slanket Blanket-Texas Tea',
                        '$price'          => 39990000, // $39.99
                        '$upc'            => '6786211451001',
                        '$sku'            => '004834GQ',
                        '$brand'          => 'Slanket',
                        '$manufacturer'   => 'Slanket',
                        '$category'       => 'Blankets & Throws',
                        '$tags'           => array('Awesome', 'Wintertime specials'),
                        '$color'          => 'Texas Tea',
                        '$quantity'       => 2
                    )
                ),
                // For marketplaces, use $seller_user_id to identify the seller
                '$seller_user_id'     => 'slinkys_emporium',
            
                '$promotions'         => array(
                    array(
                        '$promotion_id' => 'FirstTimeBuyer',
                        '$status'       => '$success',
                        '$description'  => '$5 off',
                        '$discount'     => array(
                        '$amount'                   => 5000000,  // $5.00
                        '$currency_code'            => 'USD',
                        '$minimum_purchase_amount'  => 25000000  // $25.00
                        )
                    )
                ),
            
                // Sample Custom Fields
                'digital_wallet'      => 'apple_pay', // 'google_wallet', etc.
                'coupon_code'         => 'dollarMadness',
                'shipping_choice'     => 'FedEx Ground Courier',
                'is_first_time_buyer' => false,
                    
                // Send this information an APP client.
                '$app'        => array(
                    // Example for the iOS Calculator app.
                    '$os'                  => 'iOS',
                    '$os_version'          => '10.1.3',
                    '$device_manufacturer' => 'Apple',
                    '$device_model'        => 'iPhone 4,2',
                    '$device_unique_id'    => 'A3D261E4-DE0A-470B-9E4A-720F3D3D22E6',
                    '$app_name'            => 'Calculator',
                    '$app_version'         => '3.2.7',
                    '$client_language'     => 'en-US'
                )
            );
    
            return $this->client->track('$create_order', $properties);
        }

        function flag_content()
        {
            // Sample $flag_content event
            $properties = array(
                // Required Fields
                '$user_id'    => 'billy_jones_301', // content creator
                '$content_id' => '9671500641',
            
                // Supported Fields
                '$flagged_by' => 'jamieli89'
            );
    
            return $this->client->track('$flag_content', $properties);
        }

        function link_session_to_user()
        {
            // Sample $link_session_to_user event
            $properties = array(
                // Required Fields
                '$user_id'    => 'billy_jones_301',
                '$session_id' => 'gigtleqddo84l8cm15qe4il'
            );

            return $this->client->track('$link_session_to_user', $properties);
        }

        function login()
        {
            // Sample $login event
            $properties = array(
                // Required Fields
                '$user_id'      => 'billy_jones_301',
                '$login_status' => '$failure',
            
                '$session_id' => 'gigtleqddo84l8cm15qe4il',
                '$ip'         => '128.148.1.135',
            
                // Optional Fields
                '$user_email'     => 'billy_jones_301@example.com',
                '$verification_phone_number' => '+123456789012',
                '$failure_reason' => '$account_unknown',
                '$username'       => 'billy_jones_301@example.com',
                '$account_types'  => ['merchant','premium'],
                '$social_sign_on_type' => '$google',
                '$brand_name'   => 'sift',
                '$site_domain'  => 'sift.com',
                '$site_country' => 'US',
            
                // Send this information with a login from a BROWSER client.
                '$browser'    => array(
                '$user_agent'       =>  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
                '$accept_language'  => 'en-US',
                '$content_language' => 'en-GB'
                )
            );
    
            return $this->client->track('$login', $properties);
        }

        function logout()
        {
            // Sample $logout event
            $properties = array(
                // Required Fields
                '$user_id'   => 'billy_jones_301',
            
                // Send this information from a BROWSER client.
                '$browser'    => array(
                '$user_agent'       =>  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
                '$accept_language'  => 'en-US',
                '$content_language' => 'en-GB'
                )
            );
    
            return $this->client->track('$logout', $properties);
        }

        function order_status()
        {
            // Sample $order_status event
            $properties = array(
                // Required Fields
                '$user_id'          => 'billy_jones_301',
                '$order_id'         => 'ORDER-28168441',
                '$order_status'     => '$canceled',
            
                // Optional Fields
                '$reason'           => '$payment_risk',
                '$source'           => '$manual_review',
                '$analyst'          => 'someone@your-site.com',
                '$webhook_id'       => '3ff1082a4aea8d0c58e3643ddb7a5bb87ffffeb2492dca33',
                '$description'      => 'Canceling because multiple fraudulent users on device',
            
                // Send this information from a BROWSER client.
                '$browser'    => array(
                '$user_agent'       =>  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
                '$accept_language'  => 'en-US',
                '$content_language' => 'en-GB'
                )
            );

            return $this->client->track('$order_status', $properties);
        }

        function remove_item_from_cart()
        {
            // Sample $remove_item_from_cart event
            $properties = array(
                // Required Fields
                '$user_id'    => 'billy_jones_301',
            
                // Supported Fields
                '$session_id' => 'gigtleqddo84l8cm15qe4il',
                '$item'       => array(
                    '$item_id'        => 'B004834GQO',
                    '$product_title'  => 'The Slanket Blanket-Texas Tea',
                    '$price'          => 39990000, // $39.99
                    '$currency_code'  => 'USD',
                    '$quantity'       => 2,
                    '$upc'            => '6786211451001',
                    '$sku'            => '004834GQ',
                    '$brand'          => 'Slanket',
                    '$manufacturer'   => 'Slanket',
                    '$category'       => 'Blankets & Throws',
                    '$tags'           => array('Awesome', 'Wintertime specials'),
                    '$color'          => 'Texas Tea'
                ),
            
                // Send this information from a BROWSER client.
                '$browser'    => array(
                    '$user_agent'       =>  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
                    '$accept_language'  => 'en-US',
                    '$content_language' => 'en-GB'
                )
            );  

            return $this->client->track('$remove_item_from_cart', $properties);
        }

        function security_notification()
        {
            // Sample $security_notification event
            $properties = array(
                // Required Fields
                '$user_id'    => 'billy_jones_301',
                '$session_id' => 'gigtleqddo84l8cm15qe4il',
                '$notification_status'     => '$sent',
                // Optional fields if applicable
                '$notification_type' => '$email',
                '$notified_value'    => 'billy123@domain.com',
            
                // Send this information from a BROWSER client.
                '$browser'    => array(
                '$user_agent'       =>  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
                '$accept_language'  => 'en-US',
                '$content_language' => 'en-GB'
                )
            );

            return $this->client->track('$security_notification', $properties);
        }

        function transaction()
        {
            // Sample $transaction event
            $properties = array(
                // Required Fields
                '$user_id'          => 'billy_jones_301',
                '$amount'           => 506790000, // $506.79
                '$currency_code'    => 'USD',
            
                // Supported Fields
                '$user_email'       => 'bill@gmail.com',
                '$transaction_type' => '$sale',
                '$transaction_status' => '$failure',
                '$decline_category' => '$bank_decline',
                '$order_id'         => 'ORDER-123124124',
                '$transaction_id'   => '719637215',
            
                '$billing_address'  => array( // or "$sent_address" // or "$received_address"
                    '$name'         => 'Bill Jones',
                    '$phone'        => '1-415-555-6041',
                    '$address_1'    => '2100 Main Street',
                    '$address_2'    => 'Apt 3B',
                    '$city'         => 'New London',
                    '$region'       => 'New Hampshire',
                    '$country'      => 'US',
                    '$zipcode'      => '03257'
                ),
                '$brand_name'   => 'sift',
                '$site_domain'  => 'sift.com',
                '$site_country' => 'US',
                '$ordered_from' => array(
                    '$store_id'      => '123',
                    '$store_address' => array(
                        '$name'       => 'Bill Jones',
                        '$phone'      => '1-415-555-6040',
                        '$address_1'  => '2100 Main Street',
                        '$address_2'  => 'Apt 3B',
                        '$city'       => 'New London',
                        '$region'     => 'New Hampshire',
                        '$country'    => 'US',
                        '$zipcode'    => '03257'
                    )
                ),
                // Credit card example
                '$payment_method'   => array(
                    '$payment_type'    => '$credit_card',
                    '$payment_gateway' => '$braintree',
                    '$card_bin'        => '542486',
                    '$card_last4'      => '4444'
                ),
            
                // Supported fields for 3DS
                '$status_3ds'                     => '$attempted',
                '$triggered_3ds'                  => '$processor',
                '$merchant_initiated_transaction' => false,
            
                // Supported Fields
                '$shipping_address' => array(
                    '$name'         => 'Bill Jones',
                    '$phone'        => '1-415-555-6041',
                    '$address_1'    => '2100 Main Street',
                    '$address_2'    => 'Apt 3B',
                    '$city'         => 'New London',
                    '$region'       => 'New Hampshire',
                    '$country'      => 'US',
                    '$zipcode'      => '03257'
                ),
                '$session_id'       => 'gigtleqddo84l8cm15qe4il',
            
                // For marketplaces, use $seller_user_id to identify the seller
                '$seller_user_id'     => 'slinkys_emporium',
            
                // Sample Custom Fields
                'digital_wallet'      => 'apple_pay', // 'google_wallet', etc.
                'coupon_code'         => 'dollarMadness',
                'shipping_choice'     => 'FedEx Ground Courier',
                'is_first_time_buyer' => false,
            
                // Send this information from a BROWSER client.
                '$browser'    => array(
                '$user_agent'       =>  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
                '$accept_language'  => 'en-US',
                '$content_language' => 'en-GB'
                )
            );
                    
            return $this->client->track('$transaction', $properties);
        }

        function update_account()
        {
            // Sample $update_account event
            $properties = array(
                // Required Fields
                '$user_id'    => 'billy_jones_301',
                // Supported Fields
                '$changed_password' => True,
                '$user_email'       => 'bill@gmail.com',
                '$name'             => 'Bill Jones',
                '$phone'            => '1-415-555-6040',
                '$referrer_user_id' => 'janejane102',
                '$payment_methods'  => array(
                    array(
                        '$payment_type'    => '$credit_card',
                        '$card_bin'        => '542486',
                        '$card_last4'      => '4444'
                    )
                ),
                '$billing_address'  => array(
                    '$name'         => 'Bill Jones',
                    '$phone'        => '1-415-555-6041',
                    '$address_1'    => '2100 Main Street',
                    '$address_2'    => 'Apt 3B',
                    '$city'         => 'New London',
                    '$region'       => 'New Hampshire',
                    '$country'      => 'US',
                    '$zipcode'      => '03257'
                ),
                '$shipping_address' => array(
                    '$name'         => 'Bill Jones',
                    '$phone'        => '1-415-555-6041',
                    '$address_1'    => '2100 Main Street',
                    '$address_2'    => 'Apt 3B',
                    '$city'         => 'New London',
                    '$region'       => 'New Hampshire',
                    '$country'      => 'US',
                    '$zipcode'      => '03257'
                ),
                '$social_sign_on_type'   => '$twitter',
                '$account_types'         => ['merchant', 'premium'],
            
                // Send this information from a BROWSER client.
                '$browser'    => array(
                '$user_agent'       =>  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
                '$accept_language'  => 'en-US',
                '$content_language' => 'en-GB'
                )
            );

            return $this->client->track('$update_account', $properties);
        }

        function update_content_comment()
        {
            // Sample $update_content event for comments
            $comment_properties = array(
                // Required fields
                '$user_id' => 'fyw3989sjpqr71',
                '$content_id' => 'comment-23412',
            
                // Recommended fields
                '$session_id'           => 'a234ksjfgn435sfg',
                '$status'               => '$active',
                '$ip'                   => '255.255.255.0',
            
                // Required $comment object
                '$comment'              => array(
                    '$body'               => 'Congrats on the new role!',
                    '$contact_email'      => 'alex_301@domain.com',
                    '$parent_comment_id'  => 'comment-23407',
                    '$root_content_id'    => 'listing-12923213',
                    '$images'             => array(
                        array(
                        '$md5_hash'            => '0cc175b9c0f1b6a831c399e269772661',
                        '$link'                => 'https://www.domain.com/file.png',
                        '$description'         => 'An old picture'
                        )
                    )
                ),
            
                // Send this information from a BROWSER client.
                '$browser'    => array(
                '$user_agent'       =>  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
                '$accept_language'  => 'en-US',
                '$content_language' => 'en-GB'
                )
            );
                    
            return $this->client->track('$update_content', $comment_properties);
        }

        function update_content_listing()
        {
            // Sample $update_content event for listings
            $listing_properties = array(
                // Required fields
                '$user_id'            => 'fyw3989sjpqr71',
                '$content_id'         => 'listing-23412',
            
                // Supported fields
                '$session_id'         => 'a234ksjfgn435sfg',
                '$status'             => '$active',
                '$ip'                 => '255.255.255.0',
            
                // Required $listing object
                '$listing'            => array(
                '$subject'          => '2 Bedroom Apartment for Rent',
                '$body'             => 'Capitol Hill Seattle brand new condo. 2 bedrooms and 1 full bath.',
                '$contact_email'    => 'alex_301@domain.com',
                '$contact_address'  => array(
                    '$name'           => 'Bill Jones',
                    '$phone'          => '1-415-555-6041',
                    '$city'           => 'New London',
                    '$region'         => 'New Hampshire',
                    '$country'        => 'US',
                    '$zipcode'        => '03257'
                ),
                '$locations'        => array(
                    array(
                    '$city'             => 'Seattle',
                    '$region'           => 'Washington',
                    '$country'          => 'US',
                    '$zipcode'          => '98112'
                    )
                ),
                '$listed_items'     => array(
                    array(
                    '$price'             => 2950000000, // $2950.00
                    '$currency_code'     => 'USD',
                    '$tags'              => array('heat', 'washer/dryer')
                    )
                ),
                '$images'           => array(
                    array(
                    '$md5_hash'          => '0cc175b9c0f1b6a831c399e269772661',
                    '$link'              => 'https://www.domain.com/file.png',
                    '$description'       => 'Billy’s picture'
                    )
                ),
                '$expiration_time'  => 1549063157000 // UNIX timestamp in milliseconds
                ),
            
                // Send this information from a BROWSER client.
                '$browser'    => array(
                '$user_agent'       =>  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
                '$accept_language'  => 'en-US',
                '$content_language' => 'en-GB'
                )
            );
    
            return $this->client->track('$update_content', $listing_properties);
        }

        function update_content_message()
        {
            // Sample $update_content event for messages
            $message_properties = array(
                // Required fields
                '$user_id'               => 'fyw3989sjpqr71',
                '$content_id'            => 'message-23412',
            
                // Recommended fields
                '$session_id'            => 'a234ksjfgn435sfg',
                '$status'                => '$active',
                '$ip'                    => '255.255.255.0',
            
                // Required $message object
                '$message'               => array(
                    '$body'                => 'Let’s meet at 5pm',
                    '$contact_email'       => 'alex_301@domain.com',
                    '$recipient_user_ids'  => array('fy9h989sjphh71'),
                    '$images'              => array(
                        array(
                        '$md5_hash'             => '0cc175b9c0f1b6a831c399e269772661',
                        '$link'                 => 'https://www.domain.com/file.png',
                        '$description'          => 'My hike today!'
                        )
                    )
                ),
            
                // Send this information from a BROWSER client.
                '$browser'    => array(
                '$user_agent'       =>  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
                '$accept_language'  => 'en-US',
                '$content_language' => 'en-GB'
                )
            );
    
            return $this->client->track('$update_content', $message_properties);
        }

        function update_content_post()
        {
            // Sample $update_content event for posts
            $post_properties = array(
                // Required fields
                '$user_id' => 'fyw3989sjpqr71',
                '$content_id' => 'post-23412',
            
                // Recommended fields
                '$session_id'         => 'a234ksjfgn435sfg',
                '$status'             => '$active',
                '$ip'                 => '255.255.255.0',
            
                // Required $post object
                '$post'               => array(
                    '$subject'          => 'My new apartment!',
                    '$body'             => 'Moved into my new apartment yesterday.',
                    '$contact_email'    => 'alex_301@domain.com',
                    '$contact_address'  => array(
                        '$name'                => 'Bill Jones',
                        '$city'                => 'New London',
                        '$region'              => 'New Hampshire',
                        '$country'             => 'US',
                        '$zipcode'             => '03257'
                    ),
                    '$locations'        => array(
                        array(
                        '$city'              => 'Seattle',
                        '$region'            => 'Washington',
                        '$country'           => 'US',
                        '$zipcode'           => '98112'
                        )
                    ),
                    '$categories'       => array('Personal'),
                    '$images'           => array(
                        array(
                        '$md5_hash'          => '0cc175b9c0f1b6a831c399e269772661',
                        '$link'              => 'https://www.domain.com/file.png',
                        '$description'       => 'View from the window!'
                        )
                    ),
                    '$expiration_time'  => 1549063157000
                ),
            
                // Send this information from a BROWSER client.
                '$browser'    => array(
                    '$user_agent'       =>  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
                    '$accept_language'  => 'en-US',
                    '$content_language' => 'en-GB'
                )
            );
            
            return $this->client->track('$update_content', $post_properties);
        }

        function update_content_profile()
        {
            // Sample $update_content event for profiles
            $profile_properties = array(
                // Required fields
                '$user_id'            => 'fyw3989sjpqr71',
                '$content_id'         => 'profile-23412',
            
                // Recommended fields
                '$session_id'         => 'a234ksjfgn435sfg',
                '$status'             => '$active',
                '$ip'                 => '255.255.255.0',
            
                // Required $profile object
                '$profile'            => array(
                    '$body'             => 'Hi! My name is Alex and I just moved to New London!',
                    '$contact_email'    => 'alex_301@domain.com',
                    '$contact_address'  => array(
                        '$name'           => 'Alex Smith',
                        '$phone'          => '1-415-555-6041',
                        '$city'           => 'New London',
                        '$region'         => 'New Hampshire',
                        '$country'        => 'US',
                        '$zipcode'        => '03257'
                    ),
                    '$images'           => array(
                        array(
                        '$md5_hash'          => '0cc175b9c0f1b6a831c399e269772661',
                        '$link'              => 'https://www.domain.com/file.png',
                        '$description'       => 'Alex’s picture'
                        )
                    ),
                    '$categories'       => array(
                        'Friends',
                        'Long-term dating'
                    )
                ),
            
                // Send this information from a BROWSER client.
                '$browser'    => array(
                    '$user_agent'       =>  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
                    '$accept_language'  => 'en-US',
                    '$content_language' => 'en-GB'
                )
            );
    
            return $this->client->track('$update_content', $profile_properties);
        }

        function update_content_review()
        {
            // Sample $update_content event for reviews
            $review_properties = array(
                // Required fields
                '$user_id'                => 'fyw3989sjpqr71',
                '$content_id'             => 'review-23412',
            
                // Recommended fields
                '$session_id'             => 'a234ksjfgn435sfg',
                '$status'                 => '$active',
                '$ip'                     => '255.255.255.0',
            
                // Required $review object
                '$review'                 => array(
                    '$subject'              => 'Amazing Tacos!',
                    '$body'                 => 'I ate the tacos.',
                    '$contact_email'        => 'alex_301@domain.com',
                    '$locations'            => array(
                        array(
                        '$city'                 => 'Seattle',
                        '$region'               => 'Washington',
                        '$country'              => 'US',
                        '$zipcode'              => '98112'
                        )
                    ),
                    '$reviewed_content_id'  => 'listing-234234',
                    '$images'               => array(
                        array(
                        '$md5_hash'             => '0cc175b9c0f1b6a831c399e269772661',
                        '$link'                 => 'https://www.domain.com/file.png',
                        '$description'          => 'Calamari tacos.'
                        )
                    ),
                    '$rating'               => 4.5
                ),
            
                // Send this information from a BROWSER client.
                '$browser'    => array(
                    '$user_agent'       =>  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
                    '$accept_language'  => 'en-US',
                    '$content_language' => 'en-GB'
                )
            );
            
            return $this->client->track('$update_content', $review_properties);
        }

        function update_order()
        {
            // Sample $update_order event
            $properties = array(
                // Required Fields
                '$user_id'          => 'billy_jones_301',
                // Supported Fields
                '$session_id'       => 'gigtleqddo84l8cm15qe4il',
                '$order_id'         => 'ORDER-28168441',
                '$user_email'       => 'bill@gmail.com',
                '$amount'           => 115940000, // $115.94
                '$currency_code'    => 'USD',
                '$billing_address'  => array(
                    '$name'         => 'Bill Jones',
                    '$phone'        => '1-415-555-6041',
                    '$address_1'    => '2100 Main Street',
                    '$address_2'    => 'Apt 3B',
                    '$city'         => 'New London',
                    '$region'       => 'New Hampshire',
                    '$country'      => 'US',
                    '$zipcode'      => '03257'
                ),
                '$payment_methods'  =>array(
                    array(
                        '$payment_type'    => '$credit_card',
                        '$payment_gateway' => '$braintree',
                        '$card_bin'        => '542486',
                        '$card_last4'      => '4444'
                    )
                ),
                '$brand_name'   => 'sift',
                '$site_domain'  => 'sift.com',
                '$site_country' => 'US',
                '$ordered_from' => array(
                    '$store_id'      => '123',
                    '$store_address' => array(
                        '$name'       => 'Bill Jones',
                        '$phone'      => '1-415-555-6040',
                        '$address_1'  => '2100 Main Street',
                        '$address_2'  => 'Apt 3B',
                        '$city'       => 'New London',
                        '$region'     => 'New Hampshire',
                        '$country'    => 'US',
                        '$zipcode'    => '03257'
                    )
                ),
                '$shipping_address'  => array(
                    '$name'          => 'Bill Jones',
                    '$phone'         => '1-415-555-6041',
                    '$address_1'     => '2100 Main Street',
                    '$address_2'     => 'Apt 3B',
                    '$city'          => 'New London',
                    '$region'        => 'New Hampshire',
                    '$country'       => 'US',
                    '$zipcode'       => '03257'
                ),
                '$expedited_shipping'       => True,
                '$shipping_method'          => '$physical',
                '$shipping_carrier'         => 'UPS',
                '$shipping_tracking_numbers'=> array('1Z204E380338943508', '1Z204E380338943509'),
                '$items'                    => array(
                    // A list of items
                    array(
                        '$item_id'        => '12344321',
                        '$product_title'  => 'Microwavable Kettle Corn: Original Flavor',
                        '$price'          => 4990000, // $4.99
                        '$upc'            => '097564307560',
                        '$sku'            => '03586005',
                        '$brand'          => 'Peters Kettle Corn',
                        '$manufacturer'   => 'Peters Kettle Corn',
                        '$category'       => 'Food and Grocery',
                        '$tags'           => array('Popcorn', 'Snacks', 'On Sale'),
                        '$quantity'       => 4
                    ),
                    array(
                        '$item_id'        => 'B004834GQO',
                        '$product_title'  => 'The Slanket Blanket-Texas Tea',
                        '$price'          => 39990000, // $39.99
                        '$upc'            => '6786211451001',
                        '$sku'            => '004834GQ',
                        '$brand'          => 'Slanket',
                        '$manufacturer'   => 'Slanket',
                        '$category'       => 'Blankets & Throws',
                        '$tags'           => array('Awesome', 'Wintertime specials'),
                        '$color'          => 'Texas Tea',
                        '$quantity'       => 2
                    )
                ),
                // For marketplaces, use $seller_user_id to identify the seller
                '$seller_user_id'     => 'slinkys_emporium',
            
                '$promotions'         => array(
                    array(
                        '$promotion_id' => 'FirstTimeBuyer',
                        '$status'       => '$success',
                        '$description'  => '$5 off',
                        '$discount'     => array(
                        '$amount'                   => 5000000,  // $5.00
                        '$currency_code'            => 'USD',
                        '$minimum_purchase_amount'  => 25000000  // $25.00
                        )
                    )
                ),
            
                // Sample Custom Fields
                'digital_wallet'      => 'apple_pay', // 'google_wallet', etc.
                'coupon_code'         => 'dollarMadness',
                'shipping_choice'     => 'FedEx Ground Courier',
                'is_first_time_buyer' => False,
            
                // Send this information from a BROWSER client.
                '$browser'    => array(
                    '$user_agent'       =>  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
                    '$accept_language'  => 'en-US',
                    '$content_language' => 'en-GB'
                )
            );
            
            return $this->client->track('$update_order', $properties);
        }

        function update_password()
        {
            // Sample $update_password event
            $properties = array(
                // Required Fields
                '$user_id'   => 'billy_jones_301',
                '$session_id' => 'gigtleqddo84l8cm15qe4il',
                '$status'     => '$success',
                '$reason'     => '$forced_reset',
                '$ip'         => '128.148.1.135',     // IP of the user that entered the new password after the old password was reset
            
                // Send this information from a BROWSER client.
                '$browser'    => array(
                    '$user_agent'       =>  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
                    '$accept_language'  => 'en-US',
                    '$content_language' => 'en-GB'
                )
            );
    
            return $this->client->track('$update_password', $properties);
        }

        function verification()
        {
            // Sample $verification event
            $properties = array(
                // Required Fields
                '$user_id'            => 'billy_jones_301',
                '$session_id'         => 'gigtleqddo84l8cm15qe4il',
                '$status'             => '$pending',
            
                // Optional fields if applicable
                '$verified_event'     => '$login',
                '$reason'             => '$automated_rule', // Verification was triggered based on risk score
                '$verification_type'  => '$sms',
                '$verified_value'     => '14155551212'
            );
    
            return $this->client->track('$verification', $properties);
        }

    }

?>
