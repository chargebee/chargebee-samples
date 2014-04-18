class StripePopupJsController < ApplicationController

 # Demo on how to use Stripe Pop up to get the customer card information
 # and create a subscription in ChargeBee using the same stripe token
 def checkout
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
   rescue ChargeBee::APIError => e
      # ChargeBee exception is captured through APIException and 
      # the error messsage(JSON) is sent to the client.
      render status: e.json_obj[:http_status_code], json: e.json_obj
    rescue Exception => e
      # Other errors are captured here and error messsage (as JSON) is 
      # sent to the client.
      render status: 500, json: {
        :error_msg => "Error while creating your subscription"
      }
   end
 end
end
