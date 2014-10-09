<?php
/*
 * This file contains the error handling routines used in the various demos.
 */
function customError400($e = null) {
  include($_SERVER["DOCUMENT_ROOT"]. "/error_pages/400.html");
  header("HTTP/1.0 400 Bad Request");
  if($e != null) {
    throw new Exception($e);
  }
  die();
}
function customError404($e = null) {
  include($_SERVER["DOCUMENT_ROOT"]. "/error_pages/404.html");
  header("HTTP/1.0 404 Not Found");
  if($e != null) {
    throw new Exception($e);
  }
  die();
}

function customError500($e = null) {
  include($_SERVER["DOCUMENT_ROOT"]."/error_pages/500.html");
  header("HTTP/1.0 500 Internal Server Error");
  if( $e != null ) {
   throw new Exception($e);
  }
  die();
}

// Below are error response sent for AJAX request.

/*
 * This method handles ChargeBee_PaymentException with input as card no, 
 * expiry month and year(additional card related parameters can also be passed).
 */
function handlePaymentErrors($e) {
 	error_log("Error : " . json_encode($e->getJSONObject()));
 	$errorResponse = array();
	if ( $e->getParam() != null ) {
		// Error due to card related input parameters
		$errorResponse["error_param"] = $e->getParam();
		$errorResponse["error_msg"] = "invalid value";
 	} else if($e->getApiErrorCode() == "payment_method_verification_failed") {
	 	// Card verification failed
	 	$errorResponse["error_msg"] = "Card verification failed. Please enter a valid card.
		                				If this error persists, please contact us.";
 	} else {
		// Card processing is failed or additonal parameters along with card is needed.
 		$errorResponse["error_msg"]= "Unable to process payment. Please recheck your card details  
										or try with a different card.";
 	}
	header('HTTP/1.0 400 Error');
 	print json_encode($errorResponse, true);
}

/*
 * This method handles ChargeBee_PaymentException when Stripe temp token or Braintree temp token
 * is passed to ChargeBee.
 */
function handleTempTokenErrors($e) {
	error_log("Error : " . json_encode($e->getJSONObject()));
	$errorResponse = array();
	if($e->getApiErrorCode() == "payment_method_verification_failed") {
	 	// Card verification failed
	 	$errorResponse["error_msg"] = "Card verification failed. Please enter a valid card.
		                				If this error persists, please contact us.";
	} else {
		// Card processing is failed
 		$errorResponse["error_msg"]= "Unable to process payment. Please recheck your card details  
										or try with a different card.";
	}
	header('HTTP/1.0 400 Error');
	print json_encode($errorResponse, true);
}

/*
 * This method handles ChargeBee_PaymentException when try to charge the existing subscription fails.
 */
function handleChargeAttemptFailureErrors($e) {
	error_log("Error : " . json_encode($e->getJSONObject()));
 	$errorResponse = array();
	if($e->getApiErrorCode() == "payment_processing_failed") {
		$errorResponse["error_msg"] = "We are unable to process the payment using the existing card information. 
										Please update your account with a valid card and try again.";
	 } else if($e->getApiErrorCode() == "payment_method_not_present") {
	  	$errorResponse["error_msg"] = "We couldn't process the payment because your account doesn't have a card associated with it. 										Please update your account with a valid card and try again.";
	 } else {
		 $errorResponse["error_msg"] = "Couldn't charge the subscription";
	 }
 	header("HTTP/1.0 400 Invalid Request");
 	print json_encode($errorResponse, true);
}

/*
 * This method handles coupon errors from ChargeBee.
 * Coupon error from ChargeBee is returned as ChargeBee_InvalidRequestException 
 * with param value as coupon param name.
 */ 
function handleCouponErrors($e) {
 	error_log("Error : " . json_encode($e->getJSONObject()));
	
 	$errorResponse = array();
 	if($e->getApiErrorCode() == "resource_not_found") {
		// Coupon is not found in ChargeBee
   	 	$errorResponse["error_msg"] = "It seems you have entered an incorrect coupon code. 
										That's okay. Have Faith. Please recheck your code and try again.";
 	} else if ($e->getApiErrorCode() == "resource_limit_exhausted") {
		// Coupon is found in ChargeBee but it has expired or maximum redemption is reached
   	 	$errorResponse["error_msg"] = "We are sorry. What we have here is a dead coupon code. Allow us to explain. 
										It has either been redeemed or expired.";
 	} else if ($e->getApiErrorCode() == "invalid_request") {
		// Coupon is found in ChargeBee but the coupon cannot be associated with passed plan ID or addon ID.
   	 	$errorResponse["error_msg"] = "It seems you're trying to fit a round peg in a square hole. 
										This coupon code is not applicable to this particular purchase.";
 	} else {
		// Unknown coupon error from ChargeBee.
 		$errorResponse["error_msg"] = "This coupon code cannot be applied.";
 	} 
	header('HTTP/1.0 400 Error');
 	print(json_encode($errorResponse, true));
	
}

/*
 * This method handles ChargeBee_InvalidRequestException from ChargeBee and handles 
 * api_error_code of "invalid_request" type.
 */
function handleInvalidRequestErrors($e, $param = null) {
 	error_log("Error : " . json_encode($e->getJSONObject()));
	$errorResponse = array();
	if( $e->getParam() == null || ($param != null && is_array($param) && in_array($e->getParam(), $param) ) 
							|| $e->getParam() == $param ) {
		// These errors are not due to user input but due to incomplete or wrong configuration
		// in your ChargeBee site (such as plan or addon not being present). 
	    // These errors should not occur when going live.
		$errorResponse["error_msg"] = "Service has encountered an error. Please contact us.";
		header('HTTP/1.0 500 Internal Server Error');
	} else {
		// These are parameter errors from ChargeBee. Validate the parameters passed to ChargeBee and 
		// ensure that these errors are not thrown from ChargeBee.
		$errorResponse["error_param"] = $e->getParam();
		$errorResponse["error_msg"] = "invalid value";
		header('HTTP/1.0 400 Invalid Request');
	}
	print json_encode($errorResponse,true);
}

/*
 * This method handles ChargeBee_InvalidRequestException from ChargeBee and handles 
 * api_error_code of "invalid_state_request" and "invalid_request" type.
 */ 
function handleInvalidErrors($e){
 if( $e->getApiErrorCode() == "invalid_state_for_request") {
	 // Error due to invalid state to perform the API call.
 	 error_log("Error : " . json_encode($e->getJSONObject()));
	 $errorResponse = array("error_msg" => "Invalid state for this operation");
 	 print json_encode($errorResponse, true);
	 header("HTTP/1.0 400 Invalid Request");
 } else {
 	 handleInvalidRequestException($e);
 } 
}

/*
 * Handles general errors during API call.
 */
function handleGeneralErrors($e) {
 	error_log("Error : " . $e->getMessage());
	$errorResponse = array();
	if( $e instanceof ChargeBee_OperationFailedException  ) {
		// This error could be due to unhandled exception in ChargeBee 
		// or your request is being blocked due to too many request
		$errorResponse["error_msg"] = "Something went wrong. Please try again later.";		
	} else if( $e instanceof ChargeBee_APIError ) {
		// This error could be due to invalid API key or invalid site name 
		// or if any configuration is missing in ChargeBee Admin Console
		$errorResponse["error_msg"] = "Sorry, Something doesn't seem right. Please inform us. We will get it fixed.";		
	} else if( $e instanceof ChargeBee_IOException ) {
		// This error could be due to communication with ChargeBee has failed.
		// This is generally a temporary error. 
		// Note: Incase it is a read timeout error (and not connection timeout error) 
		// then the api call might have succeeded in ChargeBee.
		// So it is better to log it.
		$errorResponse["error_msg"] = "We are facing some network connectivity issues. Please try again.";		
	} else {
		// Bug in code. Depending on your code, it could have happened even after successful 
		// ChargeBee API call.
		$errorResponse["error_msg"] = "Whoops! Something went wrong. Please inform us. We will get it fixed.";
	}
	header("HTTP/1.0 500 Error");
	print json_encode($errorResponse, true);	
}


?>
