<?php
/*
 * Adding ChargeBee php libraries
 */
require_once(dirname(__FILE__) . "/Config.php");

/*
 * Demo on how to use Stripe Pop up to get the customer card information
 * and create a subscription in ChargeBee using the same stripe token.
 */
if($_POST) {
  
 $planId = "basic";
 try {
   /*
    * Passing StripeToken, customer information, shipping information and plan id
    * to the ChargeBee create Subscription API.
    */
   
   $result = ChargeBee_Subscription::create( array("planId" => $planId,
                                                  "customer" => $_POST['customer'],
                                                  "card" => array("tmpToken" => $_POST['stripeToken']),
                                                  "shippingAddress" => $_POST['shipping_address'] ) );
   
   $jsonResp = array("forward" => "thankyou.html");
   print json_encode($jsonResp);
 } catch(ChargeBee_APIError $e) {
    /*
     * ChargeBee exception is captured through APIException and 
     * the error messsage(JSON) is sent to the client.
     */
    $jsonError = $e->getJsonObject();
    header('HTTP/1.0 ' . $jsonError["http_status_code"] . ' Error');
    print(json_encode($jsonError, true));
 } catch(Exception $e) {
    /*
     * Other errors are captured here and error messsage (as JSON) is 
     * sent to the client.
     */
    $jsonError = array("error_msg"=>"Error while creating subscription");
    header("HTTP/1.0 500 Error");
    print json_encode($jsonError,true);
 }
  
}

?>

