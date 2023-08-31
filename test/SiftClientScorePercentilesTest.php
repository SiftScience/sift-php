<?php
namespace SiftScience\Test;

use PHPUnit\Framework\TestCase;
use SiftClient;
use SiftRequest;
use SiftResponse;

class SiftClientScorePercentilesTest extends TestCase {
    private static $API_KEY = 'agreatsuccess';
    private $client;
    private $transaction_properties;
    private $label_properties;

    protected function setUp(): void {
        $this->client = new SiftClient(array('api_key' => SiftClientScorePercentilesTest::$API_KEY));
        $this->transaction_properties = [
            '$type' => '$add_item_to_cart',
            '$user_id' => 'haneeshv@exalture.com',
            '$session_id' => 'gigtleqddo84l8cm15qe4il',
                '$item' =>[
                    '$item_id' => 'B004834GQO',
                    '$product_title' => 'The Slanket Blanket-Texas Tea',
                    '$price' => 39990000,
                    '$currency_code' => 'USD',
                    '$upc' => '6786211451001',
                    '$sku' => '004834GQ',
                    '$brand' => 'Slanket',
                    '$manufacturer' => 'Slanket',
                    '$category' => 'Blankets & Throws',
                    '$tags' => [
                        'Awesome',
                        'Wintertime specials'
                    ],
                    '$color' => 'Texas Tea',
                    '$quantity' => 16
                ],
            '$brand_name' => 'sift',
            '$site_domain' => 'sift.com',
            '$site_country' => 'US',
                '$browser' => [
                    '$user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
                    '$accept_language' => 'en-US',
                    '$content_language' => 'en-GB'
                ]
        ];
        $this->label_properties = [
            '$reasons' => '[ "$fake" ]',
            '$is_bad' => true,
            '$description' => 'Listed a fake item'
        ];
    }

    protected function tearDown(): void {
        SiftRequest::clearMockResponse();
    }

    public function testSuccessfulTrackEventWithScorePercentiles(): void {
        $mockUrl = 'https://api.sift.com/v205/events?fields=SCORE_PERCENTILES';
        $mockResponse = new SiftResponse('
        {
            "status": 0, "error_message": "OK",
            "score_response":
                {
                    "user_id": "haneeshv@exalture.com",
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
}