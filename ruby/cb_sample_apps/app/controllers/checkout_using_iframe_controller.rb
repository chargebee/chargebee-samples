
class CheckoutUsingIframeController < ApplicationController

 # User after clicking signup will call this method.
 # This will return the hosted page url with iframe messaging option enabled 
 # and the id of the hosted page.
 def first_step
  plan_id = "basic"
  begin
   
   result = ChargeBee::HostedPage.checkout_new(:subscription => { :plan_id => plan_id },
                                                :customer => params["customer"],
                                                :embed => true,
                                                :iframe_messaging => true );
   

   # Sending hosted page url and hosted page id as response
   
   render json: {
          :url => result.hosted_page.url,
          :hosted_page_id => result.hosted_page.id 
     }
   

rescue ChargeBee::APIError => e
      # ChargeBee exception is captured through APIException and 
      # the error messsage(JSON) is sent to the client.
     render status: e.json_obj[:http_status_code], json: e.json_obj 
  rescue Exception => e
      # Other errors are captured here and error messsage (as JSON) is
      # sent to the client.
     render status: 500, json: {
         :error_msg => "Error while proceeding to payment details page"
     }
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
