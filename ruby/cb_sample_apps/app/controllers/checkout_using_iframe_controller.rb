require 'error_handler'
require 'validation'

class CheckoutUsingIframeController < ApplicationController

 # User after clicking signup will call this method.
 # This will return the hosted page url with iframe messaging option enabled 
 # and the id of the hosted page.
 def first_step
  Validation.validateParameters(params)
  plan_id = "basic"
  begin
   
   result = ChargeBee::HostedPage.checkout_new(
           :subscription => { :plan_id => plan_id },
           :customer => params["customer"],
           :embed => true,
           :iframe_messaging => true 
        );
   

   # Sending hosted page url and hosted page id as response
   
   render json: {
          :url => result.hosted_page.url,
          :hosted_page_id => result.hosted_page.id, 
          :site_name => ENV["CHARGEBEE_SITE"]
     }
   

  rescue ChargeBee::InvalidRequestError => e
    ErrorHandler.handle_invalid_request_errors(e, self, "subscription[plan_id]")
  rescue Exception => e
    ErrorHandler.handle_general_errors(e, self)
  end
  
 end

 # After checkout the customer will be taken to redirect handler and a check has been made 
 # whether the checkout is successful. If successful, then he will be taken to the thank you page.
 def redirect_handler
   
   id = params['id']
   result = ChargeBee::HostedPage.retrieve(id) 
   if result.hosted_page.state != "succeeded"
      redirect_to "/400"
   else
      redirect_to "/checkout_iframe/thankyou?subscription_id=#{URI.escape(result.hosted_page.content.subscription.id)}"
   end
   
 end
 
 # Show thank you page
 def thankyou
  @result = ChargeBee::Subscription.retrieve(params['subscription_id'])
 end

end
