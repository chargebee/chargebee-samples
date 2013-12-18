<?php
/*
 * Adding ChargeBee php libraries and configuration files.
 */
require_once(dirname(__FILE__) . "/Config.php");

/* This php file is configured as redirect url for the hosted page in ChargeBee 
 * app. Hosted page Id and state of the hosted page will be sent along the request
 */

 $hostedPageId = $_GET['id'];
 $status = $_GET['state'];
 try {
   if($status == "succeeded") { 
     /* Request the ChargeBee server about the Hosted page state and give the details
      * about the subscription created.
      */
     $result = ChargeBee_HostedPage::retrieve($hostedPageId);
     /* Forwarding to the corresponded subscriber page after getting respose from ChargeBee.
      */
     $queryParameters = "name=".$result->hostedPage()->content()->customer()->firstName
                        ."&planId=". $result->hostedPage()->content()->subscription()->planId;
     header("Location:thankyou.html?".$queryParameters);
   } else {
     /* If the state is not success then error page is shown to the customer. 
      */
     header("HTTP/1.0 400 Error");
     header("Location:error.html");
   }
  } catch(Exception $e) {
    customError($e);
 }

?>
