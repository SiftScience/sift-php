<?php

namespace SiftScience\Test;

use PHPUnit\Framework\TestCase;
use SiftClient;
use SiftRequest;
use SiftResponse;
use Sift;

class SiftClientTest extends TestCase
{
    private static $API_KEY = 'agreatsuccess';
    private static $ACCOUNT_ID = '90201c25e39320c45b3da37b';
    private $client;
    private $transaction_properties;
    private $label_properties;
    private $profile_properties;

    protected function setUp(): void
    {
        $this->client = new SiftClient([
            'api_key' => self::$API_KEY,
            'account_id' => self::$ACCOUNT_ID
        ]);
        $this->transaction_properties = [
            '$buyer_user_id' => '123456',
            '$seller_user_id' => '56789',
            '$amount' => 123456,
            '$currency_code' => 'USD',
            '$time' => time(),
            '$transaction_id' => 'my_transaction_id',
            '$billing_name' => 'Mike Snow',
            '$billing_bin' => '411111',
            '$billing_last4' => '1111',
            '$billing_address1' => '123 Main St.',
            '$billing_city' => 'San Francisco',
            '$billing_region' => 'CA',
            '$billing_country' => 'US',
            '$billing_zip' => '94131',
            '$user_email' => 'mike@example.com'
        ];
        $this->label_properties = [
            '$is_bad' => true,
            '$abuse_type' => 'content_abuse',
            '$description' => 'Listed a fake item'
        ];
        $this->profile_properties = [
            '$user_id' => 'test_Heather_Kindle',
            '$content_id' => 'profile-23412',
            '$session_id' => 'a234ksjfgn435sfg',
            '$status' => '$active',
            '$ip' => '255.255.255.0',
            '$profile' => [
                '$body' => 'Hi! My name is Alex and I just moved to New London!',
                '$contact_email' => 'alex_301@domain.com',
                '$contact_address' => [
                    '$name' => 'Alex Smith',
                    '$phone' => '1-415-555-6041',
                    '$city' => 'New London',
                    '$region' => 'New Hampshire',
                    '$country' => 'US',
                    '$zipcode' => '03257'
                ],
                '$images' => [
                    [
                        '$md5_hash' => 'aflshdfbalsubdf3234sfdkjb',
                        '$link' => 'https://www.domain.com/file.png',
                        '$description' => 'Alex’s picture'
                    ]
                ],
                '$categories' => [
                    'Friends',
                    'Long-term dating'
                ]
            ]
        ];
    }

    protected function tearDown(): void
    {
        SiftRequest::clearMockResponse();
    }

    public function testConstructor(): void
    {
        $this->assertInstanceOf('SiftClient', $this->client);
    }

    public function testGlobalApiKeySuccess(): void
    {
        Sift::setApiKey('test_global_api_key');
        new SiftClient();
        $this->assertTrue(true);
    }

    public function testEmptyGlobalApiKeyFail(): void
    {
        $this->expectException('InvalidArgumentException');
        Sift::setApiKey('');
        new SiftClient();
    }

    public function testNullGlobalApiKeyFail(): void
    {
        $this->expectException('InvalidArgumentException');
        Sift::setApiKey(null);
        new SiftClient();
    }

    public function testNonStringGlobalApiKeyFail(): void
    {
        $this->expectException('InvalidArgumentException');
        Sift::setApiKey(42);
        new SiftClient();
    }

    public function testEmptyApiKeyFail(): void
    {
        $this->expectException('InvalidArgumentException');
        new SiftClient(['api_key' => '']);
    }

    public function testNullApiKeyFail(): void
    {
        $this->expectException('InvalidArgumentException');
        new SiftClient(['api_key' => null]);
    }

    public function testNonStringApiKeyFail(): void
    {
        $this->expectException('InvalidArgumentException');
        new SiftClient(['api_key' => 42]);
    }

    public function testInvalidOptToConstructor(): void
    {
        $this->expectException('InvalidArgumentException');
        Sift::setApiKey('some_key');
        new SiftClient(['apiKey' => 'typos']);
    }

    public function testEmptyEventNameFail(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->client->track('', $this->transaction_properties);
    }

    public function testNullEventNameFail(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->client->track(null, $this->transaction_properties);
    }

    public function testNonStringEventNameFail(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->client->track(42, $this->transaction_properties);
    }

    public function testEmptyPropertiesFail(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->client->track('event_name', []);
    }

    public function testNullPropertiesFail(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->client->track('event_name', null);
    }

    public function testNonArrayPropertiesFail(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->client->track('event_name', 42);
    }

    public function testEmptyUserIdFail(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->client->score('');
    }

    public function testNullUserIdFail(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->client->score(null);
    }

    public function testNonStringUserIdFail(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->client->score(42);
    }

    public function testSuccessfulTrackEvent(): void
    {
        $mockUrl = 'https://api.sift.com/v205/events';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK"}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST, $mockResponse);

        $response = $this->client->track('$transaction', $this->transaction_properties);
        $this->assertTrue($response->isOk());
        $this->assertEquals('OK', $response->apiErrorMessage);
    }

    public function testSuccessfulScoreFetch(): void
    {
        $mockUrl = 'https://api.sift.com/v205/score/12345?api_key=agreatsuccess';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK",
            "user_id": "12345", "scores": {"payment_abuse": {"score": 0.55}}}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->score('12345');
        $this->assertTrue($response->isOk());
        $this->assertEquals('OK', $response->apiErrorMessage);
        $this->assertEquals(0.55, $response->body['scores']['payment_abuse']['score']);
    }

    public function testSuccessfulScoreFetchWithAbuseTypes(): void
    {
        $mockUrl = 'https://api.sift.com/v205/score/12345?api_key=agreatsuccess&abuse_types=payment_abuse%2Ccontent_abuse';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK",
            "user_id": "12345", "scores": {"payment_abuse": {"score": 0.55}}}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->score('12345', [
            'abuse_types' => ['payment_abuse', 'content_abuse']
        ]);
        $this->assertTrue($response->isOk());
        $this->assertEquals('OK', $response->apiErrorMessage);
        $this->assertEquals(0.55, $response->body['scores']['payment_abuse']['score']);
    }

    public function testSuccessfulGetUserScore(): void
    {
        $mockUrl = 'https://api.sift.com/v205/users/12345/score?api_key=agreatsuccess';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK",
            "user_id": "12345", "scores": {"payment_abuse": {"score": 0.55}}}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->get_user_score('12345');
        $this->assertTrue($response->isOk());
        $this->assertEquals('OK', $response->apiErrorMessage);
        $this->assertEquals(0.55, $response->body['scores']['payment_abuse']['score']);
    }

    public function testSuccessfulGetUserScoreWithSpecialCharacters(): void
    {
        $mockUrl = 'https://api.sift.com/v205/users/em%2FDqw%3D%3D/score?api_key=agreatsuccess';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK"}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->get_user_score('em/Dqw==');
        $this->assertTrue($response->isOk());
    }

    public function testSuccessfulGetUserScoreWithAbuseTypes(): void
    {
        $mockUrl = 'https://api.sift.com/v205/users/12345/score?api_key=agreatsuccess&abuse_types=payment_abuse%2Ccontent_abuse';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK",
            "user_id": "12345", "scores": {"payment_abuse": {"score": 0.55}}}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->get_user_score('12345', [
            'abuse_types' => ['payment_abuse', 'content_abuse']
        ]);
        $this->assertTrue($response->isOk());
        $this->assertEquals('OK', $response->apiErrorMessage);
        $this->assertEquals(0.55, $response->body['scores']['payment_abuse']['score']);
    }

    public function testSuccessfulRescoreUser(): void
    {
        $mockUrl = 'https://api.sift.com/v205/users/12345/score?api_key=agreatsuccess';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK",
            "user_id": "12345", "scores": {"payment_abuse": {"score": 0.55}}}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST, $mockResponse);

        $response = $this->client->rescore_user('12345');
        $this->assertTrue($response->isOk());
        $this->assertEquals('OK', $response->apiErrorMessage);
        $this->assertEquals(0.55, $response->body['scores']['payment_abuse']['score']);
    }

    public function testSuccessfulRescoreUserWithAbuseTypes(): void
    {
        $mockUrl = 'https://api.sift.com/v205/users/12345/score?api_key=agreatsuccess&abuse_types=payment_abuse%2Ccontent_abuse';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK",
            "user_id": "12345", "scores": {"payment_abuse": {"score": 0.55}}}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST, $mockResponse);

        $response = $this->client->rescore_user('12345', [
            'abuse_types' => ['payment_abuse', 'content_abuse']
        ]);
        $this->assertTrue($response->isOk());
        $this->assertEquals('OK', $response->apiErrorMessage);
        $this->assertEquals(0.55, $response->body['scores']['payment_abuse']['score']);
    }

    public function testSuccessfulSyncScoreFetch(): void
    {
        $mockUrl = 'https://api.sift.com/v205/events?return_score=true';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK",
            "score_response": {"user_id": "12345", "score": 0.55}}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST, $mockResponse);

        $response = $this->client->track('$transaction', $this->transaction_properties, [
            'timeout' => 2,
            'return_score' => true
        ]);
        $this->assertTrue($response->isOk());
        $this->assertEquals('OK', $response->apiErrorMessage);
        $this->assertEquals(0.55, $response->body["score_response"]["score"]);
    }

    public function testInvalidTrackOption(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->client->track('$transaction', $this->transaction_properties, [
            'timeout' => 2,
            'return_score' => true,
            'give_me_the_secret_scores' => true
        ]);
    }

    public function testSuccessfulSyncWorkflowStatusFetch(): void
    {
        $mockUrl = 'https://api.sift.com/v205/events?return_workflow_status=true&abuse_types=legacy%2Caccount_abuse';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK",
            "score_response": {"user_id": "12345", "score": 0.55}}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST, $mockResponse);

        $response = $this->client->track('$transaction', $this->transaction_properties, [
            'return_workflow_status' => true,
            'abuse_types' => ['legacy', 'account_abuse']
        ]);
        $this->assertTrue($response->isOk());
        $this->assertEquals('OK', $response->apiErrorMessage);
        $this->assertEquals(0.55, $response->body["score_response"]["score"]);
    }

    public function testSuccessfulLabelUser(): void
    {
        $mockUrl = 'https://api.sift.com/v205/users/54321/labels';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK"}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST, $mockResponse);

        $response = $this->client->label("54321", $this->label_properties);
        $this->assertTrue($response->isOk());
        $this->assertEquals('OK', $response->apiErrorMessage);
    }

    public function testSuccessfulUnlabelUser(): void
    {
        $mockUrl = 'https://api.sift.com/v205/users/54321/labels?api_key=agreatsuccess';
        $mockResponse = new SiftResponse('', 204, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::DELETE, $mockResponse);

        $response = $this->client->unlabel("54321");
        $this->assertTrue($response->isOk());
    }

    public function testSuccessfulUnlabelUserWithAbuseType(): void
    {
        $mockUrl = 'https://api.sift.com/v205/users/54321/labels?api_key=agreatsuccess&abuse_type=account_abuse';
        $mockResponse = new SiftResponse('', 204, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::DELETE, $mockResponse);

        $response = $this->client->unlabel("54321", ['abuse_type' => 'account_abuse']);
        $this->assertTrue($response->isOk());
    }

    // Test all special characters for score API
    public function testSuccessfulScoreFetchWithAllUserIdCharacters(): void
    {
        $mockUrl = 'https://api.sift.com/v205/score/12345' . urlencode('=.-_+@:&^%!$') . '?api_key=agreatsuccess';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK",
            "user_id": "12345=.-_+@:&^%!$", "score": 0.55}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->score('12345=.-_+@:&^%!$');
        $this->assertTrue($response->isOk());
        $this->assertEquals('OK', $response->apiErrorMessage);
        $this->assertEquals(0.55, $response->body["score"]);
    }

    // Test all special characters for Label API
    public function testSuccessfulLabelWithAllUserIdCharacters(): void
    {
        $mockUrl = 'https://api.sift.com/v205/users/54321' . urlencode('=.-_+@:&^%!$') . '/labels';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK"}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST, $mockResponse);

        $response = $this->client->label("54321=.-_+@:&^%!$", $this->label_properties);
        $this->assertTrue($response->isOk());
        $this->assertEquals('OK', $response->apiErrorMessage);
    }

    // Test all special characters for Unlabel API
    public function testSuccessfulUnlabelWithAllUserIdCharacters(): void
    {
        $mockUrl = 'https://api.sift.com/v205/users/54321' . urlencode('=.-_+@:&^%!$') . '/labels?api_key=agreatsuccess';
        $mockResponse = new SiftResponse('', 204, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::DELETE, $mockResponse);

        $response = $this->client->unlabel("54321=.-_+@:&^%!$");
        $this->assertTrue($response->isOk());
    }

    public function testGetWorkflowStatus(): void
    {
        $mockUrl = 'https://api.sift.com/v3/accounts/5b2fd4ddbcf4254aa6baabb6/workflows/runs/a8r89d6yh3hkn';
        $mockResponse = new SiftResponse('{"id":"4zxwibludiaaa","config":{"id":"5rrbr4iaaa","version":"1468367620871"},"config_display_name":"workflow config","abuse_types":["payment_abuse"],"state":"running","entity":{"id":"example_user","type":"user"},"history":[{"app":"decision","name":"decision","state":"running","config":{"decision_id":"user_decision"}},{"app":"event","name":"Event","state":"finished","config":{}},{"app":"user","name":"Entity","state":"finished","config":{}}]}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->getWorkflowStatus('a8r89d6yh3hkn', [
            'account_id' => '5b2fd4ddbcf4254aa6baabb6'
        ]);
        $this->assertTrue($response->isOk());
    }

    public function testGetWorkflowStatusWithSpecialCharacters(): void
    {
        $mockUrl = 'https://api.sift.com/v3/accounts/5b2fd4ddbcf4254aa6baabb6/workflows/runs/1%2F2';
        $mockResponse = new SiftResponse('{"id":"4zxwibludiaaa","config":{"id":"5rrbr4iaaa","version":"1468367620871"},"config_display_name":"workflow config","abuse_types":["payment_abuse"],"state":"running","entity":{"id":"example_user","type":"user"},"history":[{"app":"decision","name":"decision","state":"running","config":{"decision_id":"user_decision"}},{"app":"event","name":"Event","state":"finished","config":{}},{"app":"user","name":"Entity","state":"finished","config":{}}]}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->getWorkflowStatus('1/2', ['account_id' => '5b2fd4ddbcf4254aa6baabb6']);
        $this->assertTrue($response->isOk());
    }

    public function testGetUserDecisions(): void
    {
        $mockUrl = 'https://api.sift.com/v3/accounts/5b2fd4ddbcf4254aa6baabb6/users/example_user/decisions';
        $mockResponse = new SiftResponse('{"decisions":{"payment_abuse":{"decision":{"id":"user_decision"},"time":1468707128659,"webhook_succeeded":false}}}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $this->client = new SiftClient([
            'api_key' => self::$API_KEY, 'account_id' => '5b2fd4ddbcf4254aa6baabb6']);
        $response = $this->client->getUserDecisions('example_user');
        $this->assertTrue($response->isOk());
    }


    public function testGetUserDecisionsWithInvalidOption(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->client->getUserDecisions('example_user', ['return_score' => true]);
    }

    public function testGetUserDecisionsWithSpecialCharacters(): void
    {
        $mockUrl = 'https://api.sift.com/v3/accounts/5b2fd4ddbcf4254aa6baabb6/users/em%2FDqw%3D%3D/decisions';
        $mockResponse = new SiftResponse('{}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $this->client = new SiftClient([
            'api_key' => self::$API_KEY, 'account_id' => '5b2fd4ddbcf4254aa6baabb6']);
        $response = $this->client->getUserDecisions('em/Dqw==');
        $this->assertTrue($response->isOk());
    }

    public function testGetSessionDecisions(): void
    {
        $mockUrl = 'https://api.sift.com/v3/accounts/90201c25e39320c45b3da37b/users/example_user/sessions/example_session/decisions';
        $mockResponse = new SiftResponse('{"decisions":{"account_takeover":{"decision":{"id":"session_decision"},"time":1468599638005,"webhook_succeeded":false},"time":1468517407135,"webhook_succeeded":true}}}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->getSessionDecisions('example_user', 'example_session', ['timeout' => 4]);
        $this->assertTrue($response->isOk());
    }

    public function testGetSessionDecisionsWithSpecialCharacters(): void
    {
        $mockUrl = 'https://api.sift.com/v3/accounts/90201c25e39320c45b3da37b/users/em%2FDqw%3D%3D/sessions/example_session/decisions';
        $mockResponse = new SiftResponse('{}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->getSessionDecisions('em/Dqw==', 'example_session', ['timeout' => 4]);
        $this->assertTrue($response->isOk());
    }

    public function testGetOrderDecisions(): void
    {
        $mockUrl = 'https://api.sift.com/v3/accounts/90201c25e39320c45b3da37b/orders/example_order/decisions';
        $mockResponse = new SiftResponse('{"decisions":{"payment_abuse":{"decision":{"id":"order_decisionz"},"time":1468599638005,"webhook_succeeded":false},"account_abuse":{"decision":{"id":"good_order"},"time":1468517407135,"webhook_succeeded":true}}}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->getOrderDecisions('example_order', ['timeout' => 4]);
        $this->assertTrue($response->isOk());
    }

    public function testGetOrderDecisionsWithSpecialCharacters(): void
    {
        $mockUrl = 'https://api.sift.com/v3/accounts/90201c25e39320c45b3da37b/orders/KyrVAPMJ%2Fyw%3D/decisions';
        $mockResponse = new SiftResponse('{}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->getOrderDecisions('KyrVAPMJ/yw=', ['timeout' => 4]);
        $this->assertTrue($response->isOk());
    }

    public function testGetDecisionList(): void
    {
        $mockUrl = 'https://api.sift.com/v3/accounts/90201c25e39320c45b3da37b/decisions';
        $mockResponse = new SiftResponse('{"data": [{' .
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
            '}]}', 200, null);

        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->getDecisions();
        $this->assertTrue($response->isOk());
    }

    public function testGetDecisionListNextRef(): void
    {
        $mockUrl = 'https://api.sift.com/v3/accounts/90201c25e39320c45b3da37b/decisions?from=10&limit=5';
        $mockResponse = new SiftResponse('{"data": [{' .
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
            '}]}', 200, null);

        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->getDecisions([
            'next_ref' => $mockUrl
        ]);
        $this->assertTrue($response->isOk());
    }

    public function testApplyDecisionToUser(): void
    {
        $mockUrl = 'https://api.sift.com/v3/accounts/90201c25e39320c45b3da37b/users/some_user/decisions';
        $mockResponse = new SiftResponse('{' .
            '"entity": {' .
            '"id" : "some_user"' .
            '"type" : "USER"' .
            '},' .
            '"decision": {' .
            '"id": "user_looks_ok_payment_abuse"' .
            '},' .
            '"time": "1461963439151"' .
            '}' .
            '}', 200, null);

        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST, $mockResponse);

        $response = $this->client->applyDecisionToUser('some_user',
            'user_looks_ok_payment_abuse',
            'MANUAL_REVIEW',
            ['analyst' => 'analyst@example.com']
        );
        $this->assertTrue($response->isOk());
    }

    public function testApplyDecisionToUserWithSpecialCharacters(): void
    {
        $mockUrl = 'https://api.sift.com/v3/accounts/90201c25e39320c45b3da37b/users/em%2FDqw%3D%3D/decisions';
        $mockResponse = new SiftResponse('{}', 200, null);

        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST, $mockResponse);

        $response = $this->client->applyDecisionToUser('em/Dqw==',
            'user_looks_ok_payment_abuse',
            'MANUAL_REVIEW',
            ['analyst' => 'analyst@example.com']
        );
        $this->assertTrue($response->isOk());
    }

    public function testApplyDecisionToOrder(): void
    {
        $mockUrl = 'https://api.sift.com/v3/accounts/90201c25e39320c45b3da37b/users/some_user/orders/ORDER_1234/decisions';
        $mockResponse = new SiftResponse('{' .
            '"entity": {' .
            '"id" : "ORDER_1234"' .
            '"type" : "ORDER"' .
            '},' .
            '"decision": {' .
            '"id": "order_looks_ok_payment_abuse"' .
            '},' .
            '"time": "1461963439151"' .
            '}' .
            '}', 200, null);

        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST, $mockResponse);

        $response = $this->client->applyDecisionToOrder('some_user',
            'ORDER_1234',
            'order_looks_ok_payment_abuse',
            'MANUAL_REVIEW',
            ['analyst' => 'analyst@example.com']
        );

        $this->assertTrue($response->isOk());
    }

    public function testApplyDecisionToOrderWithSpecialCharacters(): void
    {
        $mockUrl = 'https://api.sift.com/v3/accounts/90201c25e39320c45b3da37b/users/em%2FDqw%3D%3D/orders/u2L8Qy%2B%2FAgM%3D/decisions';
        $mockResponse = new SiftResponse('{}', 200, null);

        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST, $mockResponse);

        $response = $this->client->applyDecisionToOrder('em/Dqw==',
            'u2L8Qy+/AgM=',
            'order_looks_ok_payment_abuse',
            'MANUAL_REVIEW',
            ['analyst' => 'analyst@example.com']
        );

        $this->assertTrue($response->isOk());
    }

    public function testApplyDecisionToSession(): void
    {
        $mockUrl = 'https://api.sift.com/v3/accounts/90201c25e39320c45b3da37b/users/some_user/sessions/SESSION_12345/decisions';
        $mockResponse = new SiftResponse('{' .
            '"entity": {' .
            '"id" : "SESSION_12345"' .
            '"type" : "SESSION"' .
            '},' .
            '"decision": {' .
            '"id": "session_looks_ok_ato"' .
            '},' .
            '"time": "1461963439151"' .
            '}' .
            '}', 200, null);

        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST, $mockResponse);

        $response = $this->client->applyDecisionToSession('some_user',
            'SESSION_12345',
            'session_looks_ok_ato',
            'MANUAL_REVIEW',
            ['analyst' => 'analyst@example.com']
        );

        $this->assertTrue($response->isOk());
    }

    public function testApplyDecisionToSessionWithSpecialCharacters(): void
    {
        $mockUrl = 'https://api.sift.com/v3/accounts/90201c25e39320c45b3da37b/users/em%2FDqw%3D%3D/sessions/u2L8Qy%2B%2FAgM%3D/decisions';
        $mockResponse = new SiftResponse('{}', 200, null);

        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST, $mockResponse);

        $response = $this->client->applyDecisionToSession('em/Dqw==',
            'u2L8Qy+/AgM=',
            'session_looks_ok_ato',
            'MANUAL_REVIEW',
            ['analyst' => 'analyst@example.com']
        );

        $this->assertTrue($response->isOk());
    }

    public function testApplyDecisionToContent(): void
    {
        $mockUrl = 'https://api.sift.com/v3/accounts/90201c25e39320c45b3da37b/users/some_user/content/CONTENT_12345/decisions';
        $mockResponse = new SiftResponse('{' .
            '"entity": {' .
            '"id" : "CONTENT_12345"' .
            '"type" : "CONTENT"' .
            '},' .
            '"decision": {' .
            '"id": "content_looks_ok_content_abuse"' .
            '},' .
            '"time": "1461963439151"' .
            '}' .
            '}', 200, null);

        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST, $mockResponse);

        $response = $this->client->applyDecisionToContent('some_user',
            'CONTENT_12345',
            'content_looks_ok_content_abuse',
            'MANUAL_REVIEW',
            ['analyst' => 'analyst@example.com']
        );

        $this->assertTrue($response->isOk());
    }

    public function testApplyDecisionToContentWithSpecialCharacters(): void
    {
        $mockUrl = 'https://api.sift.com/v3/accounts/90201c25e39320c45b3da37b/users/em%2FDqw%3D%3D/content/u2L8Qy%2B%2FAgM%3D/decisions';
        $mockResponse = new SiftResponse('{}', 200, null);

        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST, $mockResponse);

        $response = $this->client->applyDecisionToContent('em/Dqw==',
            'u2L8Qy+/AgM=',
            'content_looks_ok_content_abuse',
            'MANUAL_REVIEW',
            ['analyst' => 'analyst@example.com']
        );

        $this->assertTrue($response->isOk());
    }

    public function testTrackProfileEvent(): void
    {
        $mockUrl = 'https://api.sift.com/v205/events';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK"}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST, $mockResponse);

        $response = $this->client->track('$create_content', $this->profile_properties);
        $this->assertTrue($response->isOk());
        $this->assertEquals('OK', $response->apiErrorMessage);
    }
}
