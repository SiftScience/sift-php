<?php
    require_once dirname(__DIR__) . '/vendor/autoload.php';
    require_once dirname(__DIR__) . '/test_integration_app/events_api/test_events_api.php';
    require_once dirname(__DIR__) . '/test_integration_app/decisions_api/test_decisions_api.php';
    require_once dirname(__DIR__) . '/test_integration_app/workflows_api/test_workflows_api.php';
    require_once dirname(__DIR__) . '/test_integration_app/score_api/test_score_api.php';
    require_once dirname(__DIR__) . '/test_integration_app/verifications_api/test_verification_api.php';
    require_once dirname(__DIR__) . '/test_integration_app/psp_merchant_api/test_psp_merchant_api.php';
    include 'globals.php';

    class Main extends PHPUnit\Framework\TestCase 
    {
        public function test_all_methods(){
            $objEvents = new test_events_api();
            $objDecisions = new test_decisions_api();
            $objWorkflows  = new test_workflows_api();
            $objScore =new test_score_api();
            $objVerification = new test_verification_api();
            $objPSPMerchant = new test_psp_merchant_api();
            $objUtil = new Utils();

            // Events API
            $this->assertEquals(1, $objUtil->isOk($objEvents->add_item_to_cart()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->add_promotion()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->chargeback()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->content_status()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->create_account()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->create_content_comment()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->create_content_listing()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->create_content_message()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->create_content_post()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->create_content_profile()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->create_content_review()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->create_order()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->flag_content()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->link_session_to_user()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->login()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->logout()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->order_status()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->remove_item_from_cart()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->security_notification()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->transaction()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->update_account()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->update_content_comment()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->update_content_listing()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->update_content_message()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->update_content_post()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->update_content_profile()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->update_content_review()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->update_order()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->update_password()));
            $this->assertEquals(1, $objUtil->isOk($objEvents->verification()));
            $this->assertEquals(1, $objUtil->hasWarnings($objEvents->verification_with_warnings()));
            print("Events API Tested \n");

            // Decisions API
            $this->assertEquals(1, $objUtil->isOk($objDecisions->getUserDecisions()));
            $this->assertEquals(1, $objUtil->isOk($objDecisions->getOrderDecisions()));
            $this->assertEquals(1, $objUtil->isOk($objDecisions->getContentDecisions()));
            $this->assertEquals(1, $objUtil->isOk($objDecisions->getSessionDecisions()));
            $this->assertEquals(1, $objUtil->isOk($objDecisions->getDecisions()));
            $this->assertEquals(1, $objUtil->isOk($objDecisions->apply_decision_to_user()));
            $this->assertEquals(1, $objUtil->isOk($objDecisions->apply_decision_to_order()));
            $this->assertEquals(1, $objUtil->isOk($objDecisions->apply_decision_to_session()));
            $this->assertEquals(1, $objUtil->isOk($objDecisions->apply_decision_to_content()));
            print("Decision API Tested \n");

            // Wrokflows API
            $this->assertEquals(1, $objUtil->isOk($objWorkflows->synchronous_workflows()));
            print("Workflow API Tested \n");

            // Score API
            $this->assertEquals(1, $objUtil->isOk($objScore->user_score()));
            print("Score API Tested \n");

            // Verification API
            $this->assertEquals(1, $objUtil->isOk($objVerification->send()));
            $this->assertEquals(1, $objUtil->isOk($objVerification->resend()));
            $this->assertEquals(1, $objUtil->isOkCheck($objVerification->check()));
            print("Verification API Tested \n");

            // PSP Merchant Management API
            $merchant_id = "merchant_id_test_sift_php_".strval(floor(microtime(true) * 1000));
            $this->assertEquals(1, $objUtil->isOk($objPSPMerchant->create_merchant($merchant_id)));
            $this->assertEquals(1, $objUtil->isOk($objPSPMerchant->update_merchant($merchant_id)));
            $this->assertEquals(1, $objUtil->isOk($objPSPMerchant->get_all_merchants()));
            $this->assertEquals(1, $objUtil->isOk($objPSPMerchant->get_merchant($merchant_id)));
            print("PSP Merchant API Tested \n");
        }
    }

    class Utils 
    {

        public function isOk($response) {          
            // expect http status 200 and api status 0 or http status 201 if apiStatus exists.
            if (isset($response->apiStatus)){
                return (($response->apiStatus == 0) 
                    && (200 === $response->httpStatusCode)) || (201 === $response->httpStatusCode);
            }
            else{
                // otherwise expect http status 200 or http status 201.
                return ((200 === $response->httpStatusCode) || (201 === $response->httpStatusCode));
            }
        }

        public function isOkCheck($response) {
            // expect http status 200 and api status 50
            if (isset($response->apiStatus)){
                return (($response->apiStatus == 50) 
                    && (200 === $response->httpStatusCode));
            }
            else{
                return false;
            }
        }

        public function hasWarnings($response) {
            // expect http status 200, api status 0, and warnings present
            if (isset($response->apiStatus)){
                return ($response->apiStatus == 0)
                    && (200 === $response->httpStatusCode)
                    && ($response->body["warnings"]);
            }
            return false;
        }
    }

    $objMain = new Main();
    $objMain->test_all_methods();

?>
