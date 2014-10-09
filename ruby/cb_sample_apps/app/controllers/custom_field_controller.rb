require 'error_handler'
require 'validation'

class CustomFieldController < ApplicationController

 # Demo on how to use Custom Field created at your ChargeBee site and also
 # create a new subscription in ChargeBee.
 def checkout 
  Validation.validateParameters(params)
  day = ( params["dob_day"].to_i > 9 ? "" : "0" ) + params["dob_day"]
  month = ( params["dob_month"].to_i >9 ? "" : "0") + params["dob_month"]
  year = params["dob_year"]

  begin
      # Parsing the Date String and coverting it to Date.
      dob = "#{year}-#{month}-#{day}"

      # Calling ChargeBee Create Subscription API to create a new subscription
      # in ChargeBee for the passed plan id and customer attributes. 
      # Additionally you can send the custom field parameters created for your
      # ChargeBee site.
      # 
      # To create custom field for your site go to Settings-> Request Custom Field
      # and fill the request form.
      # 
      # For demo puropose plan with id 'basic' is hard coded here.
      # Note : Here customer object received from client side is sent directly 
      #        to ChargeBee.It is possible as the html form's input names are 
      #        in the format customer[<attribute name>] eg: customer[first_name] 
      #        and hence the $_POST["customer"] returns an associative array of the attributes.              
      
      customer = params["customer"]
      customer[:cf_date_of_birth] = dob
      result = ChargeBee::Subscription.create({
                     :plan_id => "basic",
                     :customer => customer } )
      
      # Forwarding to thank you page after subscription created successfully. 
      
      render json: {
        :forward => "thankyou?subscription_id=#{URI.escape(result.subscription.id)}"
      }
      
    rescue ChargeBee::InvalidRequestError => e
      ErrorHandler.handle_invalid_request_errors(e, self)
    rescue Exception => e
      ErrorHandler.handle_general_errors(e, self)
    end
 end

 def thankyou
      
      subscriptionId = params["subscription_id"]
      result = ChargeBee::Subscription.retrieve(subscriptionId)
      dob = result.customer.cf_date_of_birth
      @dob = Date.strptime(dob,"%Y-%m-%d").strftime("%b %d")
      @comicsType = result.customer.cf_comics_type
      
 end

end
