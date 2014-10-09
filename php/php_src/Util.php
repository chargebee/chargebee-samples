<?php
/*
 * Escapes the content passed in parameter.
 */
function esc($content) {
  if( $content == null ) {
   return "";
  }
  return htmlspecialchars($content);
}

function validateParameters($req){
	/* Your own custom implementation for validating form input parameters.
	 *
	 * Please visit ChargeBee apidocs(https://apidocs.chargebee.com/docs/api?lang=php) 
	 * for each input parameters validation constraint.
	 *
	 * Please validate as per the rules specified in apidocs for each parameter 
	 * and then call ChargeBee API to avoid parameter errors from ChargeBee.
     */
}

/*
 * Checks the session variable is set for the logged in user.
 */
function authenticate() {
  if( getSubscriptionId() == null || getCustomerId() == null ) {
    return false;
  }
  return true;
}

/*
 * Gets the subscription Id from the session variable if set in session
 */
function getSubscriptionId() {
  $subscriptionId = null;
  session_start();
  if(isset($_SESSION['subscription_id']) ) {
     $subscriptionId = $_SESSION['subscription_id'];
  }
  return $subscriptionId;
}

/*
 * Gets the customer Id from the session variable if set in session
 */
function getCustomerId() {
  $customerId = null;
  session_start();
  if(isset($_SESSION['customer_id']) ) {
     $customerId = $_SESSION['customer_id'];
  }
  return $customerId;
}

/*
 * Get the class name for each subscription state
 */
function subscriptionStatus() {
  $subStatus = array();
  $subStatus['active'] = "label-success";
  $subStatus['in_trial'] = "label-default";
  $subStatus['non_renewing'] = "label-warning";
  $subStatus['cancelled'] = "label-danger";
  $subStatus['future'] = "label-primary";
  return $subStatus;
}

/*
 * Retrieves the shipping address if found in ChargeBee
 */
function getShippingAddress($subscriptionId) {
 $addressReqParams = array("subscriptionId"=> $subscriptionId, 
                           "label"=> "shipping_address");

 $shippingAddress = null;
 try {
     $result = ChargeBee_Address::retrieve($addressReqParams);
     return $result->address();
 } catch (ChargeBee_APIError $e) {
    $jsonError = $e->getJsonObject();
    if( $jsonError['error_code'] != "resource_not_found" ) {
      throw $e;
    }
    return null;
 }

}

/*
 * Get the list of Country and its Codes.
 */
function getCountryCodes() {
  $filePath = $_SERVER['DOCUMENT_ROOT'] . "/ssp-php/country_code.txt";
  $countryCodes = array();
  $content = file_get_contents($filePath);
  $countryCodeArray = split(":" , $content);
  foreach($countryCodeArray as $line ) {
    $cc = split("," , $line);
    if(sizeof($cc) == 2) {
       $countryCodes[$cc[0]] = $cc[1];    
    }
  }
  return $countryCodes;
}

?>
