<?php
/*
 * Adding ChargeBee php libraries
 */
require(dirname(__FILE__) . "/lib/ChargeBee.php");
require(dirname(__FILE__) . "/ErrorHandler.php");

/* 
 * Sets the environment for calling the Chargebee API.
 * You need to sign up at ChargeBee app to get this credential.
 */
ChargeBee_Environment::configure("<your-site>","<your-api-key>");
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
