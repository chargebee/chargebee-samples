CbSampleApp::Application.routes.draw do

  get "/plan_config", to: "plan_configuration#configure"
   
  post "/webhook_handler", to: "webhook_handler#handle"
 
  # checkout 2step
  post '/checkout_two_step/first_step', to: 'checkout_two_step#first_step'

  get '/checkout_two_step/redirect_handler', to: 'checkout_two_step#redirect_handler'

  get '/checkout_two_step/thankyou', to: 'checkout_two_step#thankyou' 

  # update card
  get "/update_payment_method/index"

  get "/update_payment_method/profile"

  get '/update_payment_method/update', to: 'update_payment_method#update'

  get '/update_payment_method/redirect_handler', to: 'update_payment_method#redirect'

  # estimate 
  get "/estimate/checkout", to: "estimate_checkout#checkout"

  post '/estimate/estimate_checkout', to: 'estimate_checkout#create'
  
  get "/estimate/order_summary", to: "estimate_checkout#order_summary"
 
  # stripe js checkout
  post '/stripe_js/checkout', to: 'stripe_js_checkout#create'
  
  # stripe js 3ds checkout
  post '/stripe_js_3ds/checkout', to: 'stripe_js_threeds_checkout#create'

  post '/stripe_js_3ds/confirm_payment', to: 'stripe_js_threeds_checkout#confirmpayment'

  # trial signup
  post '/trial_signup/signup'=> 'trial_signup#create'

  # checkout existing
  post '/checkout_existing/checkout'=> 'checkout_existing#create'

  get '/checkout_existing/redirect_handler', to: 'redirect_handler#redirect'
  
  # For demo purpose we have used a dummy login. After form submit user will be redirected to profile.html  
  post '/checkout_existing/profile.html', to: redirect("/checkout_existing/profile.html")

  # checkout new
  get '/checkout_new/checkout'=> 'checkout_new#redirect'

  get '/checkout_new/redirect_handler', to: 'redirect_handler#redirect'

  #self service portal
  get '/ssp', to: "self_service_portal#index"
  
  post "/ssp/login", to: "self_service_portal#login"

  post "/ssp/logout", to: "self_service_portal#logout"

  get "/ssp/subscription", to: "self_service_portal#subscription"
  
  get "/ssp/update_card", to: "self_service_portal#update_card"
  
  get "/ssp/redirect_handler", to: "self_service_portal#redirect_handler"

  get "/ssp/acc_info_edit", to: "self_service_portal#account_info_edit"
  
  get "/ssp/bill_info_edit", to: "self_service_portal#billing_info_edit"

  get "/ssp/acc_info_edit", to: "self_service_portal#account_info_edit"
  
  get "/ssp/shipping_address_edit", to: "self_service_portal#shipping_address_edit"
  
  get "/ssp/subscription_reactivate", to: "self_service_portal#subscription_reactivate"

  get "/ssp/subscription_cancel", to: "self_service_portal#subscription_cancel"
 
  post "/ssp/sub_reactivate", to: "self_service_portal#sub_reactivate"

  post "/ssp/sub_cancel", to: "self_service_portal#sub_cancel"
   
  post "/ssp/update_account_info", to: "self_service_portal#update_account_info"
  
  post "/ssp/update_billing_info", to: "self_service_portal#update_billing_info"

  post "/ssp/update_shipping_address", to: "self_service_portal#update_shipping_address"
  
  get "/ssp/invoice_list", to: "self_service_portal#invoice_list"
  
  get "/ssp/invoice_as_pdf", to: "self_service_portal#invoice_as_pdf"

  # checkout popup iframe
  post "/checkout_iframe/checkout", to: "checkout_using_iframe#first_step"
 
  get "/checkout_iframe/redirect_handler", to: "checkout_using_iframe#redirect_handler"

  get "/checkout_iframe/thankyou", to: "checkout_using_iframe#thankyou"

  #stripe pop js checkout
  post "/stripe-popup-js/checkout", to: "stripe_popup_js#checkout"
 
  #Braintree js
  get "/braintree-js/signup", to: "braintree_js#signup"
  post "/braintree-js/checkout", to: "braintree_js#checkout"

  #Braintree js 3ds
  get "/braintree-js-3ds/signup", to: "braintree_js_3ds#signup"
  post "/braintree-js-3ds/checkout", to: "braintree_js_3ds#checkout"
  post "/braintree-js-3ds/estimate", to: "braintree_js_3ds#estimate_sub"

  # custom field checkout
  post 'custom_field/checkout', to: 'custom_field#checkout'  
  post 'custom_field/thankyou', to: 'custom_field#thankyou'  

  # custom error pages
  post "/400", to: "errors#error_400"  
  post "/404", to: "errors#error_404"
  post "/500", to: "errors#error_500"
  post "/*", to: "errors#error_404"
end
