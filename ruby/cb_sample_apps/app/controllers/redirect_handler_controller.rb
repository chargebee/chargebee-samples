require 'chargebee'

class RedirectHandlerController < ApplicationController

 def redirect
   
   # The redirect URL will have hosted page id and state of the checkout
   # added to it. Using the hosted page id customer, subscription and 
   # other information provided in the checkout could be retrieved.
   if params['state'] == "succeeded"
     # Acknowledge the hosted page id passed in return URL. The response will 
     # have the details of the subscription created through hosted page.
     begin
        result =  ChargeBee::HostedPage.acknowledge(params['id'])
        hosted_page = result.hosted_page
        content = hosted_page.content
        queryParameter = "name=#{URI.escape(content.customer.first_name)}&planId=#{URI.escape(content.subscription.plan_id)}" 
        # Forwarding the user to thank you page
        redirect_to "/#{hosted_page.type}/thankyou.html?#{queryParameter}"
     rescue ChargeBee::InvalidRequestError => e
        # Hosted Page id is already acknowledged.
        if e.api_error_code == "invalid_state_for_request"
           redirect_to "/400"
        else 
           raise e
        end
     end
   else
     #If the state is not success then displaying the error page to the customer
     redirect_to "/400"
   end
     

 end

end
