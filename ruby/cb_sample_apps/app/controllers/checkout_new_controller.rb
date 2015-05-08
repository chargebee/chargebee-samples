require 'chargebee'

class CheckoutNewController < ApplicationController
 
 def redirect
     
     # Calling Checkout New Hosted Page API to create a new Subscription for the
     # passed plan id. The customers are redirected to the ChargeBee hosted page
     # with the returned response hosted page url. 
     #
     # For demo purpose plan with id 'basic' is hard coded here.  
     host_url = request.protocol + request.host_with_port  
     response_result = ChargeBee::HostedPage.checkout_new({
                    :subscription => {:plan_id => "basic" },
                    :embed => false,
                    :redirect_url => host_url + "/checkout_new/redirect_handler",
                    :cancel_url => host_url + "/checkout_new/index.html" 
                })
     
     
     hosted_page_url = response_result.hosted_page.url
     # This will redirect the customers to the ChargeBee server.
     redirect_to hosted_page_url
     
 end

end
