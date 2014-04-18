/*
 * Copyright (c) 2014 ChargeBee Inc
 * All Rights Reserved.
 */
package com.chargebee.samples;

import com.chargebee.APIException;
import com.chargebee.Result;
import com.chargebee.models.HostedPage;
import com.chargebee.org.json.JSONObject;
import com.chargebee.samples.common.Utils;
import java.io.IOException;
import java.io.PrintWriter;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

/*
 * Demo on how to use iframe messaging parameter during Hosted page API call.
 */
public class CheckoutUsingIframe extends HttpServlet {

  
    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        try {
            String path = request.getServletPath();
            if (path.endsWith("/redirect_handler")) {
                redirectHandler(request, response);
            } else {
                response.sendError(HttpServletResponse.SC_NOT_FOUND);
            }
        } catch (Exception e) {
            throw new RuntimeException(e);//Will be handled in error servlet.
        }
        
    }
    

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        try {
            String path = request.getServletPath();
            if (path.endsWith("/checkout")) {
                callingIframeCheckoutPage(request, response);
            } else {
                response.sendError(HttpServletResponse.SC_NOT_FOUND);
            }
        } catch (Exception e) {
            throw new RuntimeException(e);//Will be handled in error servlet.
        }
        
    }
    
    /*
     * User after clicking signup will call this method.
     * This will return the hosted page url with iframe messaging option enabled 
     * and the id of the hosted page.
     */
    public void callingIframeCheckoutPage(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        
        response.setHeader("Content-Type", "application/json;charset=utf-8");
        PrintWriter out = response.getWriter();
        String planId = "basic";
        try {
            
            Result responseResult = HostedPage.checkoutNew().subscriptionPlanId(planId)
                    .customerFirstName(request.getParameter("customer[first_name]"))
                    .customerLastName(request.getParameter("customer[last_name]"))
                    .customerEmail(request.getParameter("customer[email]"))
                    .customerPhone(request.getParameter("customer[phone]"))
                    .customerCompany(request.getParameter("customer[company]"))
                    .embed(Boolean.TRUE)
                    .iframeMessaging(Boolean.TRUE)
                    .request();
            
            
            
            
            /*
             * Sending hosted page url and hosted page id as response
             */
            
            JSONObject responseJson = new JSONObject();
            responseJson.put("url", responseResult.hostedPage().url());
            responseJson.put("hosted_page_id", responseResult.hostedPage().id());
            out.write(responseJson.toString());
            
        } catch (APIException e) {
            /*
             * ChargeBee exception is captured through APIException and 
             * the error messsage(JSON) is sent to the client.
             */
           response.setStatus(e.httpCode);
           out.write(e.toString());
        } catch (Exception e) {
            e.printStackTrace();
            /*
             * Other errors are captured here and error messsage (as JSON) is 
             * sent to the client.
             */
            response.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
            out.write("{\"error_msg\": \" Error while proceeding to payment details page.\"}");
        } finally {
            out.flush();
        }
    }
    
    
    /*
     * After checkout the customer will be taken to redirect handler and a check has been made 
     * whether the checkout is successful. If successful, then he will be taken to the thank you page.
     */
    public void redirectHandler(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        String id = request.getParameter("id");
        
        Result result = HostedPage.retrieve(id).request();
        HostedPage hostedPage = result.hostedPage();
        if( !hostedPage.state().equals(HostedPage.State.SUCCEEDED) ) {
            response.sendError(HttpServletResponse.SC_BAD_REQUEST);
        } else {
            response.sendRedirect("thankyou.jsp?subscription_id="
                    + Utils.encodeParam(hostedPage.content().subscription().id()));
        }
        
                
    }
    
    @Override
    public String getServletInfo() {
        return "Demo on how to use Chargebee Checkout page with iFrame messaging enabled";
    }
}
