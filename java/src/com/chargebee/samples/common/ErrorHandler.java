/*
 * Copyright (c) 2014 ChargeBee Inc
 * All Rights Reserved.
 */
package com.chargebee.samples.common;

import com.chargebee.APIException;
import com.chargebee.exceptions.InvalidRequestException;
import com.chargebee.exceptions.OperationFailedException;
import com.chargebee.exceptions.PaymentException;
import com.chargebee.org.json.JSONException;
import com.chargebee.org.json.JSONObject;
import java.awt.datatransfer.StringSelection;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.Arrays;
import javax.servlet.http.HttpServletResponse;


/*
 * This file contains the error handling routines used in the various demos.
 */
public class ErrorHandler {
    
    
    /*
     * This method handles PaymentException with input as card no, 
     * expiry month and year(additional card related parameters can also be passed).
     */
     public static void handlePaymentErrors(PaymentException e,
            HttpServletResponse resp, PrintWriter out) {
         
        e.printStackTrace();
        try {
            JSONObject errorResponse = new JSONObject();
            if (e.param != null && !e.param.isEmpty()) {
                // Error due to card related input parameters
                errorResponse.put("error_param", e.param);
                errorResponse.put("error_msg", "invalid value");
            } else if ("payment_method_verification_failed".equals(e.apiErrorCode)) {
                // Card verification failed
                errorResponse.put("error_msg", " Card verification failed. Please enter a valid card. "
                                                + "If this error persists, please contact us.");
            } else {
                // Card processing is failed or additonal parameters along with card is needed.
                errorResponse.put("error_msg", "Unable to process payment. Please recheck your card details "
                                                 + "or try with a different card.");
            }
            resp.setStatus(HttpServletResponse.SC_BAD_REQUEST);
            out.write(errorResponse.toString());
        } catch (Exception ex) {
            handleGeneralErrors(ex, resp, out);
        }
    }
     
     
   /*
    * This method handles PaymentException when Stripe temp token or Braintree temp token
    * is passed to ChargeBee.
    */
    public static void handleTempTokenErrors(PaymentException e,
            HttpServletResponse resp, PrintWriter out) {
        
        e.printStackTrace();
        try {
            JSONObject errorResponse = new JSONObject();
            if ("payment_method_verification_failed".equals(e.apiErrorCode)) {
                // Card verification failed
                errorResponse.put("error_msg", "Card verification failed. Please enter a valid card."
                                                 +  "If this error persists, please contact us.");
            } else {
                // Card processing is failed
                errorResponse.put("error_msg", "Unable to process payment. Please recheck your card details " 
                                                   + "or try with a different card.");
            }
            resp.setStatus(HttpServletResponse.SC_BAD_REQUEST);
            out.write(errorResponse.toString());
        } catch (Exception ex) {
            handleGeneralErrors(ex, resp, out);
        }
    }
    
    
    /*
     * This method handles PaymentException when try to charge the customer with the existing
     * stored card fails.
     */
    public static void handleChargeAttemptFailureErrors(PaymentException e,
            HttpServletResponse resp, PrintWriter out) {
        
        e.printStackTrace();
        try {
            JSONObject errorResponse = new JSONObject();
            if ("payment_processing_failed".equals(e.apiErrorCode)) {
                errorResponse.put("error_msg", "We are unable to process the payment using the existing card information. "
                        + "Please update your account with a valid card and try again.");
            } else if ("payment_method_not_present".equals(e.apiErrorCode)) {
                errorResponse.put("error_msg", "We couldn't process the payment because your account doesn't have a card associated with it."
                        + "Please update your account with a valid card and try again.");
            } else {
                errorResponse.put("error_msg", "Couldn't charge the subscription");
            }
            resp.setStatus(HttpServletResponse.SC_BAD_REQUEST);
            out.write(errorResponse.toString());
        } catch (Exception ex) {
            handleGeneralErrors(ex, resp, out);
        }
    }
    
    
    /*
     * This method handles coupon errors from ChargeBee.
     * Coupon error from ChargeBee is returned as InvalidRequestException 
     * with param value as coupon param name.
     */ 
    public static void handleCouponErrors(InvalidRequestException e,
            HttpServletResponse resp, PrintWriter out) {
        
        e.printStackTrace();
        try {
            
            JSONObject errorResponse = new JSONObject();
            if ("resource_not_found".equals(e.apiErrorCode)) {
                // Coupon is not found in ChargeBee
                errorResponse.put("error_msg", "It seems you have entered an incorrect coupon code. "
                        + "That's okay. Have Faith. Please recheck your code and try again.");
            } else if ("resource_limit_exhausted".equals(e.apiErrorCode)) {
                // Coupon is found in ChargeBee but it has expired or maximum redemption is reached
                errorResponse.put("error_msg", "We are sorry. What we have here is a dead coupon code. Allow us to explain. "
                        + "It has either been redeemed or expired.");
            } else if ("invalid_request".equals(e.apiErrorCode)) {
                // Coupon is found in ChargeBee but the coupon cannot be associated with passed plan ID or addon ID.
                errorResponse.put("error_msg", "It seems you're trying to fit a round peg in a square hole. "
                        + "This coupon code is not applicable to this particular purchase.");
            } else {
                // Unknown coupon error from ChargeBee.
                errorResponse.put("error_msg", "This coupon code cannot be applied.");
            }
            resp.setStatus(HttpServletResponse.SC_BAD_REQUEST);
            out.write(errorResponse.toString());
            
        } catch (Exception ex) {
            handleGeneralErrors(ex, resp, out);
        }
    }
    
    
    /*
     * This method handles InvalidRequestException from ChargeBee and handles 
     * api_error_code of "invalid_request" type.
     */
    public static void handleInvalidRequestErrors(InvalidRequestException e,
            HttpServletResponse resp, PrintWriter out, String...param) {

        e.printStackTrace();
        try {
            JSONObject errorResponse = new JSONObject();
            if (e.param == null || e.param.isEmpty() 
                    || (param != null &&  Arrays.asList(param).contains(e.param))) {
                // These errors are not due to user input but due to incomplete or wrong configuration
                // in your ChargeBee site (such as plan or addon not being present). 
                // These errors should not occur when going live.
                resp.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
                errorResponse.put("error_msg", "Service has encountered an error. Please contact us.");
            } else {
                // These are parameter errors from ChargeBee. Validate the parameters passed to ChargeBee and 
		// ensure that these errors are not thrown from ChargeBee.
                resp.setStatus(HttpServletResponse.SC_BAD_REQUEST);
                errorResponse.put("error_param", e.param);
                errorResponse.put("error_msg", "invalid value");
            }
            out.write(errorResponse.toString());
        } catch (Exception ex) {
            handleGeneralErrors(ex, resp, out);
        }

    }
 
    
    /*
     * This method handles InvalidRequestException from ChargeBee and handles 
     * api_error_code of "invalid_state_request" and "invalid_request" type.
     */ 
    public static void handleInvalidErrors(InvalidRequestException e,
            HttpServletResponse resp, PrintWriter out) {

        e.printStackTrace();
        try {
            if ("invalid_state_for_request".equals(e.apiErrorCode)) {
                // Error due to invalid state to perform the API call.
                JSONObject errorResponse = new JSONObject();
                errorResponse.put("error_msg", "Invalid state for this operation");
                resp.setStatus(HttpServletResponse.SC_BAD_REQUEST);
                out.write(errorResponse.toString());
            } else {
                handleInvalidRequestErrors(e, resp, out, null);
            }
        } catch (Exception ex) {
            handleGeneralErrors(ex, resp, out);
        }

    }
    
    
    /*
     * Handles general errors during API call.
     */
    public static void handleGeneralErrors(Exception e,
            HttpServletResponse resp, PrintWriter out) {

        e.printStackTrace();
        try {
            JSONObject errorResponse = new JSONObject();
            if (e instanceof OperationFailedException) {
                // This error could be due to unhandled exception in ChargeBee 
                // or your request is being blocked due to too many request
                errorResponse.put("error_msg", "Something went wrong. Please try again later.");
            } else if (e instanceof APIException) {
                // This error could be due to invalid API key or invalid site name 
                // or if any configuration is missing in ChargeBee Admin Console
                errorResponse.put("error_msg", "Sorry, Something doesn't seem right. "
                        + "Please inform us. We will get it fixed.");
            } else if (e instanceof IOException) {
                // This error could be due to communication with ChargeBee has failed.
                // This is generally a temporary error. 
                // Note: Incase it is a read timeout error (and not connection timeout error) 
                // then the api call might have succeeded in ChargeBee.
                // So it is better to log it.
                errorResponse.put("error_msg", "We are facing some network connectivity issues. Please try again.");
            } else {
                // Bug in code. Depending on your code, it could have happened even after successful 
                // ChargeBee API call.
                errorResponse.put("error_msg", "Whoops! Something went wrong. Please inform us. "
                        + "We will get it fixed.");
            }
            resp.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
            out.write(errorResponse.toString());
        } catch (Exception ex) {
            // Bug in code !!!
            e.printStackTrace();
            resp.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
            out.write("{\"error_msg\" : \"Whoops! Something went wrong. Please inform us."
                    + " We will get it fixed.\"}");
        }
    }
}