require 'chargebee'

class CheckoutNewController < ApplicationController
 
 def redirect
     
     # Sets the environment for calling the Chargebee API.
     # You need to sign up at ChargeBee app to get this credential.
     ChargeBee.configure(:site => "<your-site>",:api_key => "<your-api-key>") 
          

     
     # Calling ChargeBee Hosted Page API to create a new Subscription for the
     # specified planId and redirecting the customer to the ChargeBee server
     # using the url returned by ChargeBee Hosted Page API.
     # For demo purpose plan with id 'basic' is hard coded here.     
     responseResult = ChargeBee::HostedPage.checkout_new({
                                          :subscription => {:plan_id=>"basic" },
                                          :embed => false })
     

     
     hostedPageUrl = responseResult.hosted_page.url
     # This will redirect to the ChargeBee server.
     redirect_to hostedPageUrl
     
 end

end
