CbSampleApp::Application.routes.draw do
 
  # checkout 2step
  match 'checkout_two_step/first_step' => 'checkout_two_step#first_step'

  match 'checkout_two_step/redirect_handler' => 'checkout_two_step#redirect_handler'

  match 'checkout_two_step/thankyou' => 'checkout_two_step#thankyou' 

  # update card
  get "update_card/index"

  get "update_card/profile"

  get 'update_card/update' => 'update_card#update'

  match 'update_card/redirect_handler' => 'update_card#redirect'

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

  # custom field checkout
  match 'custom_field/checkout' => 'custom_field#checkout'  
  match 'custom_field/thankyou' => 'custom_field#thankyou'  

  # custom error pages
  match "/400", :to => "errors#error_400"  
  match "/404", :to => "errors#error_404"
  match "/500", :to => "errors#error_500"
  
end
