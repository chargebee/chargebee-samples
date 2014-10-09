<?php
/*
 * Adding ChargeBee php libraries and configuration files.
 */
require_once(dirname(__FILE__) . "/Config.php");
require_once(dirname(__FILE__) . "/Util.php");
require_once(dirname(__FILE__) . "/ErrorHandler.php");

/*
 * Demo on how to use Chargebee Checkout page with iFrame messaging enabled
 */
$uri = $_SERVER["REQUEST_URI"];

if ($_GET) {
  if( endsWith(substr($uri,0,strpos($uri,"?")), "redirect_handler") ) {
     redirectHandler();
  } else { 
     customError400(); 
  }
} else if ($_POST) {
   if( endsWith($uri, "/checkout") ) {
     callingIframeCheckoutPage();
   } else {
      customError400();
   }
} else {
   customError400();
}


/*
 * User after clicking signup button this function will be executed.
 * This will return the hosted page url with iframe messaging option enabled 
 * and the id of the hosted page.
 */
function callingIframeCheckoutPage() {
  header('Content-Type: application/json');
  validateParameters($_POST);
  $planId = "basic";
  try {
     
     $result = ChargeBee_HostedPage::CheckoutNew( array("subscription" => array( "planId" => $planId ),
			 		              						"customer" => $_POST['customer'],
                                                      	"embed" => "true",
						      						    "iframeMessaging" => "true" ) );
     

     
     /*
      * Sending hosted page url and hosted page id as response.
      */
     $response = array( "url" => $result->hostedPage()->url,
	 	 				"hosted_page_id" => $result->hostedPage()->id,
						"site_name" => ChargeBee_Environment::defaultEnv()->getSite());
     print json_encode($response);
     
 } catch(ChargeBee_InvalidRequestException $e) {
	handleInvalidRequestErrors($e, "subscription[plan_id]");
 } catch (Exception $e) {
	handleGeneralErrors($e);
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
