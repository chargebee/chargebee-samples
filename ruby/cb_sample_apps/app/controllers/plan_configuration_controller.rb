class  PlanConfigurationController < ApplicationController

 def configure
   begin 
      return_parameter = ''
      demo_name = params['demo_name'] 
      if demo_name == "trial_signup"   
         create_plan("Basic","basic", 1000, 15)  
         return_parameter = "demo_name=Trial Signup&plan=Basic"
      elsif demo_name == "checkout_new" 
         create_plan("Basic","basic", 1000, 15)  
         return_parameter = "demo_name=Checkout new&plan=Basic" 
      elsif demo_name == "checkout_two_step"
         create_plan("Basic","basic", 1000, 15)  
         return_parameter = "demo_name=Two-step Checkout&plan=Basic"
      elsif demo_name == "checkout_existing" 
         result = create_subscription("Kim","Burner","kim@acme.com")
         return_parameter = "demo_name=Checkout Existing&plan=Basic&customer=Kim Burner"  
      elsif demo_name == "update_card"	
         result = create_subscription("John","Wayne","john@acmeinc.com")
         return_parameter = "demo_name=Update Card&plan=Basic&customer=John Wayne"
      elsif demo_name == "custom_field"
         create_plan("Basic","basic", 1000, 15)  
         return_parameter = "msg=This tutorial requires custom fields to be created for your ChargeBee site. 
                             Submit your custom field request from your site settings.
                             This demo requires a <b>\"DOB\"</b> and <b>\"Comics Type\"</b>
                             custom fields but you can request for any other fields too."
      elsif demo_name == "stripe_js"
         create_plan("Annual","annual", 2000)
         return_parameter = "demo_name=Stripe Js&plan=Basic"
      elsif demo_name == "estimate"
         create_addon("Wall Posters","wall-posters", 300, "quantity")
         create_addon("E Book","e-book", 200)
         create_plan("Monthly","monthly",600)
         return_parameter = "demo_name=Estimate api&plan=Monthly&addon=E-book&addon=Wall Posters"
      elsif demo_name == "usage_based_billing"
        return_parameter = "msg=To generate a <b>\"Pending\" </b> invoice, you need to enable <b>\"Notify and wait to close invoice\"</b> 
                            in your site settings. Once enabled, try to generate an invoice for a subscription by changing the
                            subscription's plan."
      elsif demo_name == "ssp"
         create_subscription("John","Doe","john@acmeinc.com")
         return_parameter = "demo_name=Self service portal&plan=Basic&customer=John Doe"
      elsif demo_name == "stripe-popup-js"
         create_plan("Basic","basic", 1000, 15)
         return_parameter = "demo_name=Stripe checkout popup&plan=Basic"
      elsif demo_name == "braintree-js"
         create_plan("Professional", "professional",20,10)
         return_parameter = "demo_name=Braintree js Checkout&plan=Professional"
      elsif demo_name == "checkout_iframe"
         create_plan("Basic","basic", 1000, 15)
         return_parameter = "demo_name=Checkout using iFrame&plan=Basic"
      else 
         redirect_to "/400"
	 return
      end
      redirect_to "/index.html?" + URI.escape(return_parameter)
   rescue Exception => e
         redirect_to "/400"
   end
 end

 def create_subscription(first_name, last_name, email)
    plan = create_plan("Basic","basic", 1000, 15)  
    create_subscription_params = { :plan_id => plan.id,
                                   :id => email,
				   :customer => { :first_name => first_name,
                                                 :last_name => last_name,
                                                 :email => email
                                                }
                                 }
    begin
      result = ChargeBee::Subscription.create(create_subscription_params)
      return result
    rescue ChargeBee::APIError => e
      error_json = e.json_obj
      if error_json[:error_param] == "id" and \
                error_json[:error_code] == "param_not_unique"
         result = ChargeBee::Subscription.retrieve(email)
         return result
      else
        raise "Couldn't create the Subscription"
      end
    end
 end

 def create_plan(name, id, price, trial_period = nil) 
   create_plan_params = { :id => id, :name => name,
			 :price => price, :invoice_name => name }
   if trial_period != nil 
      create_plan_params[:trial_period] = trial_period
      create_plan_params[:trial_period_unit] = "day"
   end

   begin
      result = ChargeBee::Plan.create(create_plan_params)
      return result.plan
   rescue ChargeBee::APIError => e
      error_json = e.json_obj
      if error_json[:error_param] == "id" and \
		error_json[:error_code] == "param_not_unique"
         result = ChargeBee::Plan.retrieve(id)
         return result.plan
      else 
        raise "Couldn't create the plan" 
      end
   end
 end

 def create_addon(name, id, price, type = "on_off")
   create_addon_params = { :id => id, :name => name, 
			   :invoice_name => name, :price => price,
                           :charge_type => "recurring",
			   :period => 1, :period_unit => "month",
			   :type => type 
                         }
   if type == "quantity"
     create_addon_params[:unit] = "nos"
   end
  
   begin
      result = ChargeBee::Addon.create(create_addon_params)
      return result.addon
   rescue ChargeBee::APIError => e
      error_json = e.json_obj
      if (error_json[:error_param] == "name" or error_json[:error_param] == "id") and \
               error_json[:error_code] == "param_not_unique"
          result = ChargeBee::Addon.retrieve(id)
      else 
         raise e
      end
   end
 end

end
