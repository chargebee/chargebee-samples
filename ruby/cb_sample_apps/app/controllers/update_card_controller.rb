class UpdateCardController < ApplicationController

  # Retrieves the subscription from ChargeBee and displays the customer details
  def profile
     
     id = params["customer_id"]
     @subscriptionDetail = ChargeBee::Subscription.retrieve(id)
     
     @updated = params["updated"]
     @plan = retrievePlan(@subscriptionDetail.subscription.plan_id) 
  end

  # Redirect the customer to the ChargeBee Update Card Hosted Page url.
  def update 
     id = params["customer_id"]
     # Calling the ChargeBee Update Card Hosted Page API to update card for 
     # a customer by passing the particular customers' customer id.
     # Note : To use this API return url for Update Card API's page must be set. 
     
     result = ChargeBee::HostedPage.update_card( { :customer => { :id => id }, :embed=> false } )    
       
     
     redirect_to result.hosted_page.url
     
  end

  # This method is used as redirect url for the Update Card Checkout Hosted Page API.
  def redirect
    # Request the ChargeBee server about the Update Card Hosted Page status 
    # and provides details about the subscripton and customer.
    
    if "succeeded" == params["state"]
       result = ChargeBee::HostedPage.retrieve(params["id"])
    
    
       id = result.hosted_page.content.customer.id
       redirect_to "/update_card/profile?customer_id=#{URI.escape(id)}&updated=#{URI.escape("true")}"
    
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
