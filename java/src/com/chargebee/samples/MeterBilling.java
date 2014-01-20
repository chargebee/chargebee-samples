/*
 * Copyright (c) 2013 ChargeBee Inc
 * All Rights Reserved.
 */
package com.chargebee.samples;

import com.chargebee.Environment;
import com.chargebee.Result;
import com.chargebee.models.Invoice;
import com.chargebee.org.json.JSONObject;
import java.io.IOException;
import java.sql.Timestamp;
import java.util.Random;

/*
 * Handles the meter billing for a subscription after receiving 
 * the Invoice Created event through webhook.
 */
public class MeterBilling {
    
    /*
     * Close the pending invoice by adding the usage charge 
     * as well as addons if any used by the subscription.
     */
    public void closePendingInvoice(Invoice invoiceObj) throws IOException {
        
        
        String invoiceId = invoiceObj.id();
        String subscriptionId = invoiceObj.subscriptionId();
        
        Timestamp startDate = invoiceObj.startDate();
        Timestamp endDate = invoiceObj.endDate();
        

        
        int chargeInCents = getUsageCharge(startDate, endDate, subscriptionId);

        /*
         * Calling ChargeBee Add Charge Invoice API and add Charge to invoice 
         * based on the usage made by customer.
         */
        Invoice.addCharge(invoiceId).amount(chargeInCents)
                                 .description("monthly usage")
                                 .request();
        
        
        
        Integer addonQuantity = getQuantityUsed(startDate, endDate, subscriptionId);
        
        
        /* 
         * Calling the ChargeBee Add Addon Charge Invoice API and add the no of 
         * addons used by customers to the invoice.
         */  
        Invoice.addAddonCharge(invoiceId).addonId("wallpapers")
                .addonQuantity(addonQuantity)
                .request();
        
        
        
        
        /*
         * Closing the invoice and Collecting the payment(if auto collection is on)
         * by calling the ChargeBee Collect Invoice API.
         */
        Invoice.collect(invoiceId).request();
                
    }
    
   /*
    * This method gives the amount to be charged based on the usage made by a 
    * subscription from particular start date to end date.
    * For demo purpose the charge is get by random number.
    */ 
    public static Integer getUsageCharge(Timestamp startDate, Timestamp endDate, 
            String subscriptionId) {
        Random random = new Random();
        int randomNo = random.nextInt(100000);
        System.out.println("Usage amt => " +randomNo);
        return randomNo;
    }
    
    /*
     * This method gives the no of addons used by a subscription from a particular 
     * start date to end date.
     * For demo purpose using no of quantity is get by random number.
     */
    public static Integer getQuantityUsed(Timestamp startDate, Timestamp endDate, 
            String subscriptionId) {
        Random random = new Random();
        int randomNo = random.nextInt(10);
        System.out.println("Quantity => " + randomNo);
        return randomNo;
    }
    
}
