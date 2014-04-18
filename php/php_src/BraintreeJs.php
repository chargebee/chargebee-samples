<?php
/*
 * Adding ChargeBee php librariesand configuration files.
 */
require_once(dirname(__FILE__) . "/Config.php");

/*
 * Demo on how to create subscription in ChargeBee using Braintree Js.
 */
if($_POST) {

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
 } catch ( ChargeBee_APIError $e) {
   /* ChargeBee exception is captured through APIException and 
    * the error messsage(JSON) is sent to the client.
    */
    $jsonError = $e->getJsonObject();
    header('HTTP/1.0 ' . $jsonError["http_status_code"] . ' Error');
    print(json_encode($jsonError, true)); 
 } catch ( Exception $e) {
    /* Other errors are captured here and error messsage (as JSON) 
     * sent to the client.
     */
     $jsonError = array("error_msg"=>"Error while creating subscription");
     header("HTTP/1.0 500 Error");
     print json_encode($jsonError,true);
 }
  
}

?>
