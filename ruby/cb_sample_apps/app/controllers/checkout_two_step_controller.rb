require 'json'
require 'error_handler'
require 'validation'

class CheckoutTwoStepController < ApplicationController
 
 # This method calls ChargeBee Checkout new Hosted Page API and passes the
 # shipping address as pass thru content.
 def first_step
   Validation.validateParameters(params)
   # Creating pass thru content which will be sent along the Hosted Page API request
   begin
     
     passThru = { :address => params["addr"],
                :extended_addr => params["extended_addr"],
                :city => params["city"],
                :state => params["state"],
                :zip_code => params["zip_code"]
              }
     
    # Calling ChargeBee Checkout new Hosted Page API to checkout a new subscription
    # by passing plan id the customer would like to subscribe and also passing customer 
    # first name, last name, email and phone details. The response returned by ChargeBee
    # has hosted page url and the customer will be redirected to that url.
    #
    # Note: Parameter embed(Boolean.TRUE) can be shown in iframe
    #        whereas parameter embed(Boolean.FALSE) can be shown as seperate page.
    #
    # Note : Here customer object received from client side is sent directly 
    #        to ChargeBee.It is possible as the html form's input names are 
    #        in the format customer[<attribute name>] eg: customer[first_name] 
    #        and hence the $_POST["customer"] returns an associative array of the attributes.               
    
    result = ChargeBee::HostedPage.checkout_new({:subscription => { :plan_id => "basic" },
                                                :customer => params["customer"],
                                                :embed  => false,
                                                :pass_thru_content => passThru.to_json.to_s
                                              })
       
    
    render json: {
      :forward => result.hosted_page.url 
    }
    
   rescue ChargeBee::InvalidRequestError => e
    ErrorHandler.handle_invalid_request_errors(e, self, "subscription[plan_id]")
    # ChargeBee exception is captured through APIException and 
    # the error messsage(JSON) is sent to the client.
    # render status: e.json_obj[:http_status_code], json: e.json_obj 
   rescue Exception => e
    ErrorHandler.handle_general_errors(e, self)
   end
 end

 # This method is used as redirect url for Checkout new Hosted Page API
 # and adds shipping address if subscription created successfully.
 def redirect_handler
   
   if params["state"] == "succeeded"
     result = ChargeBee::HostedPage.retrieve(params["id"])
   
     hosted_page = result.hosted_page
     if hosted_page.state != "succeeded"
        redirect_to "/400"
        return
     end
     subscription_id = hosted_page.content.subscription.id
     addShippingAddress(subscription_id, result)
     redirect_to "/checkout_two_step/thankyou?subscription_id=#{subscription_id}"
   else 
     redirect_to "/400"
   end
 end

 # This will call ChargeBee Address retrieve API and 
 # display the shipping address for the customer in thankyou.html.erb
 def thankyou
    
    subscriptionId = params["subscription_id"]
    result = ChargeBee::Address.retrieve({ :subscription_id => subscriptionId,
                                             :label => "Shipping Address"
                                           })
   @address = result.address
    
 end

 # Shipping address for the subscription is added after successful create 
 # subscription using ChargeBee Hosted Page API. The shipping address is passed 
 # as pass thru content during Hosted Page API Call and after successful create 
 # subscription the pass thru content is retrieved and using Address API 
 # shipping address is added.
 def addShippingAddress(subscriptionId, result)
     
     passThru = result.hosted_page.pass_thru_content
     shippingAddress =  JSON.parse(passThru)  
     ChargeBee::Address.update({:label => "Shipping Address",
                                :subscription_id => subscriptionId,
                                :addr => shippingAddress.fetch("address"),
                                :extended_addr => shippingAddress.fetch("extended_addr"),
                                :city => shippingAddress.fetch("city"),
                                :state => shippingAddress.fetch("state"),
                                :zip => shippingAddress.fetch("zip_code")
                              })
     
 end

end
