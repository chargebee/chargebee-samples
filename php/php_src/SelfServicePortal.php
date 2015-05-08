<?php
/*
 * Adding ChargeBee php libraries and configuration files.
 */
require_once(dirname(__FILE__) . "/Config.php");
require_once(dirname(__FILE__) . "/ErrorHandler.php");
require_once(dirname(__FILE__) . "/Util.php");

/*
 * Self Service Portal for customers to manage their subscriptions.
 */
$uri = $_SERVER["REQUEST_URI"];
if($_SERVER["REQUEST_METHOD"] == "GET") {
   if( !authenticate() ) {
     header("Location: /ssp-php/");
     return;
   }
   if( endsWith($uri, "/update_card") ) {
      updateCard();
   } else if( endsWith(substr($uri, 0, strpos($uri,"?")), "/redirect_handler") ) {
      redirectHandler();
   } else if( endsWith(substr($uri, 0, strpos($uri,"?")), "/invoice_as_pdf") )  {
      invoiceAsPdf();
   } else {
      header("HTTP/1.0 400 Error");
      include($_SERVER["DOCUMENT_ROOT"]."/error_pages/400.html");
   }
} else if( $_SERVER["REQUEST_METHOD"] == "POST") {
    if( !endsWith($uri,"/login") && !authenticate()  ) {
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
		customError400();
    }
} else {
	customError500();
}

/*
 * Forwards the user to ChargeBee hosted page to update the card details.
 */
function updateCard() {
 $hostUrl = getHostUrl();
 $customerId =  getCustomerId();
 try {
    $result = ChargeBee_HostedPage::updatePaymentMethod(
             		array("customer"=> array("id" => $customerId),
						  "redirect_url" => $hostUrl . "/ssp-php/redirect_handler",
						  "cancel_url" => $hostUrl . "/ssp-php/subscription",
             	   		  "embed" => "false" ));
    $url = $result->hostedPage()->url;
    header("Location: ". $url);
 } catch(ChargeBee_InvalidRequestException $e) {
	 error_log($e->getMessage());
    $errorResponse = array();
    if($e->getParam() != null && $e->getApiErrorCode() == "resource_not_found" 
	 	 		&& $e->getParam() == "customer[id]" ) {
		customError404($e);
    } else {
    	customError400($e);
    }
 } catch (ChargeBee_APIError $e) {
	error_log($e->getMessage());
    customError400($e);
 } catch (Exception $e) {
	error_log($e->getMessage());
	customError500($e);
 }
}

/*
 * Handles the redirection from ChargeBee on successful card update.
 */
function redirectHandler() {
 $id = $_GET['id'];
 $result = ChargeBee_HostedPage::retrieve($id);
 if( $result->hostedPage()->state == "succeeded" ) {
    header("Location: /ssp-php/subscription");  
 } else {
  	customError400();
 }
}


/*
 * Returns pdf download url for the requested invoice
 */
function invoiceAsPdf() {
  $invoiceId = $_GET['invoice_id'];
  $invoice = ChargeBee_Invoice::retrieve($invoiceId)->invoice();
  if( $invoice->subscriptionId != getSubscriptionId() ) {
    header("HTTP/1.0 400 Error");
    include($_SERVER["DOCUMENT_ROOT"]."/error_pages/400.html");
    return;
  }
  $result = ChargeBee_Invoice::pdf($invoiceId);
  header("Location: " . $result->download()->downloadUrl);
}


/*
 * Authenticates the user and sets the subscription id as session attribute.
 * Here the username should be subscription id in ChargeBee and 
 * password can be anything.
 */
function login(){
  if( fetchSubscription() ) { 
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
  if( isset($_SESSION['customer_id']) ) { 
     unset($_SESSION['customer_id']);
  }
  session_destroy();
  header("Location: /ssp-php/");
  
}


/*
 * Update customer details in ChargeBee.
 */
function updateAccountInfo() {
  header('Content-Type: application/json');
  validateParameters($_POST);
  try {
     $result = ChargeBee_Customer::update(getCustomerId(), 
                                         array( "first_name" => $_POST['first_name'],
                                                "last_name" => $_POST['last_name'],
                                                "email" => $_POST['email'],
                                                "company" => $_POST['company'],
                                                "phone" => $_POST['phone']
                                               ));
     $jsonResponse = array("forward" => "/ssp-php/subscription");
     echo json_encode($jsonResponse, true);
  } catch(ChargeBee_InvalidRequestException $e) {
	 handleInvalidRequestErrors($e);
  } catch (Exception $e) {
	 handleGeneralErrors($e);
 }
  
}



/*
 * Update Billing info of customer in ChargeBee.
 */
function updateBillingInfo() {
  header('Content-Type: application/json');
  validateParameters($_POST);
  $customerId = getCustomerId();
  $billingAddrParams = $_POST['billing_address'];
  try {
     $result = ChargeBee_Customer::updateBillingInfo($customerId, 
                    array("billing_address" => $billingAddrParams));
     $jsonResponse = array("forward" => "/ssp-php/subscription");
     print json_encode($jsonResponse, true);
  } catch(ChargeBee_InvalidRequestException $e) {
  	 handleInvalidRequestErrors($e);
  } catch (Exception $e) {
	 handleGeneralErrors($e);
  }
}


/*
 * Update Shipping address for the customer in ChargeBee.
 */
function updateShippingAddress() {
  header('Content-Type: application/json');
  validateParameters($_POST);
  try {
     $result = ChargeBee_Subscription::update( getSubscriptionId(), array( "shipping_address" => $_POST['shipping_address']));
     $jsonResponse = array("forward" => "/ssp-php/subscription");
     print json_encode($jsonResponse, true);
  } catch(ChargeBee_InvalidRequestException $e) {
     handleInvalidRequestErrors($e);
  } catch (Exception $e) {
     handleGeneralErrors($e);
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
 } catch(ChargeBee_PaymentException $e) {
	 handleChargeAttemptFailureErrors($e);
  } catch(ChargeBee_InvalidRequestException $e) {
	 handleInvalidErrors($e);
  } catch(Exception $e) {
     handleGeneralErrors($e);
  } 
}



/*
 * This method will be executed for cancel subscription request.
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
function fetchSubscription() {
  $subscriptionId = $_POST['subscription_id'];
  if( empty($subscriptionId) ) {
    return false;
  }
  try {
    $result = ChargeBee_Subscription::retrieve($subscriptionId);
    session_start();
    $_SESSION['subscription_id'] = $result->subscription()->id;
    $_SESSION['customer_id'] = $result->customer()->id;
    return true;
  } catch ( ChargeBee_APIError $e ) {
     if( $e->getApiErrorCode() == "resource_not_found" ) {
        return false;
     } 
     throw $e;
  }  
}
 

?>
