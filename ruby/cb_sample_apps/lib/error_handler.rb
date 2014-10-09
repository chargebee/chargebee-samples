# This file contains the error handling routines used in the various demos.
class ErrorHandler

  # This method handles ChargeBee::PaymentError with input as card no, 
  # expiry month and year(additional card related parameters can also be passed).
  def self.handle_payment_errors(e, object)
    puts e.json_obj
    error_response = Hash.new
    if e.param != nil
  		# error due to card related input parameters
      error_response[:error_param] = e.param
      error_response[:error_msg] = "invalid value"
    elsif e.api_error_code == "payment_method_verification_failed"
      # card verification failed
      error_response[:error_msg] = "Card verification failed. Please enter a valid card.
                                      If this error persists, please contact us."
    else
      # card processing is failed or additonal parameters along with card is needed.
      error_response[:error_msg] = "Unable to process payment. Please recheck your card details 
                                      or try with a different card."                                  
    end
    return object.render status: 400, json: error_response.to_json
  end

  # This method handles ChargeBee::PaymentError when Stripe temp token or Braintree temp token
  # is passed to ChargeBee.
  def self.handle_temp_token_errors(e, object)
    puts e.json_obj
    error_msg = ""
    if e.api_error_code == "payment_method_verification_failed" 
  	 	# card verification failed
      error_msg = "Card verification failed. Please enter a valid card.
		                				If this error persists, please contact us."
    else
      # card processing is failed
      error_msg = "Unable to process payment. Please recheck your card details  
										or try with a different card."
    end
    return object.render status: 400, json: {
      :error_msg => error_msg
    }
  end
  
  # This method handles ChargeBee::PaymentError when try to charge the existing subscription fails.
  def self.handle_charge_attempt_failure_errors(e, object)
    puts e.json_obj
    error_msg = ""
    if e.api_error_code == "payment_processing_failed"
      error_msg = "We are unable to process the payment using the existing card information. 
                                      Please update your account with a valid card and try again."
    elsif e.api_error_code == "payment_method_not_present"
      error_msg = "We couldn't process the payment because your account doesn't have a card associated with it.
                                      Please update your account with a valid card and try again."
    else
      error_msg = "Couldn't charge the subscription"
    end
    return object.render status: 400, json: {
      :error_msg => error_msg
    }
  end
  
  # This method handles coupon errors from ChargeBee.
  # Coupon error from ChargeBee is returned as ChargeBee::InvalidRequestError 
  # with param value as coupon param name.
  def self.handle_coupon_errors(e, object)
    puts e.json_obj
    
    error_msg = ""
    if e.api_error_code == "resource_not_found"
      # Coupon is not found in ChargeBee
      error_msg = "It seems you have entered an incorrect coupon code. 
                                    That's okay. Have Faith. Please recheck your code and try again."
    elsif e.api_error_code == "resource_limit_exhausted"
      # Coupon is found in ChargeBee but it has expired or maximum redemption is reached
      error_msg = "We are sorry. What we have here is a dead coupon code. Allow us to explain. 
										                It has either been redeemed or expired."
    elsif e.api_error_code == "invalid_request"
      # Coupon is found in ChargeBee but the coupon cannot be associated with passed plan ID or addon ID.
      error_msg = "It seems you're trying to fit a round peg in a square hole. 
                                     This coupon code is not applicable to this particular purchase."
    else
      # Unknown coupon error from ChargeBee.
      error_msg = "This coupon code cannot be applied."
    end  
    return object.render status: 400, json:{
      :error_msg => error_msg
    }           
                       
  end
  
  # This method handles ChargeBee::InvalidRequestError from ChargeBee and handles 
  # api_error_code of "invalid_request" type.
  def self.handle_invalid_request_errors(e, object, *param)
    puts e.json_obj
    if e.param.nil? || e.param.empty? || (!param.nil?  && param.include?(e.param) )
      # These errors are not due to user input but due to incomplete or wrong configuration
      # in your ChargeBee site (such as plan or addon not being present). 
      # These errors should not occur when going live.
      return object.render status: 500, json: {
        :error_msg => "Service has encountered an error. Please contact us."
      }
    else
  		# These are parameter errors from ChargeBee. Validate the parameters passed to ChargeBee and 
      # ensure that these errors are not thrown from ChargeBee.
      return object.render status: 400, json: {
        :error_param => e.param,
        :error_msg => "invalid value"
      }
    end  
  end
  
  # This method handles ChargeBee::InvalidRequestError from ChargeBee and handles 
  # api_error_code of "invalid_state_request" and "invalid_request" type.
  def self.handle_invalid_errors(e, object)
    if e.api_error_code == "invalid_state_for_request"      
      # Error due to invalid state to perform the API call.
      puts e.json_obj
      return object.render status: 400, json: {
        :error_msg => "Invalid state for this operation"
      } 
    else
      return handle_invalid_request_errors(e)
    end
  end
  
  # Handles general errors during API call
  def self.handle_general_errors(e, object)
    puts e.backtrace
    error_msg = ""
    if e.instance_of? ChargeBee::OperationFailedError
  		# This error could be due to unhandled exception in ChargeBee 
  		# or your request is being blocked due to too many request
      error_msg = "Something went wrong. Please try again later."
    elsif e.instance_of? ChargeBee::APIError
  		# This error could be due to invalid API key or invalid site name 
  		# or if any configuration is missing in ChargeBee Admin Console
      error_msg = "Sorry, Something doesn't seem right. Please inform us. We will get it fixed."
    elsif e.instance_of? ChargeBee::IOError
      # This error could be due to communication with ChargeBee has failed.
      # This is generally a temporary error. 
      # Note: Incase it is a read timeout error (and not connection timeout error) 
      # then the api call might have succeeded in ChargeBee.
      # So it is better to log it.
      error_msg = "We are facing some network connectivity issues. Please try again."
    else
  		# Bug in code. Depending on your code, it could have happened even after successful 
  		# ChargeBee API call.
      error_msg = "Whoops! Something went wrong. Please inform us. We will get it fixed."  
    end
    return object.render :status => 500, :json => {
      :error_msg => error_msg
    }
  end
  
end
