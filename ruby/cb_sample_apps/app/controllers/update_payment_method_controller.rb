class UpdatePaymentMethodController < ApplicationController

  # Retrieves the subscription from ChargeBee and displays the customer details
  def profile
     
     id = params["customer_id"]
     @subscriptionDetail = ChargeBee::Subscription.retrieve(id)
     
     @updated = params["updated"]
     @plan = retrievePlan(@subscriptionDetail.subscription.plan_id) 
  end

  # Redirect the customer to the ChargeBee Update Payment Method Page.
  def update 
     id = params["customer_id"]
     # Calling the ChargeBee Update Payment Method Hosted Page API to update payment 
     # method for a customer by passing the particular customers' customer id.
     # Note : To use this API return url for Update Card API's page must be set. 
     
     host_url = request.protocol + request.host_with_port  
     result = ChargeBee::HostedPage.update_payment_method({ 
          :customer => { :id => id }, 
          :embed=> false,
          :redirect_url => host_url + "/update_payment_method/redirect_handler",
          :cancel_url => host_url + "/update_payment_method/profile?customer_id=#{URI.escape(id)}"
      })    
       
     
     redirect_to result.hosted_page.url
     
  end

  # This method is called on redirection from ChargeBee after Updating Payment Method in ChargeBee.
  def redirect
    # Request the ChargeBee about the Payment Method Hosted Page status 
    # and provides details about the subscripton and customer.
    
    if "succeeded" == params["state"]
       result = ChargeBee::HostedPage.retrieve(params["id"])
       hosted_page = result.hosted_page
       if hosted_page.state != "succeeded"
           redirect_to "/400"
           return
       end
    
    
       id = hosted_page.content.customer.id
       redirect_to "/update_payment_method/profile?customer_id=#{URI.escape(id)}&updated=#{URI.escape("true")}"
    
    else
       redirect_to "/400"
    end
  end

  # Retrieve the plan from ChargeBee for the passed plan Id
  def retrievePlan(_planId)
     result = ChargeBee::Plan.retrieve(_planId)
     return result.plan
  end
  
end
