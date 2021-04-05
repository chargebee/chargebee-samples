
# Configuring the server for Checkout.com API calls.
CheckoutSdk.configure do |config|
    config.secret_key = ENV["CHECKOUTDOTCOM_SECRET_KEY"]
    config.public_key = ENV["CHECKOUTDOTCOM_PUBLIC_KEY"]
    config.base_url = 'https://api.sandbox.checkout.com' # for sandbox
  end
ChargeBee.configure(:site => ENV["CHARGEBEE_SITE"],
                    :api_key => ENV["CHARGEBEE_API_KEY"])

