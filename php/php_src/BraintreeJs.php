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
if($_POST) {

 validateParameters($_POST);
 $planId = "professional";
 try {
   
   /* Creating a subscription in ChargeBee by passing the encrypted 
    * card number and card cvv provided by Braintree Js.
    */
   $createSubscriptionParams = array("planId" => $planId,
				     				 "customer" => $_POST['customer'],
                                     "card" => $_POST['card'] );
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
