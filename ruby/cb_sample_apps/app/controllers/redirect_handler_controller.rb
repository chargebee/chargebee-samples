require 'chargebee'

class RedirectHandlerController < ApplicationController

 def redirect
   
   # The redirect URL will have hosted page id and state of the checkout
   # added to it. Using the hosted page id customer, subscription and 
   # other information provided in the checkout could be retrieved.
   if params['state'] == "succeeded"
     # Retrieving the hosted page and getting the details
     # of the subscription created through hosted page.
     result =  ChargeBee::HostedPage.retrieve(params['id'])
     hosted_page = result.hosted_page
     if hosted_page.state != "succeeded"
        redirect_to "/400"
        return
     end
     content = hosted_page.content
     queryParameter = "name=#{URI.escape(content.customer.first_name)}&planId=#{URI.escape(content.subscription.plan_id)}" 
     # Forwarding the user to thank you page
     redirect_to "/#{hosted_page.type}/thankyou.html?#{queryParameter}"
   else
     #If the state is not success then displaying the error page to the customer
     redirect_to "/400"
   end
     

 end

end
