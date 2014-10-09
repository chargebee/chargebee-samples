package com.chargebee.samples;

import com.chargebee.Environment;
import com.chargebee.Result;
import static com.chargebee.samples.common.ErrorHandler.*;
import com.chargebee.exceptions.InvalidRequestException;
import com.chargebee.models.*;
import com.chargebee.models.HostedPage;
import com.chargebee.org.json.*;
import com.chargebee.samples.common.*;
import static com.chargebee.samples.common.Utils.validateParameters;
import java.io.IOException;
import java.io.PrintWriter;
import java.net.*;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

/*
 * Demo on how to create a Subscription using ChargeBee Checkout New Hosted Page 
 * API and add Shipping Address to the subscription after successful create 
 * Subscription in ChargeBee(It is a two step checkout process using pass thru content).
 */
public class CheckoutTwoStep extends HttpServlet {

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        try {
            String path = request.getServletPath();
            if (path.endsWith("/first_step")) {
                checkoutFirstStep(request, response);
            } else {
                response.sendError(HttpServletResponse.SC_NOT_FOUND);
            }
        } catch (Exception e) {
            throw new RuntimeException(e);//Will be handled in error servlet.
        }
    }

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        try {
            String path = request.getServletPath();
            if (path.endsWith("/redirect_handler")) {
                redirectFromChargeBee(request, response);
            } else {
                response.sendError(HttpServletResponse.SC_NOT_FOUND);
            }
        } catch (Exception e) {
            throw new RuntimeException(e);//Will be handled in error servlet.
        }
    }

    /*
     * When a Checkout New API is called, the customer is redirected to a Hosted 
     * Checkout Page. And while calling this API, the shipping address can be passed 
     * as 'pass thru content'. After checkout, the customer is redirected to the 
     * Return URL, while doing this 'pass thru content' can be retrived using 
     * the hosted page ID.
     */
    private void checkoutFirstStep(HttpServletRequest request, HttpServletResponse response)
            throws Exception {
        response.setHeader("Content-Type", "application/json;charset=utf-8");
        PrintWriter out = response.getWriter();
        
        validateParameters(request);
        String planId = "basic";
        try {
            
            /*
             * Creating Pass Thru content as a JSON Object. We store the address in the pass thro content.
             */
            JSONObject passThrough = new JSONObject();
            passThrough.put("address", request.getParameter("addr"));
            passThrough.put("extended_addr", request.getParameter("extended_addr"));
            passThrough.put("city", request.getParameter("city"));
            passThrough.put("state", request.getParameter("state"));
            passThrough.put("zip_code", request.getParameter("zip_code"));
            
        /*
             * Calling ChargeBee Checkout new Hosted Page API to checkout a new subscription
             * by passing plan id the customer would like to subscribe and also passing customer 
             * first name, last name, email and phone details. The response returned by ChargeBee
             * has hosted page url and the customer will be redirected to that url.
             */
            
            Result responseResult = HostedPage.checkoutNew().subscriptionPlanId(planId)
                    .customerFirstName(request.getParameter("customer[first_name]"))
                    .customerLastName(request.getParameter("customer[last_name]"))
                    .customerEmail(request.getParameter("customer[email]"))
                    .customerPhone(request.getParameter("customer[phone]"))
                    .embed(Boolean.FALSE)
                    .passThruContent(passThrough.toString())
                    .request();
            
            
            
            out.write("{\"forward\" : \"" + responseResult.hostedPage().url() + "\"}");
            
        } catch (InvalidRequestException e) {
            handleInvalidRequestErrors(e, response, out, "subscription[plan_id]");
        } catch (Exception e) {
            handleGeneralErrors(e, response, out);
        }
    }

    /* The request will have hosted page id and state of the checkout   
     * which helps in getting the details of subscription created using 
     * ChargeBee checkout hosted page.
     */
    private void redirectFromChargeBee(HttpServletRequest request, HttpServletResponse response)
            throws Exception {
        
            /* Requesting ChargeBee server about the Hosted page state and 
         * getting the details of the created subscription.
         */
        Result result = HostedPage.retrieve(request.getParameter("id")).request();
        HostedPage hostedPage = result.hostedPage();
        if (!hostedPage.state().equals(HostedPage.State.SUCCEEDED)) {
            response.sendError(HttpServletResponse.SC_BAD_REQUEST);
            return;
        }
        
        
        String subscriptionId = hostedPage.content().subscription().id();
        addShippingAddress(subscriptionId, result);
        response.sendRedirect("thankyou.jsp?subscription_id=" + Utils.encodeParam(subscriptionId)); 
    }

    /*
     * Shipping address for the subscription is added after successful create 
     * subscription using ChargeBee Hosted Page API. The shipping address is passed 
     * as pass thru content during Hosted Page API Call and after successful create 
     * subscription the pass thru content is retrieved and using Address API 
     * shipping address is added.
     */
    private void addShippingAddress(String subscriptionId, Result result)
            throws JSONException, IOException {
        
        String passThru = result.hostedPage().passThruContent();
        JSONObject shippingAddress = new JSONObject(passThru);
        /*
         * Calling ChargeBee Address API to update/add address for a subscription.
         */
        Result addrResult = Address.update()
                .label("Shipping Address")
                .subscriptionId(subscriptionId)
                .addr(shippingAddress.getString("address"))
                .extendedAddr(shippingAddress.getString("extended_addr"))
                .city(shippingAddress.getString("city")).state(shippingAddress.getString("state"))
                .zip(shippingAddress.getString("zip_code")).request();
        
    }

    public static Address retrieveAddress(HttpServletRequest request) throws IOException {
        
        Result result = Address.retrieve().subscriptionId(request.getParameter("subscription_id"))
                .label("Shipping Address").request();
        return result.address();
        
    }

    /**
     * Returns a short description of the servlet.
     *
     * @return a String containing servlet description
     */
    @Override
    public String getServletInfo() {
        return "Demo on how to do two step checkout process using pass thru content";
    }
}
