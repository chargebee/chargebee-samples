/*
 * Copyright (c) 2013 ChargeBee Inc
 * All Rights Reserved.
 */
package com.chargebee.samples;

import com.chargebee.Result;
import com.chargebee.models.Event;
import com.chargebee.models.Invoice;
import com.chargebee.models.enums.EventType;
import com.chargebee.org.json.JSONException;
import com.chargebee.org.json.JSONObject;
import java.beans.EventSetDescriptor;
import java.io.BufferedReader;
import java.io.IOException;
import java.util.logging.Logger;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

/*
 * Demo on how to add charge for meter billing customer after
 * receiving Invoice Created event through webhook.
 */
public class WebhookHandler extends HttpServlet {

    
    /*
     * Receives the webhook content from ChargeBee.
     */
    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        
        if(!checkIfRequestIsFromChargeBee(request, response)){
            return;
        }
        
        /*
         * Getting the json content from the request.
         */
        BufferedReader reader = request.getReader();

        /* 
         * Assigning the recieved content to ChargeBee Event object.
         */
        Event event = new Event(reader);
        

        
        /*
         * Checking the event type as Invoice Created to add Charge for Meter Billing.
         */
        EventType eventType = event.eventType();
        if (EventType.INVOICE_CREATED.equals(eventType)) {
            new MeterBilling().closePendingInvoice(event.content().invoice());
        }
        
    }

    
    
    /**Check if the request is from chargebee. 
     * You can secure the webhook either using
     *   - Basic Authentication
     *   - Or check for specific value in a parameter.
     *<br/>
     * For demo purpose we are using the second option though basic auth is strongly
     * preferred. Also store the key securely in the server rather than hard coding in code.
     */
    private static boolean checkIfRequestIsFromChargeBee(HttpServletRequest req,
            HttpServletResponse resp) throws IOException{
        if(!"DEMO_KEY".equals(req.getParameter("webhook_key"))){
            resp.sendError(HttpServletResponse.SC_FORBIDDEN,"webhook_key not correct");
            return false;
        }
        return true;
    }
    
    
    /**
     * Returns a short description of the servlet.
     *
     * @return a String containing servlet description
     */
    @Override
    public String getServletInfo() {
        return "Demo on how to add charge for meter billing customer after " +
                "receiving Invoice Created event through webhook.";
    }
}
