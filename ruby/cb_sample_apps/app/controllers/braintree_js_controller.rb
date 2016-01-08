require 'error_handler'
require 'validation'
# Demo on how to create subscription in ChargeBee using Braintree Js.
class BraintreeJsController < ApplicationController
 
  def checkout
    plan_id = "professional"
    Validation.validateParameters(params)
    begin
      
      # Creating a subscription in ChargeBee by passing the encrypted 
      # card number and card cvv provided by Braintree Js.
      create_subscription_params = {:plan_id => plan_id,
                                    :customer => params['customer'],
                                    :card => {"tmp_token" => params['braintreeToken'] }}
      result = ChargeBee::Subscription.create(create_subscription_params)
      
      render json: {
        :forward => "thankyou.html"
      }                            
    rescue ChargeBee::PaymentError => e
      ErrorHandler.handle_temp_token_errors(e, self)
    rescue ChargeBee::InvalidRequestError => e
       ErrorHandler.handle_invalid_request_errors(e, self, "plan_id")
    rescue Exception => e
       ErrorHandler.handle_general_errors(e, self)
    end
    
  end

 def signup
   @client_token = getBraintreeClientToken() 
   render layout: false
 end
 
 def getBraintreeClientToken
   #return Braintree::ClientToken.generate();
 end
end
