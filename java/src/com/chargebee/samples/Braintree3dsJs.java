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
import org.apache.commons.io.IOUtils;

import com.chargebee.org.json.JSONObject;
import com.chargebee.models.Estimate;
/**
 * Demo on how to create subscription in ChargeBee using Braintree Js.
 */
public class Braintree3dsJs extends HttpServlet {

   
    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        /**
         * Setting the Content-Type as application/json.
         */

        response.setHeader("Content-Type", "application/json;charset=utf-8");
        PrintWriter out = response.getWriter();
        validateParameters(request);

         try {
            String path = request.getServletPath();
            if (path.endsWith("/checkout")) {
                createSubscription(request, out, response);
            } else if (path.endsWith("/estimate")) {
                getSubscriptionEstimate(request, out);
            }
            else {
                response.sendError(HttpServletResponse.SC_NOT_FOUND);
            }
        } catch (Exception e) {
            throw new RuntimeException(e);//Will be handled in error servlet.
        }

    }

    @Override
    public String getServletInfo() {
        return "Demo on how to create subscription in ChargeBee using Braintree Js.";
    }

    protected void createSubscription(HttpServletRequest request, PrintWriter out, HttpServletResponse response)
    {
        try {
            /* Creating a subscription in ChargeBee by passing the encrypted 
             * card number and card cvv provided by Braintree Js.
             */
            
            String planId = "professional"; // replace your plan id
            Result result = Subscription.create()
                .planId(planId)
                .customerFirstName(request.getParameter("customer[first_name]"))
                .customerLastName(request.getParameter("customer[last_name"))
                .customerEmail(request.getParameter("customer[email]"))
                .customerPhone(request.getParameter("customer[phone]"))
                .paymentIntentGatewayAccountId("<braintree-gateway-account-id>")
                .paymentIntentGwToken(request.getParameter("braintreeToken"))
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

    protected void getSubscriptionEstimate(HttpServletRequest request, PrintWriter out) throws Exception {
         String jsonBody = IOUtils.toString(request.getReader());
            JSONObject jsonObject = new JSONObject(jsonBody);
        try {
            
            Result result = Estimate.createSubscription()
                    .subscriptionPlanId(jsonObject.get("sub_plan_id").toString())
                    .request();
            
            out.write(result.estimate().toString());
        }
        catch (Exception e) {
            throw e;
        }
    }
    
}
