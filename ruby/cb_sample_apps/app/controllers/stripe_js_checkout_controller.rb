require 'chargebee'
require 'uri'
require 'error_handler'

# Demo on how to create subscription with ChargeBee API using stripe temporary token and
# adding shipping address to the subscription for shipping of product.
class StripeJsCheckoutController < ApplicationController
  
  def create
    Validation.validateParameters(params)
    begin
      result = create_subscription(params)
      add_shipping_address(params, result.customer, result.subscription)
      
      # Forwarding to thank you page after successful create subscription.
      render json: {
        :forward => "thankyou.html"
      }
      
    rescue ChargeBee::PaymentError => e
      ErrorHandler.handle_temp_token_errors(e, self)
    rescue ChargeBee::InvalidRequestError => e
       ErrorHandler.handle_invalid_request_errors(e, self, "plan_id")
    rescue Exception => e
       ErrorHandler.handle_general_errors(e, self)
    end
  end
  
  def create_subscription(_params)
    
    # Sends request to the ChargeBee server to create the subscription from
    # the parameters received. The result will have subscription attributes,
    # customer attributes and card attributes.
    #
    # Note : Here customer object received from client side is sent directly 
    #        to ChargeBee.It is possible as the html form's input names are 
    #        in the format customer[<attribute name>] eg: customer[first_name] 
    #        and hence the $_POST["customer"] returns an associative array of the attributes.               
    result = ChargeBee::Subscription.create({
      :plan_id => "basic", 
      :customer => _params["customer"],
      :card => {
        :tmp_token => _params['stripeToken'] 
       }
      })
    
    return result
  end
  
  # Adds the shipping address to an existing subscription. The first name
  # & the last name for the shipping address is get from the customer 
  # account information in ChargeBee.
  def add_shipping_address(_params, customer, subscription)
    # Adding address to the subscription for shipping product to the customer.
    # Sends request to the ChargeBee server and adds the shipping address 
    # for the given subscription Id.
    result = ChargeBee::Address.update({
      :subscription_id => subscription.id, 
      :label => "shipping_address", 
      :first_name => customer.first_name, 
      :last_name => customer.last_name, 
      :addr => _params["addr"], 
      :extended_addr => _params["extended_addr"],
      :city => _params["city"], 
      :state => _params["state"], 
      :zip => _params["zip_code"]
    })
  end

end  
