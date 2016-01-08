CbSampleApp::Application.routes.draw do

  get "plan_config" => "plan_configuration#configure"
   
  match "webhook_handler" => "webhook_handler#handle"
 
  # checkout 2step
  match 'checkout_two_step/first_step' => 'checkout_two_step#first_step'

  match 'checkout_two_step/redirect_handler' => 'checkout_two_step#redirect_handler'

  match 'checkout_two_step/thankyou' => 'checkout_two_step#thankyou' 

  # update card
  get "update_payment_method/index"

  get "update_payment_method/profile"

  get 'update_payment_method/update' => 'update_payment_method#update'

  match 'update_payment_method/redirect_handler' => 'update_payment_method#redirect'

  # estimate 
  get "estimate/checkout" => "estimate_checkout#checkout"

  match 'estimate/estimate_checkout' => 'estimate_checkout#create'
  
  get "estimate/order_summary" => "estimate_checkout#order_summary"
 
  # stripe js checkout
  match 'stripe_js/checkout' => 'stripe_js_checkout#create'
  
  # trial signup
  match 'trial_signup/signup'=> 'trial_signup#create'

  # checkout existing
  match 'checkout_existing/checkout'=> 'checkout_existing#create'

  get 'checkout_existing/redirect_handler', to: 'redirect_handler#redirect'
  
  # For demo purpose we have used a dummy login. After form submit user will be redirected to profile.html  
  post 'checkout_existing/profile.html', to: redirect("/checkout_existing/profile.html")

  # checkout new
  get 'checkout_new/checkout'=> 'checkout_new#redirect'

  get 'checkout_new/redirect_handler', to: 'redirect_handler#redirect'

  #self service portal
  get 'ssp/index' => "self_service_portal#index"
  
  match "ssp/login" => "self_service_portal#login"

  match "ssp/logout" => "self_service_portal#logout"

  get "ssp/subscription" => "self_service_portal#subscription"
  
  get "ssp/update_card" => "self_service_portal#update_card"
  
  get "ssp/redirect_handler" => "self_service_portal#redirect_handler"

  get "ssp/acc_info_edit" => "self_service_portal#account_info_edit"
  
  get "ssp/bill_info_edit" => "self_service_portal#billing_info_edit"

  get "ssp/acc_info_edit" => "self_service_portal#account_info_edit"
  
  get "ssp/shipping_address_edit" => "self_service_portal#shipping_address_edit"
  
  get "ssp/subscription_reactivate" => "self_service_portal#subscription_reactivate"

  get "ssp/subscription_cancel" => "self_service_portal#subscription_cancel"
 
  match "ssp/sub_reactivate" => "self_service_portal#sub_reactivate"

  match "ssp/sub_cancel" => "self_service_portal#sub_cancel"
   
  match "ssp/update_account_info" => "self_service_portal#update_account_info"
  
  match "ssp/update_billing_info" => "self_service_portal#update_billing_info"

  match "ssp/update_shipping_address" => "self_service_portal#update_shipping_address"
  
  get "ssp/invoice_list" => "self_service_portal#invoice_list"
  
  get "ssp/invoice_as_pdf" => "self_service_portal#invoice_as_pdf"

  # checkout popup iframe
  match "checkout_iframe/checkout" => "checkout_using_iframe#first_step"
 
  match "checkout_iframe/redirect_handler" => "checkout_using_iframe#redirect_handler"

  get "checkout_iframe/thankyou" => "checkout_using_iframe#thankyou"

  #stripe pop js checkout
  match "stripe-popup-js/checkout" => "stripe_popup_js#checkout"
 
  #Braintree js
  match "braintree-js/signup" => "braintree_js#signup"
  match "braintree-js/checkout" => "braintree_js#checkout"

  # custom field checkout
  match 'custom_field/checkout' => 'custom_field#checkout'  
  match 'custom_field/thankyou' => 'custom_field#thankyou'  

  # custom error pages
  match "/400", :to => "errors#error_400"  
  match "/404", :to => "errors#error_404"
  match "/500", :to => "errors#error_500"
  
end
