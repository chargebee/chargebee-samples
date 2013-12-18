require 'chargebee'
require 'uri'

# Demo on how to create subscription with ChargeBee API using stripe temporary token and
# adding shipping address to the subscription for shipping of product.
class StripeJsCheckoutController < ApplicationController
  
  def create
    begin
      result = create_subscription(params)
      add_shipping_address(params, result.customer, result.subscription)
      
      # Forwarding to thank you page after successful create subscription.
      render json: {
        :forward => "thankyou.html?name=#{URI.escape(result.customer.first_name)}&planId=#{URI.escape(result.subscription.plan_id)}"
      }
      
    rescue ChargeBee::APIError => e
      # ChargeBee exception is captured through APIException and 
      # the error messsage(JSON) is sent to the client.
      render status: e.json_obj[:http_status_code], json: e.json_obj
    rescue Exception => e
      # Other errors are captured here and error messsage (as JSON) 
      # sent to the client.
      # Note: Here the subscription might have been created in ChargeBee 
      #       before the exception has occured.     
      render status: 500, json: {
        :error_msg => "Error while creating your subscription"
      }
    end
  end
  
  def create_subscription(_params)
    
    # Sends request to the ChargeBee server to create the subscription from
    # the parameters received. The result will have subscription attributes,
    # customer attributes and card attributes.
    result = ChargeBee::Subscription.create({
      :plan_id => "basic", 
      :customer => {
        :email => _params["email"], 
        :first_name => _params["first_name"], 
        :last_name => _params["last_name"], 
        :phone => _params["phone"]
      },
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
      :city => _params["extended_addr"], 
      :state => _params["city"], 
      :zip => _params["zip_code"]
    })
  end

end  
