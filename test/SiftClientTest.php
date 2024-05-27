<?php
namespace SiftScience\Test;

use PHPUnit\Framework\TestCase;
use SiftClient;
use SiftRequest;
use SiftResponse;
use Sift;

class SiftClientTest extends TestCase
{
    private static $API_KEY = "agreatsuccess";
    private static $ACCOUNT_ID = "000000000000000000000000";
    private static $API_KEY_MERCHANT = "0000000000000000";
    private static $ACCOUNT_ID_MERCHANT = "000000000000000000000001";

    private $client;
    private $transaction_properties;
    private $label_properties;
    private $profile_properties;
    private $merchant;
    private $send_properties;
    private $resend_properties;
    private $check_properties;
    private $merchant_properties;
    private $post_merchant_properties;
    private $put_merchant_properties;

    private $webhook_properties;
    

    protected function setUp(): void
    {
        $this->client = new SiftClient([
            "api_key" => SiftClientTest::$API_KEY,
            "account_id" => SiftClientTest::$ACCOUNT_ID,
        ]);

        $this->merchant = new SiftClient([
            "api_key" => SiftClientTest::$API_KEY_MERCHANT,
            "account_id" => SiftClientTest::$ACCOUNT_ID_MERCHANT,
        ]);

        $this->transaction_properties = [
            '$buyer_user_id' => "123456",
            '$seller_user_id' => "56789",
            '$amount' => 123456,
            '$currency_code' => "USD",
            '$time' => time(),
            '$transaction_id' => "my_transaction_id",
            '$billing_name' => "Mike Snow",
            '$billing_bin' => "411111",
            '$billing_last4' => "1111",
            '$billing_address1' => "123 Main St.",
            '$billing_city' => "San Francisco",
            '$billing_region' => "CA",
            '$billing_country' => "US",
            '$billing_zip' => "94131",
            '$user_email' => "mike@example.com",
        ];
        $this->label_properties = [
            '$is_bad' => true,
            '$abuse_type' => "content_abuse",
            '$description' => "Listed a fake item",
        ];
        $this->profile_properties = [
            '$user_id' => "test_Heather_Kindle",
            '$content_id' => "profile-23412",
            '$session_id' => "a234ksjfgn435sfg",
            '$status' => '$active',
            '$ip' => "255.255.255.0",
            '$profile' => [
                '$body' =>
                    "Hi! My name is Alex and I just moved to New London!",
                '$contact_email' => "alex_301@domain.com",
                '$contact_address' => [
                    '$name' => "Alex Smith",
                    '$phone' => "1-415-555-6041",
                    '$city' => "New London",
                    '$region' => "New Hampshire",
                    '$country' => "US",
                    '$zipcode' => "03257",
                ],
                '$images' => [
                    [
                        '$md5_hash' => "aflshdfbalsubdf3234sfdkjb",
                        '$link' => "https://www.domain.com/file.png",
                        '$description' => "Alex’s picture",
                    ],
                ],
                '$categories' => ["Friends", "Long-term dating"],
            ],
        ];

        $this->send_properties = [
            '$user_id' => "billy_jones_301",
            '$send_to' => "billy_jones_301@gmail.com",
            '$verification_type' => '$email',
            '$brand_name' => "MyTopBrand",
            '$language' => "en",
            '$event' => [
                '$session_id' => "09f7f361575d11ff",
                '$verified_event' => '$login',
                '$reason' => '$automated_rule',
                '$ip' => "192.168.1.1",
                '$browser' => [
                    '$user_agent' =>
                        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36",
                ],
            ],
        ];

        $this->resend_properties = [
            '$user_id' => "billy_jones_301",
            '$verified_event' => '$login',
            '$verified_entity_id' => "SOME_SESSION_ID",
        ];

        $this->check_properties = [
            '$user_id' => "billy_jones_301",
            '$code' => 524313,
            '$verified_event' => '$login',
            '$verified_entity_id' => "09f7f361575d11ff",
        ];

        $this->merchant_properties = [
            "batch_size" => 2,
        ];

        $this->post_merchant_properties = [
            "id" => "api-key-1000",
            "name" => "Wonderful Payments In",
            "description" => "Wonderful Payments payment provider",
            "address" => [
                "name" => "Alany",
                "address_1" => "Big Payment blvd, 22",
                "address_2" => "apt, 8",
                "city" => "New Orleans",
                "region" => "NA",
                "country" => "US",
                "zipcode" => "76830",
                "phone" => "0394888320",
            ],
            "category" => "1002",
            "service_level" => "Platinum",
            "status" => "active",
            "risk_profile" => [
                "level" => "low",
                "score" => 10,
            ],
        ];

        $this->put_merchant_properties = [
            "id" => "api-key-1",
            "name" => "Wonderful Payments Inc",
            "description" => "Wonderful Payments payment provider",
            "address" => [
                "name" => "Alany",
                "address_1" => "Big Payment blvd, 22",
                "address_2" => "apt, 8",
                "city" => "New Orleans",
                "region" => "NA",
                "country" => "US",
                "zipcode" => "76830",
                "phone" => "0394888320",
            ],
            "category" => "1002",
            "service_level" => "Platinum",
            "status" => "active",
            "risk_profile" => [
                "level" => "low",
                "score" => 10,
            ],
        ];

        $this->webhook_properties = [
            "payload_type" => "ORDER_V1_0",
            "status" => "active",
            "url" => "https://example.com/",
            "enabled_events" => ['$create_order'],
            "name" => "My webhook name",
            "description" => "This is a webhook!"
        ];
    }

    protected function tearDown(): void
    {
        SiftRequest::clearMockResponse();
    }

    public function testConstructor(): void
    {
        $this->assertInstanceOf("SiftClient", $this->client);
    }

    public function testGlobalApiKeySuccess(): void
    {
        Sift::setApiKey("test_global_api_key");
        new SiftClient();
        $this->assertTrue(true);
    }

    public function testEmptyGlobalApiKeyFail(): void
    {
        $this->expectException("InvalidArgumentException");
        Sift::setApiKey("");
        new SiftClient();
    }

    public function testNullGlobalApiKeyFail(): void
    {
        $this->expectException("InvalidArgumentException");
        Sift::setApiKey(null);
        new SiftClient();
    }

    public function testNonStringGlobalApiKeyFail(): void
    {
        $this->expectException("InvalidArgumentException");
        Sift::setApiKey(42);
        new SiftClient();
    }

    public function testEmptyApiKeyFail(): void
    {
        $this->expectException("InvalidArgumentException");
        new SiftClient(["api_key" => ""]);
    }

    public function testNullApiKeyFail(): void
    {
        $this->expectException("InvalidArgumentException");
        new SiftClient(["api_key" => null]);
    }

    public function testNonStringApiKeyFail(): void
    {
        $this->expectException("InvalidArgumentException");
        new SiftClient(["api_key" => 42]);
    }

    public function testInvalidOptToConstructor(): void
    {
        $this->expectException("InvalidArgumentException");
        Sift::setApiKey("some_key");
        new SiftClient(["apiKey" => "typos"]);
    }

    public function testEmptyEventNameFail(): void
    {
        $this->expectException("InvalidArgumentException");
        $this->client->track("", $this->transaction_properties);
    }

    public function testNullEventNameFail(): void
    {
        $this->expectException("InvalidArgumentException");
        $this->client->track(null, $this->transaction_properties);
    }

    public function testNonStringEventNameFail(): void
    {
        $this->expectException("InvalidArgumentException");
        $this->client->track(42, $this->transaction_properties);
    }

    public function testEmptyPropertiesFail(): void
    {
        $this->expectException("InvalidArgumentException");
        $this->client->track("event_name", []);
    }

    public function testNullPropertiesFail(): void
    {
        $this->expectException("InvalidArgumentException");
        $this->client->track("event_name", null);
    }

    public function testNonArrayPropertiesFail(): void
    {
        $this->expectException("InvalidArgumentException");
        $this->client->track("event_name", 42);
    }

    public function testEmptyUserIdFail(): void
    {
        $this->expectException("InvalidArgumentException");
        $this->client->score("");
    }

    public function testNullUserIdFail(): void
    {
        $this->expectException("InvalidArgumentException");
        $this->client->score(null);
    }

    public function testNonStringUserIdFail(): void
    {
        $this->expectException("InvalidArgumentException");
        $this->client->score(42);
    }

    public function testSuccessfulTrackEvent(): void
    {
        $mockUrl = "https://api.sift.com/v205/events";
        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": "OK"}',
            200,
            null
        );
        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::POST,
            $mockResponse
        );

        $response = $this->client->track(
            '$transaction',
            $this->transaction_properties
        );
        $this->assertTrue($response->isOk());
        $this->assertEquals("OK", $response->apiErrorMessage);
    }

    public function testSuccessfulScoreFetch(): void
    {
        $mockUrl =
            "https://api.sift.com/v205/score/12345?api_key=agreatsuccess";
        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": "OK",
            "user_id": "12345", "scores": {"payment_abuse": {"score": 0.55}}}',
            200,
            null
        );
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->score("12345");
        $this->assertTrue($response->isOk());
        $this->assertEquals("OK", $response->apiErrorMessage);
        $this->assertEquals(
            0.55,
            $response->body["scores"]["payment_abuse"]["score"]
        );
    }

    public function testSuccessfulScoreFetchWithAbuseTypes(): void
    {
        $mockUrl =
            "https://api.sift.com/v205/score/12345?api_key=agreatsuccess&abuse_types=payment_abuse%2Ccontent_abuse";
        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": "OK",
            "user_id": "12345", "scores": {"payment_abuse": {"score": 0.55}}}',
            200,
            null
        );
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->score("12345", [
            "abuse_types" => ["payment_abuse", "content_abuse"],
        ]);
        $this->assertTrue($response->isOk());
        $this->assertEquals("OK", $response->apiErrorMessage);
        $this->assertEquals(
            0.55,
            $response->body["scores"]["payment_abuse"]["score"]
        );
    }

    public function testSuccessfulGetUserScore(): void
    {
        $mockUrl =
            "https://api.sift.com/v205/users/12345/score?api_key=agreatsuccess";
        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": "OK",
            "user_id": "12345", "scores": {"payment_abuse": {"score": 0.55}}}',
            200,
            null
        );
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->get_user_score("12345");
        $this->assertTrue($response->isOk());
        $this->assertEquals("OK", $response->apiErrorMessage);
        $this->assertEquals(
            0.55,
            $response->body["scores"]["payment_abuse"]["score"]
        );
    }

    public function testSuccessfulGetUserScoreWithSpecialCharacters(): void
    {
        $mockUrl =
            "https://api.sift.com/v205/users/em%2FDqw%3D%3D/score?api_key=agreatsuccess";
        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": "OK"}',
            200,
            null
        );
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->get_user_score("em/Dqw==");
        $this->assertTrue($response->isOk());
    }

    public function testSuccessfulGetUserScoreWithAbuseTypes(): void
    {
        $mockUrl =
            "https://api.sift.com/v205/users/12345/score?api_key=agreatsuccess&abuse_types=payment_abuse%2Ccontent_abuse";
        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": "OK",
            "user_id": "12345", "scores": {"payment_abuse": {"score": 0.55}}}',
            200,
            null
        );
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->get_user_score("12345", [
            "abuse_types" => ["payment_abuse", "content_abuse"],
        ]);
        $this->assertTrue($response->isOk());
        $this->assertEquals("OK", $response->apiErrorMessage);
        $this->assertEquals(
            0.55,
            $response->body["scores"]["payment_abuse"]["score"]
        );
    }

    public function testSuccessfulRescoreUser(): void
    {
        $mockUrl =
            "https://api.sift.com/v205/users/12345/score?api_key=agreatsuccess";
        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": "OK",
            "user_id": "12345", "scores": {"payment_abuse": {"score": 0.55}}}',
            200,
            null
        );
        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::POST,
            $mockResponse
        );

        $response = $this->client->rescore_user("12345");
        $this->assertTrue($response->isOk());
        $this->assertEquals("OK", $response->apiErrorMessage);
        $this->assertEquals(
            0.55,
            $response->body["scores"]["payment_abuse"]["score"]
        );
    }

    public function testSuccessfulRescoreUserWithAbuseTypes(): void
    {
        $mockUrl =
            "https://api.sift.com/v205/users/12345/score?api_key=agreatsuccess&abuse_types=payment_abuse%2Ccontent_abuse";
        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": "OK",
            "user_id": "12345", "scores": {"payment_abuse": {"score": 0.55}}}',
            200,
            null
        );
        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::POST,
            $mockResponse
        );

        $response = $this->client->rescore_user("12345", [
            "abuse_types" => ["payment_abuse", "content_abuse"],
        ]);
        $this->assertTrue($response->isOk());
        $this->assertEquals("OK", $response->apiErrorMessage);
        $this->assertEquals(
            0.55,
            $response->body["scores"]["payment_abuse"]["score"]
        );
    }

    public function testSuccessfulSyncScoreFetch(): void
    {
        $mockUrl = "https://api.sift.com/v205/events?return_score=true";
        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": "OK",
            "score_response": {"user_id": "12345", "score": 0.55}}',
            200,
            null
        );
        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::POST,
            $mockResponse
        );

        $response = $this->client->track(
            '$transaction',
            $this->transaction_properties,
            [
                "timeout" => 2,
                "return_score" => true,
            ]
        );
        $this->assertTrue($response->isOk());
        $this->assertEquals("OK", $response->apiErrorMessage);
        $this->assertEquals($response->body["score_response"]["score"], 0.55);
    }

    public function testInvalidTrackOption(): void
    {
        $this->expectException("InvalidArgumentException");
        $response = $this->client->track(
            '$transaction',
            $this->transaction_properties,
            [
                "timeout" => 2,
                "return_score" => true,
                "give_me_the_secret_scores" => true,
            ]
        );
    }

    public function testSuccessfulSyncWorkflowStatusFetch(): void
    {
        $mockUrl =
            "https://api.sift.com/v205/events?return_workflow_status=true&return_route_info=true&abuse_types=legacy%2Caccount_abuse";
        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": "OK",
            "score_response": {"user_id": "12345", "score": 0.55}}',
            200,
            null
        );
        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::POST,
            $mockResponse
        );

        $response = $this->client->track(
            '$transaction',
            $this->transaction_properties,
            [
                "return_workflow_status" => true,
                "return_route_info" => true,
                "abuse_types" => ["legacy", "account_abuse"],
            ]
        );
        $this->assertTrue($response->isOk());
        $this->assertEquals("OK", $response->apiErrorMessage);
        $this->assertEquals($response->body["score_response"]["score"], 0.55);
    }

    public function testSuccessfulLabelUser(): void
    {
        $mockUrl = "https://api.sift.com/v205/users/54321/labels";
        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": "OK"}',
            200,
            null
        );
        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::POST,
            $mockResponse
        );

        $response = $this->client->label("54321", $this->label_properties);
        $this->assertTrue($response->isOk());
        $this->assertEquals("OK", $response->apiErrorMessage);
    }

    public function testSuccessfulUnlabelUser(): void
    {
        $mockUrl =
            "https://api.sift.com/v205/users/54321/labels?api_key=agreatsuccess";
        $mockResponse = new SiftResponse("", 204, null);
        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::DELETE,
            $mockResponse
        );

        $response = $this->client->unlabel("54321");
        $this->assertTrue($response->isOk());
    }

    public function testSuccessfulUnlabelUserWithAbuseType(): void
    {
        $mockUrl =
            "https://api.sift.com/v205/users/54321/labels?api_key=agreatsuccess&abuse_type=account_abuse";
        $mockResponse = new SiftResponse("", 204, null);
        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::DELETE,
            $mockResponse
        );

        $response = $this->client->unlabel("54321", [
            "abuse_type" => "account_abuse",
        ]);
        $this->assertTrue($response->isOk());
    }

    // Test all special characters for score API
    public function testSuccessfulScoreFetchWithAllUserIdCharacters(): void
    {
        $mockUrl =
            "https://api.sift.com/v205/score/12345" .
            urlencode('=.-_+@:&^%!$') .
            "?api_key=agreatsuccess";
        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": "OK",
            "user_id": "12345=.-_+@:&^%!$", "score": 0.55}',
            200,
            null
        );
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->score('12345=.-_+@:&^%!$');
        $this->assertTrue($response->isOk());
        $this->assertEquals("OK", $response->apiErrorMessage);
        $this->assertEquals($response->body["score"], 0.55);
    }

    // Test all special characters for Label API
    public function testSuccessfulLabelWithAllUserIdCharacters(): void
    {
        $mockUrl =
            "https://api.sift.com/v205/users/54321" .
            urlencode('=.-_+@:&^%!$') .
            "/labels";
        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": "OK"}',
            200,
            null
        );
        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::POST,
            $mockResponse
        );

        $response = $this->client->label(
            "54321=.-_+@:&^%!$",
            $this->label_properties
        );
        $this->assertTrue($response->isOk());
        $this->assertEquals("OK", $response->apiErrorMessage);
    }

    // Test all special characters for Unlabel API
    public function testSuccessfulUnlabelWithAllUserIdCharacters(): void
    {
        $mockUrl =
            "https://api.sift.com/v205/users/54321" .
            urlencode('=.-_+@:&^%!$') .
            "/labels?api_key=agreatsuccess";
        $mockResponse = new SiftResponse("", 204, null);
        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::DELETE,
            $mockResponse
        );

        $response = $this->client->unlabel("54321=.-_+@:&^%!$");
        $this->assertTrue($response->isOk());
    }

    public function testGetWorkflowStatus(): void
    {
        $mockUrl =
            "https://api.sift.com/v3/accounts/" .
            SiftClientTest::$ACCOUNT_ID .
            "/workflows/runs/a8r89d6yh3hkn";
        $mockResponse = new SiftResponse(
            '{"id":"4zxwibludiaaa","config":{"id":"5rrbr4iaaa","version":"1468367620871"},"config_display_name":"workflow config","abuse_types":["payment_abuse"],"state":"running","entity":{"id":"example_user","type":"user"},"history":[{"app":"decision","name":"decision","state":"running","config":{"decision_id":"user_decision"}},{"app":"event","name":"Event","state":"finished","config":{}},{"app":"user","name":"Entity","state":"finished","config":{}}]}',
            200,
            null
        );
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->getWorkflowStatus("a8r89d6yh3hkn", [
            "account_id" => SiftClientTest::$ACCOUNT_ID,
        ]);
        $this->assertTrue($response->isOk());
    }

    public function testGetWorkflowStatusWithSpecialCharacters(): void
    {
        $mockUrl =
            "https://api.sift.com/v3/accounts/" .
            SiftClientTest::$ACCOUNT_ID .
            "/workflows/runs/1%2F2";
        $mockResponse = new SiftResponse(
            '{"id":"4zxwibludiaaa","config":{"id":"5rrbr4iaaa","version":"1468367620871"},"config_display_name":"workflow config","abuse_types":["payment_abuse"],"state":"running","entity":{"id":"example_user","type":"user"},"history":[{"app":"decision","name":"decision","state":"running","config":{"decision_id":"user_decision"}},{"app":"event","name":"Event","state":"finished","config":{}},{"app":"user","name":"Entity","state":"finished","config":{}}]}',
            200,
            null
        );
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->getWorkflowStatus("1/2", [
            "account_id" => SiftClientTest::$ACCOUNT_ID,
        ]);
        $this->assertTrue($response->isOk());
    }

    public function testGetUserDecisions(): void
    {
        $mockUrl =
            "https://api.sift.com/v3/accounts/" .
            SiftClientTest::$ACCOUNT_ID .
            "/users/example_user/decisions";
        $mockResponse = new SiftResponse(
            '{"decisions":{"payment_abuse":{"decision":{"id":"user_decision"},"time":1468707128659,"webhook_succeeded":false}}}',
            200,
            null
        );
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $this->client = new SiftClient([
            "api_key" => SiftClientTest::$API_KEY,
            "account_id" => SiftClientTest::$ACCOUNT_ID,
        ]);
        $response = $this->client->getUserDecisions("example_user");
        $this->assertTrue($response->isOk());
    }

    public function testGetUserDecisionsWithInvalidOption(): void
    {
        $this->expectException("InvalidArgumentException");
        $this->client->getUserDecisions("example_user", [
            "return_score" => true,
        ]);
    }
  
    public function testGetUserDecisionsWithSpecialCharacters(): void
    {
        $mockUrl =
            "https://api.sift.com/v3/accounts/" .
            SiftClientTest::$ACCOUNT_ID .
            "/users/em%2FDqw%3D%3D/decisions";
        $mockResponse = new SiftResponse("{}", 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $this->client = new SiftClient([
            "api_key" => SiftClientTest::$API_KEY,
            "account_id" => SiftClientTest::$ACCOUNT_ID,
        ]);
        $response = $this->client->getUserDecisions("em/Dqw==");
        $this->assertTrue($response->isOk());
    }

    public function testGetSessionDecisions(): void
    {
        $mockUrl =
            "https://api.sift.com/v3/accounts/" .
            SiftClientTest::$ACCOUNT_ID .
            "/users/example_user/sessions/example_session/decisions";
        $mockResponse = new SiftResponse(
            '{"decisions":{"account_takeover":{"decision":{"id":"session_decision"},"time":1468599638005,"webhook_succeeded":false},"time":1468517407135,"webhook_succeeded":true}}}',
            200,
            null
        );
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->getSessionDecisions(
            "example_user",
            "example_session",
            ["timeout" => 4]
        );
        $this->assertTrue($response->isOk());
    }

    public function testGetSessionDecisionsWithSpecialCharacters(): void
    {
        $mockUrl =
            "https://api.sift.com/v3/accounts/" .
            SiftClientTest::$ACCOUNT_ID .
            "/users/em%2FDqw%3D%3D/sessions/example_session/decisions";
        $mockResponse = new SiftResponse("{}", 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->getSessionDecisions(
            "em/Dqw==",
            "example_session",
            ["timeout" => 4]
        );
        $this->assertTrue($response->isOk());
    }


    public function testGetOrderDecisions(): void
    {
        $mockUrl =
            "https://api.sift.com/v3/accounts/" .
            SiftClientTest::$ACCOUNT_ID .
            "/orders/example_order/decisions";
        $mockResponse = new SiftResponse(
            '{"decisions":{"payment_abuse":{"decision":{"id":"order_decisionz"},"time":1468599638005,"webhook_succeeded":false},"account_abuse":{"decision":{"id":"good_order"},"time":1468517407135,"webhook_succeeded":true}}}',
            200,
            null
        );

        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->getOrderDecisions("example_order", [
            "timeout" => 4,
        ]);
        $this->assertTrue($response->isOk());
    }


    public function testGetOrderDecisionsWithSpecialCharacters(): void
    {
        $mockUrl =
            "https://api.sift.com/v3/accounts/" .
            SiftClientTest::$ACCOUNT_ID .
            "/orders/KyrVAPMJ%2Fyw%3D/decisions";
        $mockResponse = new SiftResponse("{}", 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->getOrderDecisions("KyrVAPMJ/yw=", [
            "timeout" => 4,
        ]);
        $this->assertTrue($response->isOk());
    }


    public function testGetDecisionList(): void
    {
        $mockUrl =
            "https://api.sift.com/v3/accounts/" .
            SiftClientTest::$ACCOUNT_ID .
            "/decisions";
        $mockResponse = new SiftResponse(
            '{"data": [{' .
                '"id": "block_user_payment_abuse", "name": "Block user",' .
                '"description": "cancel and refund all of the user\'s' .
                ' pending order.", "entity_type": "user,"' .
                '"abuse_type": "payment_abuse",' .
                '"category": "block",' .
                '"webhook_url": "http://webhook.example.com",' .
                '"created_at": 1468005577348,' .
                '"created_by": "admin@example.com",' .
                '"updated_at": 1469229177756,' .
                '"updated_by": "billy@exmaple.com"' .
                "}]}",
            200,
            null
        );

        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->getDecisions();
        $this->assertTrue($response->isOk());
    }

    public function testGetDecisionListNextRef(): void
    {
        $mockUrl =
            "https://api.sift.com/v3/accounts/" .
            SiftClientTest::$ACCOUNT_ID .
            "/decisions?from=10&limit=5";
        $mockResponse = new SiftResponse(
            '{"data": [{' .
                '"id": "block_user_payment_abuse", "name": "Block user",' .
                '"description": "cancel and refund all of the user\'s' .
                ' pending order.", "entity_type": "user,"' .
                '"abuse_type": "payment_abuse",' .
                '"category": "block",' .
                '"webhook_url": "http://webhook.example.com",' .
                '"created_at": 1468005577348,' .
                '"created_by": "admin@example.com",' .
                '"updated_at": 1469229177756,' .
                '"updated_by": "billy@exmaple.com"' .
                "}]}",
            200,
            null
        );

        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->getDecisions([
            "next_ref" => $mockUrl,
        ]);
        $this->assertTrue($response->isOk());
    }


    public function testApplyDecisionToUser(): void
    {
        $mockUrl =
            "https://api.sift.com/v3/accounts/" .
            SiftClientTest::$ACCOUNT_ID .
            "/users/some_user/decisions";
        $mockResponse = new SiftResponse(
            "{" .
                '"entity": {' .
                '"id" : "some_user"' .
                '"type" : "USER"' .
                "}," .
                '"decision": {' .
                '"id": "user_looks_ok_payment_abuse"' .
                "}," .
                '"time": "1461963439151"' .
                "}" .
                "}",
            200,
            null
        );

        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::POST,
            $mockResponse
        );

        $response = $this->client->applyDecisionToUser(
            "some_user",
            "user_looks_ok_payment_abuse",
            "MANUAL_REVIEW",
            ["analyst" => "analyst@example.com"]
        );
        $this->assertTrue($response->isOk());
    }


    public function testApplyDecisionToUserWithSpecialCharacters(): void
    {
        $mockUrl =
            "https://api.sift.com/v3/accounts/" .
            SiftClientTest::$ACCOUNT_ID .
            "/users/em%2FDqw%3D%3D/decisions";
        $mockResponse = new SiftResponse("{}", 200, null);

        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::POST,
            $mockResponse
        );

        $response = $this->client->applyDecisionToUser(
            "em/Dqw==",
            "user_looks_ok_payment_abuse",
            "MANUAL_REVIEW",
            ["analyst" => "analyst@example.com"]
        );
        $this->assertTrue($response->isOk());
    }

    public function testApplyDecisionToOrder(): void
    {
        $mockUrl =
            "https://api.sift.com/v3/accounts/" .
            SiftClientTest::$ACCOUNT_ID .
            "/users/some_user/orders/ORDER_1234/decisions";
        $mockResponse = new SiftResponse(
            "{" .
                '"entity": {' .
                '"id" : "ORDER_1234"' .
                '"type" : "ORDER"' .
                "}," .
                '"decision": {' .
                '"id": "order_looks_ok_payment_abuse"' .
                "}," .
                '"time": "1461963439151"' .
                "}" .
                "}",
            200,
            null
        );

        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::POST,
            $mockResponse
        );

        $response = $this->client->applyDecisionToOrder(
            "some_user",
            "ORDER_1234",
            "order_looks_ok_payment_abuse",
            "MANUAL_REVIEW",
            ["analyst" => "analyst@example.com"]
        );

        $this->assertTrue($response->isOk());
    }
  
    public function testApplyDecisionToOrderWithSpecialCharacters(): void
    {
        $mockUrl =
            "https://api.sift.com/v3/accounts/" .
            SiftClientTest::$ACCOUNT_ID .
            "/users/em%2FDqw%3D%3D/orders/u2L8Qy%2B%2FAgM%3D/decisions";
        $mockResponse = new SiftResponse("{}", 200, null);

        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::POST,
            $mockResponse
        );

        $response = $this->client->applyDecisionToOrder(
            "em/Dqw==",
            "u2L8Qy+/AgM=",
            "order_looks_ok_payment_abuse",
            "MANUAL_REVIEW",
            ["analyst" => "analyst@example.com"]
        );

        $this->assertTrue($response->isOk());
    }

    public function testApplyDecisionToSession(): void
    {
        $mockUrl =
            "https://api.sift.com/v3/accounts/" .
            SiftClientTest::$ACCOUNT_ID .
            "/users/some_user/sessions/SESSION_12345/decisions";
        $mockResponse = new SiftResponse(
            "{" .
                '"entity": {' .
                '"id" : "SESSION_12345"' .
                '"type" : "SESSION"' .
                "}," .
                '"decision": {' .
                '"id": "session_looks_ok_ato"' .
                "}," .
                '"time": "1461963439151"' .
                "}" .
                "}",
            200,
            null
        );

        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::POST,
            $mockResponse
        );

        $response = $this->client->applyDecisionToSession(
            "some_user",
            "SESSION_12345",
            "session_looks_ok_ato",
            "MANUAL_REVIEW",
            ["analyst" => "analyst@example.com"]
        );

        $this->assertTrue($response->isOk());
    }
  
    public function testApplyDecisionToSessionWithSpecialCharacters(): void
    {
        $mockUrl =
            "https://api.sift.com/v3/accounts/" .
            SiftClientTest::$ACCOUNT_ID .
            "/users/em%2FDqw%3D%3D/sessions/u2L8Qy%2B%2FAgM%3D/decisions";
        $mockResponse = new SiftResponse("{}", 200, null);

        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::POST,
            $mockResponse
        );

        $response = $this->client->applyDecisionToSession(
            "em/Dqw==",
            "u2L8Qy+/AgM=",
            "session_looks_ok_ato",
            "MANUAL_REVIEW",
            ["analyst" => "analyst@example.com"]
        );

        $this->assertTrue($response->isOk());
    }

    public function testApplyDecisionToContent(): void
    {
        $mockUrl =
            "https://api.sift.com/v3/accounts/" .
            SiftClientTest::$ACCOUNT_ID .
            "/users/some_user/content/CONTENT_12345/decisions";
        $mockResponse = new SiftResponse(
            "{" .
                '"entity": {' .
                '"id" : "CONTENT_12345"' .
                '"type" : "CONTENT"' .
                "}," .
                '"decision": {' .
                '"id": "content_looks_ok_content_abuse"' .
                "}," .
                '"time": "1461963439151"' .
                "}" .
                "}",
            200,
            null
        );

        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::POST,
            $mockResponse
        );

        $response = $this->client->applyDecisionToContent(
            "some_user",
            "CONTENT_12345",
            "content_looks_ok_content_abuse",
            "MANUAL_REVIEW",
            ["analyst" => "analyst@example.com"]
        );

        $this->assertTrue($response->isOk());
    }

    public function testApplyDecisionToContentWithSpecialCharacters(): void
    {
        $mockUrl =
            "https://api.sift.com/v3/accounts/" .
            SiftClientTest::$ACCOUNT_ID .
            "/users/em%2FDqw%3D%3D/content/u2L8Qy%2B%2FAgM%3D/decisions";
        $mockResponse = new SiftResponse("{}", 200, null);

        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::POST,
            $mockResponse
        );

        $response = $this->client->applyDecisionToContent(
            "em/Dqw==",
            "u2L8Qy+/AgM=",
            "content_looks_ok_content_abuse",
            "MANUAL_REVIEW",
            ["analyst" => "analyst@example.com"]
        );

        $this->assertTrue($response->isOk());
    }

    public function testTrackProfileEvent(): void
    {
        $mockUrl = "https://api.sift.com/v205/events";
        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": "OK"}',
            200,
            null
        );
        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::POST,
            $mockResponse
        );

        $response = $this->client->track(
            '$create_content',
            $this->profile_properties
        );
        $this->assertTrue($response->isOk());
        $this->assertEquals("OK", $response->apiErrorMessage);
    }

    public function testSend(): void
    {
        $mockUrl = "https://api.sift.com/v1.1/verification/send";
        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": "OK"}',
            200,
            null
        );
        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::POST,
            $mockResponse
        );

        $response = $this->client->send($this->send_properties);
        $this->assertTrue($response->isOk());
        $this->assertEquals("OK", $response->apiErrorMessage);
    }

    public function testResend(): void
    {
        $mockUrl = "https://api.sift.com/v1.1/verification/resend";
        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": "OK"}',
            200,
            null
        );
        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::POST,
            $mockResponse
        );

        $response = $this->client->resend($this->resend_properties);
        $this->assertTrue($response->isOk());
        $this->assertEquals("OK", $response->apiErrorMessage);
    }

    public function testCheck(): void
    {
        $mockUrl = "https://api.sift.com/v1.1/verification/check";
        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": "OK"}',
            200,
            null
        );
        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::POST,
            $mockResponse
        );

        $response = $this->client->check($this->check_properties);
        $this->assertTrue($response->isOk());
        $this->assertEquals("OK", $response->apiErrorMessage);
    }

    public function testMerchants(): void
    {
        $mockUrl =
            "https://api.sift.com/v3/accounts/" .
            SiftClientTest::$ACCOUNT_ID_MERCHANT .
            "/psp_management/merchants?batch_size=2";

        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": "OK"}',
            200,
            null
        );
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->merchant->merchants($this->merchant_properties);
        $this->assertTrue($response->isOk());
    }
  
    public function testPostMerchant(): void
    {
        $mockUrl =
            "https://api.sift.com/v3/accounts/" .
            SiftClientTest::$ACCOUNT_ID_MERCHANT .
            "/psp_management/merchants";
        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": "OK"}',
            200,
            null
        );
        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::POST,
            $mockResponse
        );

        $response = $this->merchant->postMerchant(
            $this->post_merchant_properties
        );
        $this->assertTrue($response->isOk());
        $this->assertEquals("OK", $response->apiErrorMessage);
    }

    public function testGetMerchant(): void
    {
        $mockUrl =
            "https://api.sift.com/v3/accounts/" .
            SiftClientTest::$ACCOUNT_ID_MERCHANT .
            "/psp_management/merchants/api-key-1";
        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": "OK"}',
            200,
            null
        );
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->merchant->getMerchant("api-key-1");
        $this->assertTrue($response->isOk());
    }

    public function testPutMerchant(): void
    {
        $mockUrl =
            "https://api.sift.com/v3/accounts/" .
            SiftClientTest::$ACCOUNT_ID_MERCHANT .
            "/psp_management/merchants/api-key-1";
        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": "OK"}',
            200,
            null
        );
        SiftRequest::setMockResponse($mockUrl, SiftRequest::PUT, $mockResponse);

        $response = $this->merchant->putMerchant(
            "api-key-1",
            $this->put_merchant_properties
        );
        $this->assertTrue($response->isOk());
    }

    public function testSuccessfulTrackEventWithScorePercentiles(): void {
        $mockUrl = 'https://api.sift.com/v205/events?fields=SCORE_PERCENTILES';
        $mockResponse = new SiftResponse('
        {
            "status": 0, "error_message": "OK",
            "score_response":
                {
                    "user_id": "billy_jones_301",
                    "latest_labels": {},
                    "workflow_statuses": [],
                    "scores": {
                        "account_abuse": {
                            "score": 0.32787917675535705,
                            "reasons": [{
                                "name": "Latest item product title",
                                "value": "The Slanket Blanket-Texas Tea"
                            }],
                            "percentiles": {
                                "last_7_days": -1.0,
                                "last_1_days": -1.0,
                                "last_10_days": -1.0,
                                "last_5_days": -1.0
                            }
                        },
                        "acontent_abuse": {
                            "score": 0.28056292905897995,
                            "reasons": [{
                                "name": "timeSinceFirstEvent",
                                "value": "13.15 minutes"
                            }],
                            "percentiles": {
                                "last_7_days": -1.0,
                                "last_1_days": -1.0,
                                "last_10_days": -1.0,
                                "last_5_days": -1.0
                            }
                        },
                        "payment_abuse": {
                            "score": 0.28610507028376797,
                            "reasons": [{
                                "name": "Latest item currency code",
                                "value": "USD"
                            }, {
                                "name": "Latest item item ID",
                                "value": "B004834GQO"
                            }, {
                                "name": "Latest item product title",
                                "value": "The Slanket Blanket-Texas Tea"
                            }],
                            "percentiles": {
                                "last_7_days": -1.0,
                                "last_1_days": -1.0,
                                "last_10_days": -1.0,
                                "last_5_days": -1.0
                            }
                        },
                        "promotion_abuse": {
                            "score": 0.05731508921450917,
                            "percentiles": {
                                "last_7_days": -1.0,
                                "last_1_days": -1.0,
                                "last_10_days": -1.0,
                                "last_5_days": -1.0
                            }
                        }
                    }
                }
            }', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST ,$mockResponse);
        $response = $this->client->track('$transaction', $this->transaction_properties, [
            "version" => "205","include_score_percentiles" => true
        ]);
       
         $this->assertTrue($response->isOk());
         $this->assertEquals($response->apiErrorMessage, 'OK');
         $this->assertEquals(-1.0, $response->body["score_response"]["scores"]["account_abuse"]["percentiles"]["last_7_days"]);
    }

    public function testScoreAPIWithScorePercentiles(): void {
        $mockUrl = 'https://api.sift.com/v205/score/12345?api_key=agreatsuccess&fields=SCORE_PERCENTILES';
        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": "OK",
            "user_id": "12345", "scores": {"payment_abuse": {"score": 0.55}}}',
            200,
            null
        );
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET ,$mockResponse);
        $response = $this->client->score('12345',  [
            "version" => "205","include_score_percentiles" => true
        ]);
       
        $this->assertTrue($response->isOk());
        $this->assertEquals("OK", $response->apiErrorMessage);
        $this->assertEquals(
            0.55,
            $response->body["scores"]["payment_abuse"]["score"]
        );
    }

    public function testGetUserScoreWithScorePercentiles(): void
    {
        $mockUrl =
            "https://api.sift.com/v205/users/12345/score?api_key=agreatsuccess&fields=SCORE_PERCENTILES";
        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": "OK",
            "user_id": "12345", "scores": {"payment_abuse": {"score": 0.55}}}',
            200,
            null
        );
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->get_user_score("12345", ["include_score_percentiles" => true]);
        $this->assertTrue($response->isOk());
        $this->assertEquals("OK", $response->apiErrorMessage);
        $this->assertEquals(
            0.55,
            $response->body["scores"]["payment_abuse"]["score"]
        );
    }

    public function testSuccessfulTrackEventWithWarnings(): void {
        $mockUrl = 'https://api.sift.com/v205/events?fields=WARNINGS';
        $mockResponse = new SiftResponse('
        {
            "status": 0, "error_message": "OK",
            "warnings": {
                "count": 1,
                "items": [
                    {
                        "message": "Invalid field value"
                    }
                ]
            }
        }', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST ,$mockResponse);
        $response = $this->client->track('$transaction', $this->transaction_properties, [
            "version" => "205", "include_warnings" => true
        ]);

        $this->assertTrue($response->isOk());
        $this->assertEquals('OK', $response->apiErrorMessage);
        $this->assertEquals(1, $response->body["warnings"]["count"]);
        $this->assertEquals('Invalid field value', $response->body["warnings"]["items"][0]["message"]);
    }

    public function testPostWebhook(): void
    {
        $mockUrl =
        "https://api.sift.com/v3/accounts/" .
        SiftClientTest::$ACCOUNT_ID .
        "/webhooks";

        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": "OK"}',
            200,
            null
        );

        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::POST,
            $mockResponse
        );

        $response = $this->client->postWebhooks(
            $this->webhook_properties
        );
        $this->assertTrue($response->isOk());
        $this->assertEquals("OK", $response->apiErrorMessage);
    }

    public function testRetrieveWebhook(): void
    {
        $mockUrl =
        "https://api.sift.com/v3/accounts/" .
        SiftClientTest::$ACCOUNT_ID .
        "/webhooks/webhook_id";

        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": "OK"}',
            200,
            null
        );

        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::GET,
            $mockResponse
        );

        $response = $this->client->retrieveWebhook('webhook_id');
        $this->assertTrue($response->isOk());
        $this->assertEquals("OK", $response->apiErrorMessage);
    }

    public function testListAllWebhooks(): void
    {
        $mockUrl =
        "https://api.sift.com/v3/accounts/" .
        SiftClientTest::$ACCOUNT_ID .
        "/webhooks";

        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": "OK"}',
            200,
            null
        );

        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::GET,
            $mockResponse
        );

        $response = $this->client->listAllWebhooks();
        $this->assertTrue($response->isOk());
        $this->assertEquals("OK", $response->apiErrorMessage);
    }

    public function testUpdateWebhook(): void
    {
        $mockUrl =
        "https://api.sift.com/v3/accounts/" .
        SiftClientTest::$ACCOUNT_ID .
        "/webhooks/webhook_id";

        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": "OK"}',
            200,
            null
        );

        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::PUT,
            $mockResponse
        );

        $response = $this->client->updateWebhook('webhook_id', $this->webhook_properties);
        $this->assertTrue($response->isOk());
        $this->assertEquals("OK", $response->apiErrorMessage);
    }

    public function testDeleteWebhook(): void
    {
        $mockUrl =
        "https://api.sift.com/v3/accounts/" .
        SiftClientTest::$ACCOUNT_ID .
        "/webhooks/webhook_id";

        $mockResponse = new SiftResponse(
            '{"status": 0, "error_message": ""}',
            204,
            null
        );

        SiftRequest::setMockResponse(
            $mockUrl,
            SiftRequest::DELETE,
            $mockResponse
        );

        $response = $this->client->deleteWebhook('webhook_id');
        $this->assertTrue($response->isOk());
        $this->assertEquals("", $response->apiErrorMessage);
        $this->assertEquals("204", $response->httpStatusCode);
    }
}
