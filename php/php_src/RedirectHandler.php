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
     $hostedPage = $result->hostedPage();
     if( $hostedPage->state != "succeeded" ) {
        header("HTTP/1.0 400 Error");
        include($_SERVER["DOCUMENT_ROOT"]."/error_pages/400.html");
	return;
     }
     /* Forwarding to the corresponded subscriber page after getting respose from ChargeBee.
      */
     $queryParameters = "name=".$hostedPage->content()->customer()->firstName
                        ."&planId=". $hostedPage->content()->subscription()->planId;
     header("Location:thankyou.html?".$queryParameters);
   } else {
     /* If the state is not success then error page is shown to the customer. 
      */
     header("HTTP/1.0 400 Error");
     include($_SERVER["DOCUMENT_ROOT"]."/error_pages/400.html");
   }
  } catch(Exception $e) {
    customError500($e);
 }

?>
