<?php

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
