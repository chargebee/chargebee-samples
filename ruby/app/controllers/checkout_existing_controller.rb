require 'chargebee'

class CheckoutExistingController < ApplicationController

 def create
     
     # Sets the environment for calling the Chargebee API.
     # You need to sign up at ChargeBee app to get this credential.
     ChargeBee.configure(:site => "<your-site>",:api_key => "<your-api-key>")
     
     
     redirectURL = getCheckoutExistingUrl(params)
     # This will redirect to the ChargeBee server.
     redirect_to redirectURL
     
 end

 
 def getCheckoutExistingUrl(_params)
   # Request the ChargeBee server to get the hosted page url.
   # Passing Timestamp as ZERO to the trial end will immediately change the 
   # subscription from trial state to active state.
   # Note: parameter embed(Boolean.TRUE) can be shown in iframe
   #       whereas parameter embed(Boolean.FALSE) can be shown as seperate page.
   responseResult = ChargeBee::HostedPage.checkout_existing({
                            :subscription => {
	                           :id => _params["subscription_id"],
                                   :trial_end => 0
                                   },
                                   :embed => false
                               })
   responseResult.hosted_page.url
 end
 
end
