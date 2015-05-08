# Handles the meter billing for a subscription after receiving 
# the Invoice Created event through webhook.  
class MeterBilling
  
  # Close the pending invoice by adding the usage charge 
  # as well as addons if any used by the subscription.
  def close_pending_invoice(invoice_obj)
    
    
    invoice_id = invoice_obj.id
    subscription_id = invoice_obj.subscription_id

    start_date = invoice_obj.start_date
    end_date = invoice_obj.end_date
    

    
    charge = get_usage_charge(start_date,end_date,subscription_id)

    # Calling ChargeBee Add Charge Invoice API and add Charge to invoice 
    # based on the usage made by customer.
    ChargeBee::Invoice.add_charge(invoice_id, { 
                  :amount => charge, 
                  :description => "monthly charge" 
            })
    
        
    
    addon_quantity = get_quantity_used(start_date, end_date, subscription_id)
    
    # Calling the ChargeBee Add Addon Charge Invoice API and add the no of 
    # addons used by customers to the invoice.
    ChargeBee::Invoice.add_addon_charge(invoice_id, 
                  :addon_id => "wallpapers" ,
                  :addon_quantity => addon_quantity
            )          
    
        
        
    
    # Closing the invoice and Collecting the payment(if auto collection is on)
    # by calling the ChargeBee Collect Invoice API.
    ChargeBee::Invoice.collect(invoice_id)
            

  end

  # This method gives the amount to be charged based on the usage made by a 
  # subscription from particular start date to end date.
  # For demo purpose the charge is get by random number.
  def get_usage_charge(start_date, end_date, subscription_id)
    random_no = Random.rand(100000)
    return random_no
  end

  # This method gives the no of addons used by a subscription from a particular 
  # start date to end date.
  # For demo purpose using no of quantity is get by random number.  
  def get_quantity_used(start_date, end_date, subscription_id)
    random_no = Random.rand(10)
    return random_no
  end
end
