/*
 * Copyright (c) 2014 ChargeBee Inc
 * All Rights Reserved.
 */
package com.chargebee.samples;

import com.chargebee.APIException;
import com.chargebee.Result;
import com.chargebee.exceptions.InvalidRequestException;
import com.chargebee.exceptions.PaymentException;
import com.chargebee.models.Subscription;
import java.io.IOException;
import java.io.PrintWriter;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import static com.chargebee.samples.common.ErrorHandler.*;
import static com.chargebee.samples.common.Utils.validateParameters;

/*
 * Demo on how to use Stripe Pop up to get the customer card information
 * and create a subscription in ChargeBee using the same stripe token
 */
public class StripePopUpJs extends HttpServlet {

  
  
    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        /*
         * Setting the Content-Type as application/json.
         */
        response.setHeader("Content-Type", "application/json;charset=utf-8");
        PrintWriter out = response.getWriter();
        
        validateParameters(request);
        String planId = "basic";
        try{
            /*
             * Passing StripeToken, customer information, shipping information and plan id
             * to the ChargeBee create Subscription API.
             */
            
            Result result = Subscription.create().planId(planId)
                   .customerFirstName(request.getParameter("customer[first_name]"))
                   .customerLastName(request.getParameter("customer[last_name]"))
                   .customerEmail(request.getParameter("customer[email]"))
                   .customerPhone(request.getParameter("customer[phone]"))
                   .cardTmpToken(request.getParameter("stripeToken"))
                   .shippingAddressLine1(request.getParameter("shipping_address[line1"))
                   .shippingAddressLine2(request.getParameter("shipping_address[line2]"))
                   .shippingAddressCity(request.getParameter("shipping_address[city]"))
                   .shippingAddressState(request.getParameter("shipping_address[state]"))
                   .shippingAddressZip(request.getParameter("shipping_address[zip]"))
                   .request();
            
        
             out.write("{\"forward\": \"thankyou.html\"}");
        } catch(PaymentException e) {
            handleTempTokenErrors(e, response, out);
        } catch(InvalidRequestException e){
            handleInvalidRequestErrors(e, response, out, "plan_id");
        } catch(Exception e) {
            handleGeneralErrors(e, response, out);
        } finally {
            out.flush();
        }
        
    }

    @Override
    public String getServletInfo() {
        return "Demo on how to use Stripe Pop up to get the customer card information.";
    }
}
