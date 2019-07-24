require 'chargebee'
require 'uri'
require 'error_handler'
require 'stripe'

# Demo on how to create subscription with ChargeBee API using stripe payment intent and
# adding shipping address to the subscription for shipping of product.
class StripeJsThreedsCheckoutController < ApplicationController
  
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


  # When client sends a payment method id, then we need to create a payment intent
  # in stripe. For creating a payment intent, we need to specify the exact amount which needs
  # to be put on HOLD. In order to get the estimated amount, we need to call chargebee's
  # create_subscription_estimate api to get the amount.

  # When client sends a payment intent id, then we need to confirm that payment intent
  # in stripe.

  # NOTE:
  # While creating payment intent in stripe, make sure to pass the following two parameters
  # with the same values.
  # "capture_method" => "manual", "setup_future_usage" => "off_session"
  def confirmpayment
    Stripe.api_key = ENV["STRIPE_API_KEY"]
    begin
      if params['payment_method_id']
        # Calling chargebee's create_subscription_estimate api
        estimate = create_subscription_estimate(params)
        
        # Create the PaymentIntent
        intent = Stripe::PaymentIntent.create(
          payment_method: params['payment_method_id'],
          amount: estimate.invoice_estimate.total,
          currency: estimate.invoice_estimate.currency_code,
          confirm: 'true',
          confirmation_method: 'manual',
          capture_method: 'manual',
          setup_future_usage: 'off_session'
        )
        
      elsif params['payment_intent_id']
        # Confirming the payment intent in stripe
        intent = Stripe::PaymentIntent.confirm(params['payment_intent_id'])
      end
    rescue Stripe::CardError => e
      # Display error on client
      return [200, { error: e.message }.to_json]
    end

    return generate_payment_response(intent)
  end

  # Based on the payment intent status, create an appropriate response for client
  # to handle it accordingly.
  # When intent status is 'requires_source_action' or 'requires_action' then client needs
  # to handle extra authentication by calling stripe js function.
  # When intent status is 'requires_capture' then payment intent is ready to be passed into
  # chargebee's endpoint

  
  def generate_payment_response(intent)
    Stripe.api_key = ENV["STRIPE_API_KEY"]
    if (intent.status == 'requires_source_action' || intent.status == 'requires_action') &&
        intent.next_action.type == 'use_stripe_sdk'
      # Inform the client to handle the action
      render json: {
          requires_action: true,
          payment_intent_client_secret: intent.client_secret
        }.to_json
    elsif intent.status == 'requires_capture'
      # The payment didn’t need any additional actions it just needs to be captured
      # Now can pass this on to chargebee for creating subscription
      render json: { success: true, payment_intent_id: intent.id }.to_json
    else
      # Invalid status
      return [500, { error: intent.status }.to_json]
    end
  end
  

  # Call chargebee's create_subscription_estimate api to get the estimated amount
  # for current subscription creation.
  def create_subscription_estimate(_params)
    
    result = ChargeBee::Estimate.create_subscription({
      :billing_address => {
        :line1 => _params['addr'],
        :line2 => _params['extended_addr'],
        :city => _params['city'],
        :stateCode => _params['state'],
        :zip => _params['zip_code'],
        :country => "US"
        },
      :subscription => {
        :plan_id => "basic"
        }
      })
    
    estimate = result.estimate
    return estimate
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
      :customer => {
        :first_name => _params['customer']['first_name'],
        :last_name => _params['customer']['last_name'],
        :email => _params['customer']['email'],
        :phone => _params['customer']['phone']
      },
      :payment_intent => {
        :gw_token => _params['payment_intent_id'],
        :gateway_account_id => "<your-gateway-account-id>"
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
