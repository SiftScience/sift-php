<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

class SiftClient203Test extends PHPUnit_Framework_TestCase {
    private static $API_KEY = 'agreatsuccess';
    private $client;
    private $transaction_properties;

    protected function setUp() {
        $this->client = new SiftClient(array('api_key' => SiftClient203Test::$API_KEY));
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
            '$reasons' => '[ "$fake" ]',
            '$is_bad' => true,
            '$description' => 'Listed a fake item'
        );
    }

    protected function tearDown() {
        SiftRequest::clearMockResponse();
    }

    public function testSuccessfulTrackEvent() {
        $mockUrl = 'https://api.siftscience.com/v203/events';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK"}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST ,$mockResponse);
        $response = $this->client->track('$transaction', $this->transaction_properties, array(
            'version' => '203'
        ));
        $this->assertTrue($response->isOk());
        $this->assertEquals($response->apiErrorMessage, 'OK');
    }

    public function testSuccessfulScoreFetch() {
        $this->client = new SiftClient(array(
            'api_key' => SiftClient203Test::$API_KEY, 'version' => '203'));
        $mockUrl = 'https://api.siftscience.com/v203/score/12345?api_key=agreatsuccess';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK",
                "user_id": "12345", "score": 0.55}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::GET, $mockResponse);
        $response = $this->client->score('12345');
        $this->assertTrue($response->isOk());
        $this->assertEquals($response->apiErrorMessage, 'OK');
        $this->assertEquals($response->body["score"], 0.55);
    }

    public function testSuccessfulSyncScoreFetch() {
        $mockUrl = 'https://api.siftscience.com/v203/events?return_score=true';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK",
                "score_response": {"user_id": "12345", "score": 0.55}}', 200, null);

        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST, $mockResponse);
        $response = $this->client->track('$transaction', $this->transaction_properties, array(
            'timeout' => 2,
            'return_score' => true,
            'version' => 203
        ));
        $this->assertTrue($response->isOk());
        $this->assertEquals($response->apiErrorMessage, 'OK');
        $this->assertEquals($response->body["score_response"]["score"], 0.55);
    }

    public function testSuccessfulLabelUser() {
        $this->client = new SiftClient(array(
            'api_key' => SiftClient203Test::$API_KEY, 'version' => '203'));
        $mockUrl = 'https://api.siftscience.com/v203/users/54321/labels';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK"}', 200, null);

        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST, $mockResponse);
        $response = $this->client->label("54321", $this->label_properties);
        $this->assertTrue($response->isOk());
        $this->assertEquals($response->apiErrorMessage, 'OK');
    }

    public function testSuccessfulUnlabelUser() {
        $mockUrl = 'https://api.siftscience.com/v203/users/54321/labels?api_key=agreatsuccess';
        $mockResponse = new SiftResponse('', 204, null);

        SiftRequest::setMockResponse($mockUrl, SiftRequest::DELETE, $mockResponse);
        $response = $this->client->unlabel("54321", array('version' => '203'));
        $this->assertTrue($response->isOk());
    }

    // Test all special characters for score API
    public function testSuccessfulScoreFetchWithAllUserIdCharacters() {
        $this->client = new SiftClient(array(
            'api_key' => SiftClient203Test::$API_KEY, 'version' => '203'));
        $mockUrl = 'https://api.siftscience.com/v203/score/12345' . urlencode('=.-_+@:&^%!$') . '?api_key=agreatsuccess';
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
        $mockUrl = 'https://api.siftscience.com/v203/users/54321' . urlencode('=.-_+@:&^%!$') . '/labels';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK"}', 200, null);

        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST, $mockResponse);
        $response = $this->client->label("54321=.-_+@:&^%!$", $this->label_properties, array(
            'version' => '203'
        ));
        $this->assertTrue($response->isOk());
        $this->assertEquals($response->apiErrorMessage, 'OK');
    }

    // Test all special characters for Unlabel API
    public function testSuccessfulUnlabelWithAllUserIdCharacters() {
        $this->client = new SiftClient(array(
            'api_key' => SiftClient203Test::$API_KEY, 'version' => '203'));
        $mockUrl = 'https://api.siftscience.com/v203/users/54321' . urlencode('=.-_+@:&^%!$') . '/labels?api_key=agreatsuccess';
        $mockResponse = new SiftResponse('', 204, null);

        SiftRequest::setMockResponse($mockUrl, SiftRequest::DELETE, $mockResponse);
        $response = $this->client->unlabel("54321=.-_+@:&^%!$");
        $this->assertTrue($response->isOk());
    }

}
