require 'chargebee'
require 'json'
require 'error_handler'
require 'validation'

class TrialSignupController < ApplicationController
 
 def create
   Validation.validateParameters(params)
    begin
      result = create_subscription(params)
      
      # Forwarding to success page after trial subscription created successfully in ChargeBee.
      render json: {
        :forward => "thankyou.html"
      }
      
    rescue ChargeBee::InvalidRequestError => e
       ErrorHandler.handle_invalid_request_errors(e, self)
    rescue Exception => e
       ErrorHandler.handle_general_errors(e, self)
    end
 end


 def create_subscription(_params)
    
    # Constructing the request parameters and sending request to ChargeBee server 
    # to create a trial subscription. 
    # For demo purpose plan with id 'basic' with trial period 15 days at 
    # ChargeBee app is hard coded here.
    #
    # Note : Here customer object received from client side is sent directly 
    #        to ChargeBee.It is possible as the html form's input names are 
    #        in the format customer[<attribute name>] eg: customer[first_name] 
    #        and hence the $_POST["customer"] returns an associative array of the attributes. 
    result = ChargeBee::Subscription.create({ :plan_id => "basic",
                                              :customer => _params['customer'] })
    return result
   
 end

end
