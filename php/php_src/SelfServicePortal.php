<?php
/*
 * Adding ChargeBee php libraries and configuration files.
 */
require_once(dirname(__FILE__) . "/Config.php");

/*
 * Self Service Portal for customers to manage their subscriptions.
 */
$uri = $_SERVER["REQUEST_URI"];

if($_GET) {
   if( getSubscriptionId() == null ) {
     header("Location: /ssp-php/");
     return;
   }
   if( endsWith(substr($uri, 0, strpos($uri,"?")), "/update_card") ) {
      updateCard();
   } else if( endsWith(substr($uri, 0, strpos($uri,"?")), "/redirect_handler") ) {
      redirectHandler();
   } else if( endsWith(substr($uri, 0, strpos($uri,"?")), "/invoice_as_pdf") )  {
      invoiceAsPdf();
   } else {
      header("HTTP/1.0 400 Error");
      include($_SERVER["DOCUMENT_ROOT"]."/error_pages/400.html");
   }
} else if( $_POST ) {
    if( !endsWith($uri,"/login") && getSubscriptionId == null ) {
      header("Location: /ssp-php/");
      return;
    }
    if ( endsWith($uri,"/login") ) {
	login();
    } else if( endsWith($uri, "/logout") ) {
        logout();
    } else if( endsWith($uri,"/update_account_info") ) {
        updateAccountInfo();   
    } else if( endsWith($uri,"/update_billing_info") ) {
	updateBillingInfo();   
    } else if(endsWith($uri, "/update_shipping_address")) {
       updateShippingAddress(); 
    } else if(endsWith($uri, "/sub_cancel") ) {
       subscriptionCancel();
    } else if( endsWith($uri, "/sub_reactivate") ) { 
       subscriptionReactivate();
    } else {
	header("HTTP/1.0 400 Error");
	include($_SERVER["DOCUMENT_ROOT"]."/error_pages/400.html");
    }
} else {
    header("HTTP/1.0 400 Error");
    include($_SERVER["DOCUMENT_ROOT"]."/error_pages/400.html");

}

function getSubscriptionId() {
  $subscriptionId = null;
  session_start();
  if(isset($_SESSION['subscription_id']) ) {
     $subscriptionId = $_SESSION['subscription_id'];
  }
  return $subscriptionId;
}
/*
 * Forwards the user to ChargeBee hosted page to update the card details.
 */
function updateCard() {
 $customerId =  $_GET['customer_id'];
 try {
   $result = ChargeBee_HostedPage::updateCard(
             array("customer"=> array("id"=> $customerId ), 
             "embed" => "false" ));
   $url = $result->hostedPage()->url;
   header("Location: ". $url);
 } catch (Exception $e) {
     header("HTTP/1.0 500 Error");
     include($_SERVER["DOCUMENT_ROOT"]."/error_pages/500.html");
 }
}

/*
 * Handles the redirection from ChargeBee on successful card update.
 */
function redirectHandler() {
 if( "succeeded" == $_GET['state'] ) {
   header("Location: /ssp-php/subscription");  
 } else {
   header("HTTP/1.0 400 Error");
   include($_SERVER["DOCUMENT_ROOT"]."/error_pages/400.html");
 }
}


/*
 * Returns pdf download url for the requested invoice
 */
function invoiceAsPdf() {
  $invoiceId = $_GET['invoice_id'];
  $result = ChargeBee_Invoice::pdf($invoiceId);
  header("Location: " . $result->download()->downloadUrl);
}


/*
 * Authenticates the user and sets the subscription id as session attribute.
 * Here the username should be subscription id in ChargeBee and 
 * password can be anything.
 */
function login(){
  $username = $_POST['subscription_id'];
  $password = $_POST['password'];
  if( verifyCredentials($username, $password) ) { 
      session_start();
      $_SESSION['subscription_id']=$username;
      header("Location: subscription");
   } else {
        header("Location: /ssp-php/?login=failed");
   } 
}

/*
 * Log out the user by invalidating its session
 */
function logout() {
  session_start();
  if( isset($_SESSSION['subscription_id']) ) {
     unset($_SESSION['subscription_id']);
  }
  session_destroy();
  header("Location: /ssp-php/");
  
}


/*
 * Update customer details in ChargeBee.
 */
function updateAccountInfo() {
  header('Content-Type: application/json');
  try {
    $result = ChargeBee_Customer::update($_POST['customer_id'], 
                                         array( "first_name" => $_POST['first_name'],
                                                "last_name" => $_POST['last_name'],
                                                "email" => $_POST['email'],
                                                "company" => $_POST['company'],
                                                "phone" => $_POST['phone']
                                               ));
    $jsonResponse = array("forward" => "/ssp-php/subscription");
    echo json_encode($jsonResponse, true);
  } catch (ChargeBee_APIError $e) {
    $jsonError = $e->getJsonObject();
    header("HTTP/1.0 " . $jsonError['http_status_code'] . " Error");
    print json_encode($jsonError,true);
  } catch (Exception $e) {
    $jsonError = array("error_msg" => "Error in updating information");
    header("HTTP/1.0 500 Error");
    print json_encode($jsonError, true);
 }
  
}



/*
 * Update Billing info of customer in ChargeBee.
 */
function updateBillingInfo() {
  header('Content-Type: application/json');
  $customerId = $_POST['customer_id'];
  $billingAddrParams = $_POST['billing_address'];
  try {
     $result = ChargeBee_Customer::updateBillingInfo($customerId, 
                                                     array("billing_address" => $billingAddrParams));
     $jsonResponse = array("forward" => "/ssp-php/subscription");
     print json_encode($jsonResponse, true);
  } catch( ChargeBee_APIError $e) {
     $jsonError = $e->getJsonObject();
     header("HTTP/1.0 ". $jsonError['http_status_code'] . " Error");
     print json_encode($jsonError, true);
  } catch( Exception $e) {
     $jsonError = array("error_msg" => "Error in updating information");
     header("HTTP/1.0 500 Error");
     print json_encode($jsonError, true);
  }
}


/*
 * Update Shipping address for the customer in ChargeBee.
 */
function updateShippingAddress() {
  header('Content-Type: application/json');
  try {
     $result = ChargeBee_Subscription::update( getSubscriptionId(), array( "shipping_address" => $_POST['shipping_address']));
     $jsonResponse = array("forward" => "/ssp-php/subscription");
     print json_encode($jsonResponse, true);
  } catch ( ChargeBee_APIError $e ) {     
     $jsonError = $e->getJsonObject();
     header("HTTP/1.0 ". $jsonError['http_status_code'] . " Error");
     print json_encode($jsonError, true);
  } catch( Exception $e) {
     $jsonError = array("error_msg" => "Error in updating information");
     header("HTTP/1.0 500 Error");
     print json_encode($jsonError, true);
  }
}


/*
 * Reactivate the subscription from cancel/non-renewing state to active state.
 */
function subscriptionReactivate() {
  header('Content-Type: application/json');
  try {
    $subscriptionId = getSubscriptionId();
    $result = ChargeBee_Subscription::reactivate($subscriptionId);
    $jsonResponse = array("forward" => "/ssp-php/subscription");
    print json_encode($jsonResponse, true);
  } catch ( ChargeBee_APIError $e ) {
     $jsonError = $e->getJsonObject();
     header("HTTP/1.0 ". $jsonError['http_status_code'] . " Error");
     print json_encode($jsonError, true);
  } catch( Exception $e) {
     $jsonError = array("error_msg" => "Error while reactivating subscription");
     header("HTTP/1.0 500 Error");
     print json_encode($jsonError, true);
  } 
}



/*
 * Cancels the Subscription.
 */
function subscriptionCancel() {
  $subscriptionId = getSubscriptionId();
  $cancelStatus = $_POST['cancel_status'];
  $params = array();
  if( $cancelStatus == "cancel_on_next_renewal" ) {
     $params['end_of_term'] = 'true';
  }
  $result = ChargeBee_Subscription::cancel($subscriptionId,$params);
  header("Location: subscription");
}



 
/*
 * Verifying subscription id is present in ChargeBee.
 */
function verifyCredentials($username, $password) {
  try {
    ChargeBee_Subscription::retrieve($username);
    return true;
  } catch ( ChargeBee_APIError $e ) {
     $jsonError = $e->getJsonObject();
     if( $jsonError['error_code'] == "resource_not_found" ) {
        return false;
     } 
     throw $e;
  }  
}
 


?>
