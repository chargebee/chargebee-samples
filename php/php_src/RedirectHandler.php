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
     /* 
	  * Retrieving the hosted page and getting the details
      * of the subscription created through hosted page.
      */
     $result = ChargeBee_HostedPage::retrieve($hostedPageId);
     $hostedPage = $result->hostedPage();
     if( $hostedPage->state != "succeeded" ) {
        header("HTTP/1.0 400 Error");
        include($_SERVER["DOCUMENT_ROOT"]."/error_pages/400.html");
	    return;
     }
     /* 
	  * Forwarding the user to thank you page.
      */
	 $content = $hostedPage->content();
     $queryParameters = "name=". $content->customer()->firstName
                        ."&planId=". $content->subscription()->planId;
     header("Location:thankyou.html?".$queryParameters);
   } else {
     /* 
	  * If the state is not success then displaying the 
	  * error page to the customer. 
      */
     header("HTTP/1.0 400 Error");
     include($_SERVER["DOCUMENT_ROOT"]."/error_pages/400.html");
   }
  } catch(Exception $e) {
    customError500($e);
 }

?>
