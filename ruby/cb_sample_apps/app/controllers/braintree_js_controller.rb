
# Demo on how to create subscription in ChargeBee using Braintree Js.
class BraintreeJsController < ApplicationController
 
  def checkout
    plan_id = "professional"
    begin
      
      # Creating a subscription in ChargeBee by passing the encrypted 
      # card number and card cvv provided by Braintree Js.
      create_subscription_params = {:plan_id => plan_id,
                                    :customer => params['customer'],
                                    :card => params['card'] }
      result = ChargeBee::Subscription.create( create_subscription_params )
      
      render json: {
        :forward => "thankyou.html"
      }                            
    rescue ChargeBee::APIError => e
      # ChargeBee exception is captured through APIException and 
      # the error messsage(JSON) is sent to the client.
      render status: e.json_obj[:http_status_code], json: e.json_obj
    rescue Exception => e
      # Other errors are captured here and error messsage (as JSON) 
      # sent to the client.
      render status: 500, json: {
        :error_msg => "Error while creating your subscription"
      }
    end
 
  end
 
end
