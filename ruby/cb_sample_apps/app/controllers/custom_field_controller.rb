class CustomFieldController < ApplicationController

 # Demo on how to use Custom Field created at your ChargeBee site and also
 # create a new subscription in ChargeBee.
 def checkout 
  day = params["dob_day"] 
  month = params["dob_month"]
  year = params["dob_year"]
  
  begin
      # Parsing the Date String and coverting it to Date.
      dob = Time.parse("#{day}-#{month}-#{year}").to_i

      # Calling ChargeBee Create Subscription API to create a new subscription
      # in ChargeBee for the passed plan id and customer attributes. 
      # Additionally you can send the custom field parameters created for your
      # ChargeBee site.
      # 
      # To create custom field for your site go to Settings-> Request Custom Field
      # and fill the request form.
      # 
      # For demo puropose plan with id 'basic' is hard coded here.
      
      result = ChargeBee::Subscription.create({
                     :plan_id => "basic",
                     :customer => { :email => params["email"],
                                    :first_name => params["first_name"],
                                    :last_name => params["last_name"],
                                    :phone => params["phone"],
                                    :cf_date_of_birth => dob,
                                    :cf_comics_type => params["comics_type"]
                     }})
      
      # Forwarding to thank you page after subscription created successfully. 
      
      render json: {
        :forward => "thankyou?subscription_id=#{URI.escape(result.subscription.id)}"
      }
      
    rescue ChargeBee::APIError => e
      # ChargeBee exception is captured through APIException and 
      # the error messsage (as JSON) is sent to the client.
      render json: e.json_obj, status: e.json_obj[:http_status_code]
    rescue Exception => e
      # Other errors are captured here and error messsage (as JSON) 
      # sent to the client.
      # Note: Here the subscription might have been created in ChargeBee 
      #       before the exception has occured.
      render status: 500, json: {
        :error_msg => "Error while creating your subscription"
      }
    end
 end

 def thankyou
      
      subscriptionId = params["subscription_id"]
      result = ChargeBee::Subscription.retrieve(subscriptionId)
      dobAsLong = Time.at(result.customer.cf_date_of_birth)
      @dob = dobAsLong.strftime("%m-%b")
      @comicsType = result.customer.cf_comics_type
      
 end

end
