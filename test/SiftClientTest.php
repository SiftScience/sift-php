<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

class SiftClientTest extends PHPUnit_Framework_TestCase {
    private static $API_KEY = 'agreatsuccess';
    private $client;
    private $transaction_properties;

    protected function setUp() {
        $this->client = new SiftClient(SiftClientTest::$API_KEY);
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
    }

    protected function tearDown() {
        SiftRequest::clearMockResponse();
    }

    public function testConstructor() {
        $this->assertInstanceOf('SiftClient', $this->client);
    }

    public function testEmptyApiKeyFail() {
        $this->setExpectedException('InvalidArgumentException');
        new SiftClient('');
    }

    public function testNullApiKeyFail() {
        $this->setExpectedException('InvalidArgumentException');
        new SiftClient(null);
    }

    public function testNonStringApiKeyFail() {
        $this->setExpectedException('InvalidArgumentException');
        new SiftClient(42);
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
        $mockUrl = 'https://api.siftscience.com/v203/events';
        $mockResponse = new SiftResponse('{"status": 0, "error_message": "OK"}', 200, null);
        SiftRequest::setMockResponse($mockUrl, SiftRequest::POST ,$mockResponse);
        $response = $this->client->track('$transaction', $this->transaction_properties);
        $this->assertTrue($response->isOk());
        $this->assertEquals($response->apiErrorMessage, 'OK');
    }

    public function testSuccessfulScoreFetch() {
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
        $response = $this->client->track('$transaction', $this->transaction_properties, 2, null, true);
        $this->assertTrue($response->isOk());
        $this->assertEquals($response->apiErrorMessage, 'OK');
        $this->assertEquals($response->body["score_response"]["score"], 0.55);
    }
}
