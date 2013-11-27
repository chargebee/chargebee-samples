CbSampleApp::Application.routes.draw do
  
  match 'stripe_js/checkout' => 'stripe_js_checkout#create'

  match 'trial_signup/signup'=> 'trial_signup#create'

  match 'checkout_existing/checkout'=> 'checkout_existing#create'

  get 'checkout_new/checkout'=> 'checkout_new#redirect'

  get 'checkout_existing/redirect_handler', to: 'redirect_handler#redirect'

  get 'checkout_new/redirect_handler', to: 'redirect_handler#redirect'

  # For demo purpose we have used a dummy login. After form submit user will be redirected to profile.html  
  post 'checkout_existing/profile.html', to: redirect("/checkout_existing/profile.html")

  # The priority is based upon order of creation:
  # first created -> highest priority.

  # Sample of regular route:
  #   match 'products/:id' => 'catalog#view'
  # Keep in mind you can assign values other than :controller and :action

  # Sample of named route:
  #   match 'products/:id/purchase' => 'catalog#purchase', :as => :purchase
  # This route can be invoked with purchase_url(:id => product.id)

  # Sample resource route (maps HTTP verbs to controller actions automatically):
  #   resources :products

  # Sample resource route with options:
  #   resources :products do
  #     member do
  #       get 'short'
  #       post 'toggle'
  #     end
  #
  #     collection do
  #       get 'sold'
  #     end
  #   end

  # Sample resource route with sub-resources:
  #   resources :products do
  #     resources :comments, :sales
  #     resource :seller
  #   end

  # Sample resource route with more complex sub-resources
  #   resources :products do
  #     resources :comments
  #     resources :sales do
  #       get 'recent', :on => :collection
  #     end
  #   end

  # Sample resource route within a namespace:
  #   namespace :admin do
  #     # Directs /admin/products/* to Admin::ProductsController
  #     # (app/controllers/admin/products_controller.rb)
  #     resources :products
  #   end

  # You can have the root of your site routed with "root"
  # just remember to delete public/index.html.

  # See how all your routes lay out with "rake routes"

  # This is a legacy wild controller route that's not recommended for RESTful applications.
  # Note: This route will make all actions in every controller accessible via GET requests.
  # match ':controller(/:action(/:id))(.:format)'
  
  match "/404", :to => "errors#error_404"
  match "/500", :to => "errors#error_500"
  
end
