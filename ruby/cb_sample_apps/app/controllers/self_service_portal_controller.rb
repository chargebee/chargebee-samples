
class SelfServicePortalController < ApplicationController
 
 layout :nil
 before_filter :authenticate, :except => [:login, :index]

 # authenticating the user using subscription id in session 
 def authenticate
  @subscription_id = session[:subscription_id] 
  @customer_id = session[:customer_id]
  if @subscription_id == nil || @customer_id == nil
    redirect_to "/ssp/index"
  end
 end

 def index
  if session[:subscription_id] != nil && session[:customer_id] != nil
     redirect_to "/ssp/subscription"
  end
  @login_failed = false
  if params['login'] == "failed" 
    @login_failed = true
  end
 end

  
 # Retrieves the Subscription details from ChargeBee
 def subscription
    @result = ChargeBee::Subscription.retrieve(@subscription_id)
    @billing_address = @result.customer.billing_address
    @country_codes = get_country_codes
    
    @shipping_address = retrieve_shipping_address(@subscription_id)
    @estimate = ChargeBee::Estimate.update_subscription({ :subscription => {:id => @subscription_id } }).estimate
    
    @subscription_status = subscription_status()[@result.subscription.status]
 end
  


 # Forwards the user to ChargeBee hosted page to update the card details.
 def update_card
   begin
     result = ChargeBee::HostedPage.update_card({ :customer => { :id => @customer_id },
                                                   :embed => "false" })
     redirect_to result.hosted_page.url
   rescue Exception => e
     redirect_to "/500"
   end
 end

 # Handles the redirection from ChargeBee on successful card update.
 def redirect_handler
   id = params['id']
   result = ChargeBee::HostedPage.retrieve(id)
   if result.hosted_page.state == "succeeded"
      redirect_to "/ssp/subscription"
   else 
      redirect_to "/400"
   end
 end
 
 # Retrieves the customer information from ChargeBee
 def account_info_edit
   result = ChargeBee::Customer.retrieve(@customer_id)
   @customer = result.customer
 end

 # Retrieves the Billing information from ChargeBee
 def billing_info_edit
   result = ChargeBee::Customer.retrieve(@customer_id)
   @customer = result.customer
   @billing_address = result.customer.billing_address
   @country_codes = get_country_codes
 end 

 # Retrieves the shipping address if found in ChargeBee
 def shipping_address_edit
  @shipping_address = retrieve_shipping_address(@subscription_id)
  @country_codes = get_country_codes
 end

 # Cancels the Subscription
 def subscription_cancel
   @cancel_on_next_renewal = true
  if params['next_renewal'] == "false"
     @cancel_on_next_renewal = false
   end
 end

 
 # list last 20 invoices of the subscription
 def invoice_list
   @list_result = ChargeBee::Invoice.invoices_for_subscription(@subscription_id,
                               { :limit => 20 })

 end
 

 
# Retrieves the pdf download url for the requested invoice
 def invoice_as_pdf
   invoice_id = params['invoice_id']
   invoice = ChargeBee::Invoice.retrieve(invoice_id).invoice
   if invoice.subscription_id != @subscription_id
      redirect_to "/400"
      return
   end
   result = ChargeBee::Invoice.pdf(invoice_id)
   redirect_to result.download.download_url
 end
 

 # Authenticates the user and sets the subscription id as session attribute.
 # Here the username should be subscription id in ChargeBee and 
 # password can be anything.
 def login
    if fetch_subscription(params) 
       redirect_to "/ssp/subscription"
    else
       redirect_to "/ssp/index?login=failed"
    end    
 end
 
 # Log out the user by invalidating its session
 def logout
   session.delete(:subscription_id)
   session.delete(:customer_id)
   redirect_to "/ssp/index"
 end

 # Update customer details in ChargeBee.
 def update_account_info
   begin
     customer_id = @customer_id
     result = ChargeBee::Customer.update(customer_id, {:first_name => params['first_name'],
                                                       :last_name => params['last_name'],
                                                       :email => params['email'],
                                                       :company => params['company'],
                                                       :phone => params['phone']
                                                     })  
     render json: {
        :forward => "/ssp/subscription"
      }
   rescue ChargeBee::APIError => e
     render status: e.json_obj[:http_status_code], json: e.json_obj
   rescue Exception => e
      render status: 500, json: {
        :error_msg => "Error in updating your information"
      }
   end 
 end

 
 # Update Billing info of customer in ChargeBee.
 def update_billing_info
    billing_address = params['billing_address']
    begin 
      ChargeBee::Customer.update_billing_info(@customer_id, :billing_address => billing_address)
      render json: {
        :forward => "/ssp/subscription"
      } 
    rescue ChargeBee::APIError => e
      puts e.json_obj
      render status: e.json_obj[:http_status_code], json: e.json_obj
    rescue Exception => e
      render status: 500, json: {
        :error_msg => "Error in updating your information"
      }
    end

 end
 

 # Update Shipping address for the customer in ChargeBee.
 def update_shipping_address
   begin
      ChargeBee::Subscription.update( @subscription_id,
                                     { :shipping_address => params['shipping_address'] } )
      render json: {
         :forward => "/ssp/subscription"
      }
    rescue ChargeBee::APIError => e
       render status: e.json_obj[:http_status_code], json: e.json_obj
    rescue Exception => e
      render status: 500, json: {
        :error_msg => "Error in updating your information"
      }
    end    
 end
 

 
 # Reactivate the subscription from cancel/non-renewing state to active state.
 def sub_reactivate
   begin
      ChargeBee::Subscription.reactivate(@subscription_id)
   render json: {
         :forward => "/ssp/subscription"
      }
    rescue ChargeBee::APIError => e
       render status: e.json_obj[:http_status_code], json: e.json_obj
    rescue Exception => e
      render status: 500, json: {
        :error_msg => "Error in updating your information"
      }
    end
 end
 

 
 def sub_cancel
   cancel_status = params['cancel_status']
   params = {}
   if cancel_status == "cancel_on_next_renewal"
      puts "Subscription cancel on end of term"
      params[:end_of_term] = "true"
   end
   result = ChargeBee::Subscription.cancel(@subscription_id, params)
   redirect_to "/ssp/subscription"
 end
 

 # Return Shipping Address if it is found in ChargeBee.
 def retrieve_shipping_address(subscription_id) 
    begin
       result = ChargeBee::Address.retrieve({ :subscription_id => subscription_id,
                                              :label => "shipping_address"
                                            })
       return result.address
    rescue ChargeBee::APIError => e 
      if  e.json_obj[:error_code] == "resource_not_found"
        return  nil
      else 
        throw e
      end
    end
 end
 
  
 # Verify Subscription Id is present in ChargeBee.
 def fetch_subscription(_params) 
    subscription_id = _params['subscription_id']
    if subscription_id.blank? == true 
       return false
    end 
    begin
      result = ChargeBee::Subscription.retrieve(subscription_id)
      session[:subscription_id] = result.subscription.id
      session[:customer_id] = result.customer.id
      return true
    rescue ChargeBee::APIError => e
     if e.json_obj[:error_code] == "resource_not_found"
       return false
     end
     throw e
    end
 end
 

 # Get the list of Country and its Codes.
 def get_country_codes
    content = File.read("public/ssp/country_code.txt")
    country_codes = Hash.new
    content.split(":").each do |country_code |
       cc = country_code.split(",")
       if cc.size == 2 
          country_codes[cc[0]] = cc[1]
       end
    end
    return country_codes
 end

 # Get the class name for each subscription state  
 def subscription_status()
    status = { "active" => "label-success",
               "in_trial" => "label-default",
               "non_renewing" => "label-warning",
               "cancelled" => "label-danger",
               "future" => "label-primary"
             }
    return status
 end

end
