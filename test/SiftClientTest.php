<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

class SiftClientTest extends PHPUnit_Framework_TestCase {
    private static $API_KEY = 'agreatsuccess';
    private static $ACCOUNT_ID = '90201c25e39320c45b3da37b';
    private $client;
    private $transaction_properties;

    protected function setUp() {
        $this->client = new SiftClient(array(
            'api_key' => SiftClientTest::$API_KEY,
            'account_id' => SiftClientTest::$ACCOUNT_ID
        ));
        $this->transaction_properties = array(
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
        );
        $this->label_properties = array(
            '$is_bad' => true,
            '$abuse_type' => 'content_abuse',
            '$description' => 'Listed a fake item'
        );
    }

    protected function tearDown() {
        SiftRequest::clearMockResponse();
    }

    public function testConstructor() {
        $this->assertInstanceOf('SiftClient', $this->client);
    }

    public function testGlobalApiKeySuccess() {
        $this->setExpectedException(null);
        Sift::setApiKey('test_global_api_key');
        new SiftClient();
    }

    public function testEmptyGlobalApiKeyFail() {
        $this->setExpectedException('InvalidArgumentException');
        Sift::setApiKey('');
        new SiftClient();
    }

    public function testNullGlobalApiKeyFail() {
        $this->setExpectedException('InvalidArgumentException');
        Sift::setApiKey(null);
        new SiftClient();
    }

    public function testNonStringGlobalApiKeyFail() {
        $this->setExpectedException('InvalidArgumentException');
        Sift::setApiKey(42);
        new SiftClient();
    }

    public function testEmptyApiKeyFail() {
        $this->setExpectedException('InvalidArgumentException');
        new SiftClient(array('api_key' => ''));
    }

    public function testNullApiKeyFail() {
        $this->setExpectedException('InvalidArgumentException');
        new SiftClient(array('api_key' => null));
    }

    public function testNonStringApiKeyFail() {
        $this->setExpectedException('InvalidArgumentException');
        new SiftClient(array('api_key' => 42));
    }

    public function testInvalidOptToConstructor() {
        $this->setExpectedException('InvalidArgumentException');
        Sift::setApiKey('some_key');
        new SiftClient(array('apiKey' => 'typos'));
    }

    public function testEmptyEventNameFail() {
        $this->setExpectedException('InvalidArgumentException');
        $this->client->track('', $this->transaction_properties);
    }

    public function testNullEventNameFail() {
        $this->setExpectedException('InvalidArgumentException');
        $this->client->track(null, $this->transaction_properties);
    }

    public function testNonStringEventNameFail() {
        $this->setExpectedException('InvalidArgumentException');
        $this->client->track(42, $this->transaction_properties);
    }

    public function testEmptyPropertiesFail() {
        $this->setExpectedException('InvalidArgumentException');
        $this->client->track('event_name', array());
    }

    public function testNullPropertiesFail() {
        $this->setExpectedException('InvalidArgumentException');
        $this->client->track('event_name', null);
    }

    public function testNonArrayPropertiesFail() {
        $this->setExpectedException('InvalidArgumentException');
        $this->client->track('event_name', 42);
    }

    public function testEmptyUserIdFail() {
        $this->setExpectedException('InvalidArgumentException');
        $this->client->score('');
    }

    public function testNullUserIdFail() {
        $this->setExpectedException('InvalidArgumentException');
        $this->client->score(null);
    }

    public function testNonStringUserIdFail() {
        $this->setExpectedException('InvalidArgumentException');
        $this->client->score(42);
    }

    public function testSuccessfulTrackEvent() {
        $mockUrl = 'https://api.siftscience.com/v204/events';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK"}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST ,$mockResponse);

        $response = $this->client->track('$transaction', $this->transaction_properties);
        $this->assertTrue($response->isOk());
        $this->assertEquals($response->apiErrorMessage, 'OK');
    }

    public function testSuccessfulScoreFetch() {
        $mockUrl = 'https://api.siftscience.com/v204/score/12345?api_key=agreatsuccess';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK",
                "user_id": "12345", "scores": {"payment_abuse": {score: 0.55}}}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->score('12345');
        $this->assertTrue($response->isOk());
        $this->assertEquals($response->apiErrorMessage, 'OK');
        $this->assertEquals($response->body['scores']['payment_abuse']['score'], 0.55);
    }

    public function testSuccessfulScoreFetchWithAbuseTypes() {
        $mockUrl = 'https://api.siftscience.com/v204/score/12345?api_key=agreatsuccess&abuse_types=payment_abuse%2Ccontent_abuse';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK",
                "user_id": "12345", "scores": {"payment_abuse": {score: 0.55}}}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->score('12345', array(
            'abuse_types' => array('payment_abuse', 'content_abuse')
        ));
        $this->assertTrue($response->isOk());
        $this->assertEquals($response->apiErrorMessage, 'OK');
        $this->assertEquals($response->body['scores']['payment_abuse']['score'], 0.55);
    }

    public function testSuccessfulSyncScoreFetch() {
        $mockUrl = 'https://api.siftscience.com/v204/events?return_score=true';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK",
                "score_response": {"user_id": "12345", "score": 0.55}}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST, $mockResponse);

        $response = $this->client->track('$transaction', $this->transaction_properties, array(
            'timeout' => 2,
            'return_score' => true
        ));
        $this->assertTrue($response->isOk());
        $this->assertEquals($response->apiErrorMessage, 'OK');
        $this->assertEquals($response->body["score_response"]["score"], 0.55);
    }

    public function testInvalidTrackOption() {
        $this->setExpectedException('InvalidArgumentException');
        $response = $this->client->track('$transaction', $this->transaction_properties, array(
            'timeout' => 2,
            'return_score' => true,
            'give_me_the_secret_scores' => true
        ));
    }

    public function testSuccessfulSyncWorkflowStatusFetch() {
        $mockUrl = 'https://api.siftscience.com/v204/events?return_workflow_status=true&abuse_types=legacy%2Caccount_abuse';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK",
                "score_response": {"user_id": "12345", "score": 0.55}}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST, $mockResponse);

        $response = $this->client->track('$transaction', $this->transaction_properties, array(
            'return_workflow_status' => true,
            'abuse_types' => array('legacy', 'account_abuse')
        ));
        $this->assertTrue($response->isOk());
        $this->assertEquals($response->apiErrorMessage, 'OK');
        $this->assertEquals($response->body["score_response"]["score"], 0.55);
    }

    public function testSuccessfulLabelUser() {
        $mockUrl = 'https://api.siftscience.com/v204/users/54321/labels';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK"}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST, $mockResponse);

        $response = $this->client->label("54321", $this->label_properties);
        $this->assertTrue($response->isOk());
        $this->assertEquals($response->apiErrorMessage, 'OK');
    }

    public function testSuccessfulUnlabelUser() {
        $mockUrl = 'https://api.siftscience.com/v204/users/54321/labels?api_key=agreatsuccess';
        $mockResponse = new SiftResponse('', 204, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::DELETE, $mockResponse);

        $response = $this->client->unlabel("54321");
        $this->assertTrue($response->isOk());
    }

    public function testSuccessfulUnlabelUserWithAbuseType() {
        $mockUrl = 'https://api.siftscience.com/v204/users/54321/labels?api_key=agreatsuccess&abuse_type=account_abuse';
        $mockResponse = new SiftResponse('', 204, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::DELETE, $mockResponse);

        $response = $this->client->unlabel("54321", array('abuse_type' => 'account_abuse'));
        $this->assertTrue($response->isOk());
    }

    // Test all special characters for score API
    public function testSuccessfulScoreFetchWithAllUserIdCharacters() {
        $mockUrl = 'https://api.siftscience.com/v204/score/12345' . urlencode('=.-_+@:&^%!$') . '?api_key=agreatsuccess';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK",
                "user_id": "12345=.-_+@:&^%!$", "score": 0.55}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->score('12345=.-_+@:&^%!$');
        $this->assertTrue($response->isOk());
        $this->assertEquals($response->apiErrorMessage, 'OK');
        $this->assertEquals($response->body["score"], 0.55);
    }

    // Test all special characters for Label API
    public function testSuccessfulLabelWithAllUserIdCharacters() {
        $mockUrl = 'https://api.siftscience.com/v204/users/54321' . urlencode('=.-_+@:&^%!$') . '/labels';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK"}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST, $mockResponse);

        $response = $this->client->label("54321=.-_+@:&^%!$", $this->label_properties);
        $this->assertTrue($response->isOk());
        $this->assertEquals($response->apiErrorMessage, 'OK');
    }

    // Test all special characters for Unlabel API
    public function testSuccessfulUnlabelWithAllUserIdCharacters() {
        $mockUrl = 'https://api.siftscience.com/v204/users/54321' . urlencode('=.-_+@:&^%!$') . '/labels?api_key=agreatsuccess';
        $mockResponse = new SiftResponse('', 204, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::DELETE, $mockResponse);

        $response = $this->client->unlabel("54321=.-_+@:&^%!$");
        $this->assertTrue($response->isOk());
    }


    public function testGetWorkflowStatus() {
        $mockUrl = 'https://api3.siftscience.com/v3/accounts/5b2fd4ddbcf4254aa6baabb6/workflows/runs/a8r89d6yh3hkn';
        $mockResponse = new SiftResponse('{"id":"4zxwibludiaaa","config":{"id":"5rrbr4iaaa","version":"1468367620871"},"config_display_name":"workflow config","abuse_types":["payment_abuse"],"state":"running","entity":{"id":"example_user","type":"user"},"history":[{"app":"decision","name":"decision","state":"running","config":{"decision_id":"user_decision"}},{"app":"event","name":"Event","state":"finished","config":{}},{"app":"user","name":"Entity","state":"finished","config":{}}]}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->getWorkflowStatus('a8r89d6yh3hkn', array(
            'account_id' => '5b2fd4ddbcf4254aa6baabb6'
        ));
        $this->assertTrue($response->isOk());
    }


    public function testGetUserDecisions() {
        $mockUrl = 'https://api3.siftscience.com/v3/accounts/5b2fd4ddbcf4254aa6baabb6/users/example_user/decisions';
        $mockResponse = new SiftResponse('{"decisions":{"payment_abuse":{"decision":{"id":"user_decision"},"time":1468707128659,"webhook_succeeded":false}}}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $this->client = new SiftClient(array(
            'api_key' => SiftClientTest::$API_KEY, 'account_id' => '5b2fd4ddbcf4254aa6baabb6'));
        $response = $this->client->getUserDecisions('example_user');
        $this->assertTrue($response->isOk());
    }


    public function testGetUserDecisionsWithInvalidOption() {
        $this->setExpectedException('InvalidArgumentException');
        $response = $this->client->getUserDecisions('example_user', array('return_score' => true));
    }


    public function testGetOrderDecisions() {
        $mockUrl = 'https://api3.siftscience.com/v3/accounts/90201c25e39320c45b3da37b/orders/example_order/decisions';
        $mockResponse = new SiftResponse('{"decisions":{"payment_abuse":{"decision":{"id":"order_decisionz"},"time":1468599638005,"webhook_succeeded":false},"account_abuse":{"decision":{"id":"good_order"},"time":1468517407135,"webhook_succeeded":true}}}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);

        $response = $this->client->getOrderDecisions('example_order', array('timeout' => 4));
        $this->assertTrue($response->isOk());
    }

    public function testGetDecisionList() {
        $mockUrl = 'https://api3.siftscience.com/v3/accounts/90201c25e39320c45b3da37b/decisions';
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

    public function testGetDecisionListNextRef() {
        $mockUrl = 'https://api3.siftscience.com/v3/accounts/90201c25e39320c45b3da37b/decisions?from=10&limit=5';
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

        $response = $this->client->getDecisions(array(
          'next_ref' => $mockUrl
        ));
        $this->assertTrue($response->isOk());
    }

    public function testApplyDecisionOnUser() {
        $mockUrl = 'https://api3.siftscience.com/v3/accounts/90201c25e39320c45b3da37b/users/some_user/decisions';
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
            array('analyst' => 'analyst@example.com')
        );
        $this->assertTrue($response->isOk());
    }

    public function testApplyDecisionOnOrder() {
        $mockUrl = 'https://api3.siftscience.com/v3/accounts/90201c25e39320c45b3da37b/users/some_user/orders/ORDER_1234/decisions';
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
            array('analyst' => 'analyst@example.com')
        );

        $this->assertTrue($response->isOk());
    }
}
