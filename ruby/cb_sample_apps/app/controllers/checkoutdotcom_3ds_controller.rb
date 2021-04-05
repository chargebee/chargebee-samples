require 'uri'
require 'net/http'
require 'json'
require 'chargebee'
require 'checkout_sdk'

# Create a Chargebee subscription with a Checkout.com payment method
class CheckoutDotCom3ds
  attr_accessor :wait_duration
  attr_reader :api

# Configuring the server for Checkout.com API calls.
  CheckoutSdk.configure do |config|
    config.secret_key = '<checkout.com-secret-key>'
    config.public_key = 'checkout.com-public key'
    config.base_url = 'https://api.sandbox.checkout.com' # for sandbox
  end
  ChargeBee.configure(site: '<chargebee-site-name>',
                      api_key: '<chargebee-api-key>')

  def initialize
    @api = CheckoutSdk::ApiResource.new
    @payment_id = nil
    @wait_duration = 15
  end
  
  # Create a PaymentRequestSource from a token
  def get_token(token_id)
    payment_request_source = CheckoutSdk::PaymentRequestSource.new
    payment_request_source.type = 'token'
    payment_request_source.token = token_id
    payment_request_source.amount = 10_000
    payment_request_source.currency = 'USD'
    payment_request_source.threeds_enabled = true
    payment_request_source.capture = false
    payment_request_source
  end

  # Create a Payment Source from a PaymentRequestSource
  def create_checkout_payment_source(token_id)
    # Send API call to Checkout.com
    @api.checkout_connection.reset
    response = @api.request_payment(get_token(token_id))
    # Check whether there's a valid response.
    # If response is invalid then print the response data.
    if response.status > 400
      puts 'Token might have been already used or expired. Check the response from Checkout.com mentioned below'
      puts response.data
      exit
    end
    response_json = JSON.parse(response.data[:body])
    # If the payment is declined then exit the program
    if response_json['status'].eql? 'Declined'
      puts "Reason for Decline: #{response_json['response_summary']}"
      abort 'Payment has been declined so exiting this program'
    end
    response_json
  end



  # Get payment details for a payment id
  def get_payment_intent_info(payment_intent_id)
    # Reset the Excon::Connection before reusing it.
    @api.checkout_connection.reset
    response = @api.get_payment_details(payment_intent_id)
    JSON.parse(response.data[:body])
  end

  # Check whether a payment can be used to create a subscription.
  def payment_authorized?(payment_id)
    check_payment = get_payment_intent_info(payment_id)
    status = check_payment['status']
    puts "Current Payment Status: #{status}"
    case status
    when 'Authorized', 'Card Verified'
      puts 'Payment has been authorized'
      return true
      # when 'Declined'
      #   puts "Reason for Decline: #{check_payment['response_summary']}"
      #   abort 'Payment has been declined so exiting this program'
    end
    puts "3DS Flow hasn't been completed"
    false
  end


  # Create a subscription in Chargebee
  def create_subscription(payment_intent_id)
    result = ChargeBee::Subscription.create({
                                              plan_id: '<chargebee-plan-id>',
                                              auto_collection: 'on',
                                              payment_intent: {
                                                gateway_account_id: '<checkout.com-gateway-id>',
                                                gw_token: payment_intent_id
                                              }
                                            })
    subscription = result.subscription
    puts "Chargebee subscription ID: #{subscription.id} created for Checkout.com payment ID: #{payment_intent_id}"
  end

end

checkout3ds = CheckoutDotCom3ds.new
payment_source = checkout3ds.create_checkout_payment_source('<checkout.com-client-token>')
payment_source_id = payment_source['id']
# Keep trying until a payment is authorized(3DS) or verified(Non-3DS)
until checkout3ds.payment_authorized?(payment_source_id)
  
  redirect_url = payment_source['_links']['redirect']['href']
  puts("Complete 3DS Flow Here: #{redirect_url}")
  
  checkout3ds.wait_duration = 10
  puts 'Payment has not been authorised yet.'
  puts "If you have just authorized the payment then wait for #{checkout3ds.wait_duration} seconds"
  sleep checkout3ds.wait_duration
end
checkout3ds.create_subscription(payment_source_id)