require 'chargebee'

class RedirectHandlerController < ApplicationController

 def redirect
   
   # The request will have hosted page id and state of the checkout   
   # which helps in getting the details of subscription created using 
   # ChargeBee checkout hosted page.
   if params['state'] == "succeeded"
     # Request the ChargeBee server about the Hosted page state and give the details
     # about the subscription created.
     result =  ChargeBee::HostedPage.retrieve(params['id'])
     hosted_page = result.hosted_page
     if hosted_page.state != "succeeded"
        redirect_to "/400"
        return
     end
   queryParameter = "name=#{URI.escape(hosted_page.content.customer.first_name)}&planId=#{URI.escape(hosted_page.content.subscription.plan_id)}" 
     redirect_to "/#{hosted_page.type}/thankyou.html?#{queryParameter}"
   else
     # If the state is not success then error page is shown to the customer.
     redirect_to "/400"
   end
     

 end

end
