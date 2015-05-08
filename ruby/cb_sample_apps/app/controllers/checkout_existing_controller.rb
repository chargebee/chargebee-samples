require 'chargebee'

class CheckoutExistingController < ApplicationController

 def create
     
     redirect_url = get_checkout_existing_url(params)
     # This will redirect to the ChargeBee server.
     redirect_to redirect_url
     
 end

 
 def get_checkout_existing_url(_params)
   # Requesting ChargeBee for the hosted page url.
   # Passing Timestamp as ZERO to the trial end parameter will immediately change the 
   # subscription from trial state to active state.
   # Note: Parameter embed specifies the returned hosted page URL 
   #       is shown in iframe or as seperate page.
   host_url = request.protocol + request.host_with_port
   response_result = ChargeBee::HostedPage.checkout_existing({
           :subscription => {:id => _params["subscription_id"],
                :trial_end => 0 
           },
           :embed => false,
           :redirect_url => host_url + "/checkout_existing/redirect_handler",
           :cancel_url => host_url + "/checkout_existing/profile.html"
        })
   response_result.hosted_page.url
 end
 
end
