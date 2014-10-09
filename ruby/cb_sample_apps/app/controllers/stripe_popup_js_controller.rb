require 'error_handler'
require 'validation'

class StripePopupJsController < ApplicationController

 # Demo on how to use Stripe Pop up to get the customer card information
 # and create a subscription in ChargeBee using the same stripe token
 def checkout
   Validation.validateParameters(params)
   plan_id = "basic"
   begin
      
      # Passing StripeToken, customer information, shipping information and plan id
      # to the ChargeBee create Subscription API.
      result = ChargeBee::Subscription.create({ :plan_id => plan_id,
                                               :customer => params['customer'],
                                               :card => {:tmp_token => params['stripeToken'] },
                                               :shipping_address => params['shipping_address'] } );
      

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
end
