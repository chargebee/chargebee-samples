<?php
/*
 * Adding ChargeBee php libraries
 */
require_once(dirname(__FILE__) . "/Config.php");
require_once(dirname(__FILE__) . "/Util.php");
require_once(dirname(__FILE__) . "/ErrorHandler.php");

/*
 * Demo on how to use Stripe Pop up to get the customer card information
 * and create a subscription in ChargeBee using the same stripe token.
 */
if($_POST) {
 validateParameters($_POST);
 $planId = "basic";
 try {
   /*
    * Passing StripeToken, customer information, shipping information and plan id
    * to the ChargeBee create Subscription API.
    */
   
   $result = ChargeBee_Subscription::create( 
             array("planId" => $planId,
                   "customer" => $_POST['customer'],
                   "card" => array("tmpToken" => $_POST['stripeToken']),
                   "shippingAddress" => $_POST['shipping_address'] 
		));
   
   $jsonResp = array("forward" => "thankyou.html");
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

