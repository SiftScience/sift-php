<?php
    require_once dirname(__DIR__) . '/vendor/autoload.php';
    require_once dirname(__DIR__) . '/test_integration_app/events_api/test_events_api.php';
    require_once dirname(__DIR__) . '/test_integration_app/decisions_api/test_decisions_api.php';
    require_once dirname(__DIR__) . '/test_integration_app/workflows_api/test_workflows_api.php';
    require_once dirname(__DIR__) . '/test_integration_app/score_api/test_score_api.php';
    require_once dirname(__DIR__) . '/test_integration_app/verifications_api/test_verification_api.php';
    require_once dirname(__DIR__) . '/test_integration_app/psp_merchant_api/test_psp_merchant_api.php';

    // set api_key and account_id as Environment variables
    putenv("api_key=PUT_A_VALID_API_KEY");
    putenv("account_id=PUT_A_VALID_ACCOUNT_ID");

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

            // Decisions API
            $this->assertEquals(1, $objUtil->isOk($objDecisions->getUserDecisions()));
            $this->assertEquals(1, $objUtil->isOk($objDecisions->getOrderDecisions()));
            $this->assertEquals(1, $objUtil->isOk($objDecisions->getContentDecisions()));
            $this->assertEquals(1, $objUtil->isOk($objDecisions->getSessionDecisions()));
            $this->assertEquals(1, $objUtil->isOk($objDecisions->getDecisions()));
            $this->assertEquals(0, $objUtil->isOk($objDecisions->apply_decision_to_user()));
            $this->assertEquals(0, $objUtil->isOk($objDecisions->apply_decision_to_order()));
            $this->assertEquals(0, $objUtil->isOk($objDecisions->apply_decision_to_session()));
            $this->assertEquals(0, $objUtil->isOk($objDecisions->apply_decision_to_content()));

            // Wrokflows API
            $this->assertEquals(1, $objUtil->isOk($objWorkflows->synchronous_workflows()));
        
            // Score API
            $this->assertEquals(0, $objUtil->isOk($objScore->user_score()));//403

            // Verification API
            $this->assertEquals(1, $objUtil->isOk($objVerification->Send()));
            $this->assertEquals(1, $objUtil->isOk($objVerification->resend()));
            $this->assertEquals(1, $objUtil->isOk($objVerification->check()));

            // PSP Merchant Management API
            $this->assertEquals(0, $objUtil->isOk($objPSPMerchant->create_merchant()));
            $this->assertEquals(1, $objUtil->isOk($objPSPMerchant->update_merchant()));
            $this->assertEquals(1, $objUtil->isOk($objPSPMerchant->get_all_merchants()));
            $this->assertEquals(1, $objUtil->isOk($objPSPMerchant->get_merchant()));

            print_r("tests are completed");
        }
    }

    class Utils 
    {

        public function isOk($response) {
            print_r($response);
            // expect http status 200 and api status 0 or http status 201 if apiStatus exists.
            if (isset($response->apiStatus)){
                return (($response->apiStatus == 0) && (200 === $response->httpStatusCode)) || (201 === $response->httpStatusCode);
            }
            else{
                // otherwise expect http status 200 or http status 201.
                return ((200 === $response->httpStatusCode) || (201 === $response->httpStatusCode));
            }
        }
    }

    $objMain = new Main();
    $objMain->test_all_methods();

?>