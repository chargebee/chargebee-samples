<?php
/*
 * Adding ChargeBee php libraries and configuration files.
 */
require_once(dirname(__FILE__) . "/Config.php");

/*
 * Demo on how to use Chargebee Checkout page with iFrame messaging enabled
 */
$uri = $_SERVER["REQUEST_URI"];

if ($_GET) {
  if( endsWith(substr($uri,0,strpos($uri,"?")), "redirect_handler") ) {
     redirectHandler();
  } else { 
     header("HTTP/1.0 400 Error");
     include($_SERVER["DOCUMENT_ROOT"]."/error_pages/400.html"); 
  }
} else if ($_POST) {
   if( endsWith($uri, "/checkout") ) {
     callingIframeCheckoutPage();
   } else {
      header("HTTP/1.0 400 Error");
      include($_SERVER["DOCUMENT_ROOT"]."/error_pages/400.html"); 
   }
} else {
   header("HTTP/1.0 400 Error");
   include($_SERVER["DOCUMENT_ROOT"]."/error_pages/400.html"); 
}


/*
 * User after clicking signup will call this method.
 * This will return the hosted page url with iframe messaging option enabled 
 * and the id of the hosted page.
 */
function callingIframeCheckoutPage() {
  header('Content-Type: application/json');
  $planId = "basic";
  try {
     
     $result = ChargeBee_HostedPage::CheckoutNew( array("subscription" => array( "planId" => $planId ),
			 		              "customer" => $_POST['customer'],
                                                      "embed" => "true",
						      "iframeMessaging" => "true" ) );
     

     
     /*
      * Sending hosted page url and hosted page id as response.
      */
     $response = array("url" => $result->hostedPage()->url, "hosted_page_id" => $result->hostedPage()->id );
     print json_encode($response);
     
} catch (ChargeBee_APIError $e) {
    /*
     * ChargeBee exception is captured through APIException and 
     * the error messsage(JSON) is sent to the client.
     */
    $jsonError = $e->getJsonObject();
    header("HTTP/1.0 " . $jsonError['http_status_code'] . " Error");
    print json_encode($jsonError,true);
  } catch (Exception $e) {
    /*
     * Other errors are captured here and error messsage (as JSON) is 
     * sent to the client.
     */
    $jsonError = array("error_msg" => "Error while proceeding to payment details page.");
    header("HTTP/1.0 500 Error");
    print json_encode($jsonError, true);
  }
}


/*
 * After checkout the customer will be taken to redirect handler and a check has been made 
 * whether the checkout is successful. If successful, then he will be taken to the thank you page.
 */
function redirectHandler() {
  
  $id = $_GET['id'];
  $result = ChargeBee_HostedPage::retrieve($id);
  $hostedPage = $result->hostedPage();
  if( $hostedPage->state != "succeeded") {
    header("HTTP/1.0 400 Error");
    include($_SERVER["DOCUMENT_ROOT"]."/error_pages/400.html"); 
  } else {
    header("Location: thankyou.php?subscription_id=".URLencode($hostedPage->content()->subscription()->id));
  
  }
 
}

?>
