<?php
/*
 * Adding ChargeBee php librariesand configuration files.
 */
require_once(dirname(__FILE__) . "/Config.php");
require_once(dirname(__FILE__) . "/ErrorHandler.php");
require_once(dirname(__FILE__) . "/Util.php");

/*
 * Demo on how to create subscription in ChargeBee using Braintree Js.
 */

$uri = $_SERVER["REQUEST_URI"];
$requestBody = json_decode(file_get_contents('php://input'), true);
/*
 * Decode the request from post body and call the appropriate function
 * based on the POST endpoint url.
 */
if ($requestBody) {
    if (endsWith($uri, "/estimate")) {
        estimateSub($requestBody);
    } else if (endsWith($uri, "/checkout")) {
        createSubscription($requestBody);
    } else {
        header("HTTP/1.0 400 Error");
        include($_SERVER["DOCUMENT_ROOT"]."/error_pages/400.html");
    }
}

function estimateSub($body)
{
    
    $result = ChargeBee_Estimate::createSubscription(array(
        "subscription" => array(
          "planId" => $body["sub_plan_id"]
          )
        ));
    
    $estimate = $result->estimate();
    print json_encode($estimate);
}

function createSubscription($body) {
   $planId = "professional";
    try {
        
        /* Creating a subscription in ChargeBee by passing the encrypted 
        * card number and card cvv provided by Braintree Js.
        */
        $createSubscriptionParams = array(
            "planId" => $planId,
            "customer" => $body['customer'],
            "payment_intent" => array(
                "gw_token" => $body['braintreeToken'],
                "gateway_account_id" => "<braintree_gateway_account_id>"
            )
        );
        $result = ChargeBee_Subscription::create($createSubscriptionParams);
        

        $jsonResp = array("forward" => "/braintree-js/thankyou.html");
        print json_encode($jsonResp);
    } catch(ChargeBee_PaymentException $e) {
        handleTempTokenErrors($e);
    } catch(ChargeBee_InvalidRequestException $e) {
        handleInvalidRequestErrors($e, "plan_id");
    } catch(Exception $e) {
        handleGeneralErrors($e);
    }
}


?>
