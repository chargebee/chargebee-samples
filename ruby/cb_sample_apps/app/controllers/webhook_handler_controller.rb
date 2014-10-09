require 'json'

# Demo on how to add charge for meter billing customer after
# receiving Invoice Created event through webhook.
class WebhookHandlerController < ApplicationController
 
 # Receives the webhook content from ChargeBee.
 def handle

  if check_if_request_is_from_chargebee(params) == false
    return  
  end

  
  # Getting the json content from the request.
  body = request.body.string

  # Assigning the recieved content to ChargeBee Event object.
  event =  ChargeBee::Event.deserialize(body)   
  

  

  # Checking the event type as Invoice Created to add Charge for Meter Billing.
  if event.event_type == "invoice_created" 
     id = event.content.invoice.id
     invoice_obj = ChargeBee::Invoice.retrieve(id).invoice
     if invoice_obj.status == "pending"
        MeterBilling.new.close_pending_invoice(invoice_obj)
        render json: {:message =>  "Invoice has been closed successfully" }
     else
       render json: {:message => "Invoice is not in pending state" }
     end
  else
     render json: {:status => "ok"}
  end
  
 end

 
 
 # Check if the request is from chargebee.
 # You can secure the webhook either using
 #  - Basic Authentication
 #  - Or check for specific value in a parameter.
 # For demo purpose we are using the second option though basic auth is strongly
 # preferred. Also store the key securely in the server rather than hard coding in code.
 def check_if_request_is_from_chargebee(_params) 
   if _params['webhook_key'] != "DEMO_KEY"
      render status: 403, json: {"error_msg" => "webhook_key not correct"}
      return false
   end
   return true
 end



end
