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
import static com.chargebee.samples.common.Utils.*;
/**
 * Demo on how to create subscription in ChargeBee using Braintree Js.
 */
public class BraintreeJs extends HttpServlet {

   
    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        /**
         * Setting the Content-Type as application/json.
         */
        response.setHeader("Content-Type", "application/json;charset=utf-8");
        PrintWriter out = response.getWriter();
        String planId = "professional";
        validateParameters(request);
        try {
            /* Creating a subscription in ChargeBee by passing the encrypted 
             * card number and card cvv provided by Braintree Js.
             */
            
            Result result = Subscription.create()
                                .planId(planId)
                                .customerFirstName(request.getParameter("customer[first_name]"))
                                .customerLastName(request.getParameter("customer[last_name"))
                                .customerEmail(request.getParameter("customer[email]"))
                                .customerPhone(request.getParameter("customer[phone]"))
                                .cardNumber(request.getParameter("card[number]"))//Would have been encrypted in client
                                .cardCvv(request.getParameter("card[cvv]"))//Would have been encrypted in client
                                .cardExpiryMonth(Integer.valueOf(request.getParameter("card[expiry_month]")))
                                .cardExpiryYear(Integer.valueOf(request.getParameter("card[expiry_year]")))
                                .request();
            
            out.write("{\"forward\": \"/braintree-js/thankyou.html\"}");
        } catch(PaymentException e){
            handleTempTokenErrors(e, response, out);
        } catch (InvalidRequestException e) {
            handleInvalidRequestErrors(e, response, out, "plan_id");
        } catch (Exception e) {
            handleGeneralErrors(e, response, out);
        }

    }

    @Override
    public String getServletInfo() {
        return "Demo on how to create subscription in ChargeBee using Braintree Js.";
    }
}
