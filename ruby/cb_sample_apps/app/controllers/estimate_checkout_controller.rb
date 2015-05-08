require 'error_handler'
require 'validation'

class EstimateCheckoutController < ApplicationController
 
 # Displays the checkout page with order summary
 def checkout
  begin
   @estimate = estimate_result(params) 
   @coupon = params["coupon"]
  rescue ChargeBee::APIError => e
   redirect_to "/#{e.json_obj[:http_status_code]}"
  rescue Exception => e
   redirect_to "/500"
  end
 end


 # creates the Subscription at Chargebee using addon and coupon, 
 # if it is passed along with request parameters 
 def create
  Validation.validateParameters(params) 
  begin
    
    # Forming create subscription request parameters to ChargeBee
    # Note : Here customer object received from client side is sent directly 
    #        to ChargeBee.It is possible as the html form's input names are 
    #        in the format customer[<attribute name>] eg: customer[first_name] 
    #        and hence the $_POST["customer"] returns an associative array of the attributes.           
    create_subscription_params = {:plan_id => "monthly",
                         :customer => params['customer'],
                         :card => { :number => params['card_no'],
                                    :expiry_month => params['expiry_month'],
                                    :expiry_year => params['expiry_year'],
                                    :cvv => params['cvc'] 
                    }}
   
   # Adding coupon to the create subscription request, if it is set by user
   if params['coupon'] != nil && params['coupon'] != ""
     create_subscription_params['coupon'] = params['coupon']
   end

   
   addons = Array.new
   # Adding addons to the create subscription request parameters
   create_subscription_params[:addons] = addons
   # Adding addon1 to the addons array, if it is set by user.
   if params['wallposters-quantity'] != nil
     addon1 = { :id => "wall-posters", 
                :quantity => params['wallposters-quantity'] 
              }
     addons.push(addon1)
   end
               
 
   # Adding ebook to the addons array, if it is set by user
   if params['ebook'] != nil
     ebook = { :id => "e-book" }
     addons.push(ebook)
   end
   
 
   
   # Sending request to the ChargeBee.
   result =  ChargeBee::Subscription.create( create_subscription_params )  

   # Adds shipping address to the subscription using the subscription Id 
   # returned during create subscription response.
   shipping_address(result, params)
 
   # Forwarding to thank you page.
   render json: { :forward => "thankyou.html" }
               
  rescue ChargeBee::PaymentError=> e
    ErrorHandler.handle_payment_errors(e, self)
  rescue ChargeBee::InvalidRequestError => e
    if e.param == "coupon"
      ErrorHandler.handle_coupon_errors(e,self)
    else
      ErrorHandler.handle_invalid_request_errors(e, self, "plan_id",
                    "addons[id][0]", "addons[id][1]")
    end
  rescue Exception => e
    ErrorHandler.handle_general_errors(e, self)
  end
 end 

 # Estimates the order summary for the selected addons and coupon 
 # during the checkout
 def order_summary
  begin
    @estimate = estimate_result(params)
    @coupon = params['coupon']
  rescue ChargeBee::InvalidRequestError => e
    if e.param == "subscription[coupon]"
      ErrorHandler.handle_coupon_errors(e, self)
    elsif
      ErrorHandler.handle_invalid_request_errors(e, self, "subscription[plan_id]",
                    "addons[id][0]", "addons[id][1]")
    end
    return
  rescue Exception => e
    ErrorHandler.handle_general_errors(e, self )
    return
  end
  render layout: false 
 end


 # Calls the ChargeBee Estimate API to find the total amount for checkout.
 def estimate_result(_params)
  # Forming create subscription estimate parameters to ChargeBee.
  subscription_params = { :plan_id => "monthly" }

  # Adding addon1 to the create subscription estimate request, if it is set by user.
  if _params['coupon'] !=nil
    subscription_params[:coupon] =  _params['coupon']
  end

  addons = Array.new
  # Adding addon1 to the addons array, if it is set by user.
  if _params['wallposters-quantity'] != nil  
    addon1 = { :id => "wall-posters", 
               :quantity => params['wallposters-quantity'] 
             } 
    addons.push addon1
  end

  # Adding ebook to the addons array, if it is set by user.
  if _params['ebook'] != nil
    ebook = { :id => "e-book" }
    addons.push ebook
  end

  # Sending request to the ChargeBee.
  result = ChargeBee::Estimate.create_subscription({
                      :subscription => subscription_params,
                      :addons => addons 
            })  
  return result.estimate
 end


 # Add Shipping address using the subscription id returned from 
 # create subscription response.
 def shipping_address(result, _params)
   result = ChargeBee::Address.update({
      :label => "shipping_address",
      :subscription_id => result.subscription.id,
      :first_name => result.customer.first_name,
      :last_name => result.customer.last_name,
      :addr => _params["addr"],
      :city => _params["extended_addr"],
      :state => _params["city"],
      :zip => _params["zip_code"]
    })

 end
 
end
