/*
 * Copyright (c) 2014 ChargeBee Inc
 * All Rights Reserved.
 */
package com.chargebee.samples;

import com.chargebee.APIException;
import com.chargebee.Result;
import com.chargebee.internal.Request;
import com.chargebee.models.Address;
import com.chargebee.models.Customer;
import com.chargebee.models.HostedPage;
import com.chargebee.models.Invoice;
import com.chargebee.models.Subscription;
import java.io.BufferedReader;
import java.io.File;
import java.io.FileReader;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.HashMap;
import java.util.Map;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;

/*
 * Self Service Portal for customers to manage their subscriptions.
 * 
 */
public class SelfServicePortal extends HttpServlet {
    
    @Override
    public String getServletInfo() {
        return "Self Service Portal for customers to manage their subscription";
    }

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        String path = request.getServletPath();
        if( !authenticate(request) ) {
            response.sendRedirect("/ssp");
            return;
        }
        try {
            if (path.endsWith("/update_card")) {
                updateCard(request, response);
            } else if (path.endsWith("/redirect_handler")) {
                redirectHandler(request, response);
            } else if (path.endsWith("/invoice_as_pdf")) {
                invoiceAsPdf(request, response);
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
        String path = request.getServletPath();
        if( !path.endsWith("/login") &&  !authenticate(request) ) {
            response.sendRedirect("/ssp/");
            return;
        }
        try {
            if (path.endsWith("/login")) {
                login(request, response);
            } else if (path.endsWith("/logout")) {
                logout(request, response);
            } else if (path.endsWith("/update_account_info")) {
                updateAccountInfo(request, response);
            } else if (path.endsWith("/update_billing_info")) {
                updateBillingInfo(request, response);
            } else if (path.endsWith("/update_shipping_address")) {
                updateShippingAddress(request, response);
            } else if (path.endsWith("/sub_cancel")) {
                subscriptionCancel(request, response);
            } else if (path.endsWith("/sub_reactivate")) {
                subscriptionReactivate(request, response);
            } else {
                response.sendError(HttpServletResponse.SC_NOT_FOUND);
            }
        } catch (Exception e) {
            throw new RuntimeException(e);//Will be handled in error servlet.
        }
    }
    
    /*
     * Checks the session variable is set for the logged in user.
     */
    public static boolean authenticate(HttpServletRequest request)  {
        if( getSubscriptionId(request) == null || getCustomerId(request) == null) {
            return false;
        }
        return true;
    }
    
    /*
     * Gets the subscription Id from the session variable if set in session
     */
    public static String getSubscriptionId(HttpServletRequest request) {
        String subscriptionId = null;
        if (request.getSession(false) != null
                && request.getSession(false).getAttribute("subscription_id") != null) {
            subscriptionId = request.getSession(false)
                             .getAttribute("subscription_id").toString();
           
        } 
        return subscriptionId;
    }
    
    
    /*
     * Gets the customer Id from the session variable if set in session
     */
    public static String getCustomerId(HttpServletRequest request) {
        String customerId = null;
        if (request.getSession(false) != null
                && request.getSession(false).getAttribute("customer_id") != null) {
            customerId = request.getSession(false)
                             .getAttribute("customer_id").toString();
           
        } 
        return customerId;
    }

    /*
     * Forwards the user to ChargeBee hosted page to update the card details.
     */
    private void updateCard(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        try {
            Result result = HostedPage.updateCard()
                    .customerId(getCustomerId(request))
                    .embed(Boolean.FALSE).request();
            response.sendRedirect(result.hostedPage().url());
        } catch (Exception e) {
            e.printStackTrace();
            response.sendError(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
        }

    }

    /*
     * Handles the redirection from ChargeBee on successful card update.
     */
    private void redirectHandler(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        String id = request.getParameter("id");
        Result result = HostedPage.retrieve(id).request();
        if (result.hostedPage().state().equals(HostedPage.State.SUCCEEDED)) {
            response.sendRedirect("/ssp/subscription.jsp");
        } else {
            response.sendError(HttpServletResponse.SC_BAD_REQUEST);
        }

    }

    
    /*
     * Returns pdf download url for the requested invoice
     */
    private void invoiceAsPdf(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        //response.setHeader("Content-Type", "application/json;charset=utf-8");
        String invoiceId = request.getParameter("invoice_id");
        Invoice invoice = Invoice.retrieve(invoiceId).request().invoice();
        if( !getSubscriptionId(request).equals(invoice.subscriptionId()) ) {
            response.sendError(HttpServletResponse.SC_BAD_REQUEST);
            return;
        }
        Result result = Invoice.pdf(invoiceId).request();
        response.sendRedirect(result.download().downloadUrl());
    }
    

    /*
     * Authenticates the user and sets the subscription id as session attribute.
     * Here the username should be subscription id in ChargeBee and 
     * password can be anything.
     */
    private void login(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, Exception {

        if ( fetchSubscription(request)) {
            response.sendRedirect("subscription.jsp");
        } else {
            response.sendRedirect("/ssp?login=failed");
        }
    }

    /*
     * Log out the user by invalidating its session
     */
    private void logout(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        if (request.getSession(false) != null) {
            request.getSession(false).invalidate();
        }
        response.sendRedirect("index.jsp");
    }
    
    /*
     * Update customer details in ChargeBee.
     */
    private void updateAccountInfo(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        response.setHeader("Content-Type", "application/json;charset=utf-8");
        PrintWriter out = response.getWriter();
        try {
            Result result = Customer.update(getCustomerId(request))
                    .firstName(request.getParameter("first_name"))
                    .lastName(request.getParameter("last_name"))
                    .company(request.getParameter("company"))
                    .phone(request.getParameter("phone"))
                    .email(request.getParameter("email")).request();

            out.write("{ \"forward\" : \"/ssp/subscription.jsp\" }");
        } catch (APIException e) {
            response.setStatus(e.httpCode);
            out.write(e.toString());
        } catch (Exception e) {
            out.write("{\" error_msg \" : \"Error in updating information\"}");
            response.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
        } finally {
            out.flush();
        }
    }

    
    /*
     * Update Billing info of customer in ChargeBee.
     */
    private void updateBillingInfo(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        response.setHeader("Content-Type", "application/json;charset=utf-8");
        PrintWriter out = response.getWriter();
        try {
            Customer.updateBillingInfo(getCustomerId(request))
                    .billingAddressFirstName(request.getParameter("billing_address[first_name]"))
                    .billingAddressLastName(request.getParameter("billing_address[last_name]"))
                    .billingAddressLine1(request.getParameter("billing_address[line1]"))
                    .billingAddressLine2(request.getParameter("billing_address[line2]"))
                    .billingAddressCity(request.getParameter("billing_address[city]"))
                    .billingAddressState(request.getParameter("billing_address[state]"))
                    .billingAddressCountry(request.getParameter("billing_address[country]"))
                    .billingAddressZip(request.getParameter("billing_address[zip]"))
                    .request();

            out.write("{\"forward\" : \"/ssp/subscription.jsp\"}");
        } catch (APIException e) {
            out.print(e.toString());
            response.setStatus(e.httpCode);
        } catch (Exception e) {
            out.write("{\" error_msg \" : \"Error in updating information\"}");
            response.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
        } finally {
            out.flush();
        }
    }
    

    /*
     * Update Shipping address for the customer in ChargeBee.
     */
    private void updateShippingAddress(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        response.setHeader("Content-Type", "application/json;charset=utf-8");
        PrintWriter out = response.getWriter();
        try {
            Subscription.update(getSubscriptionId(request))
                    .shippingAddressFirstName(request.getParameter("shipping_address[first_name]"))
                    .shippingAddressLastName(request.getParameter("shipping_address[last_name]"))
                    .shippingAddressLine1(request.getParameter("shipping_address[line1]"))
                    .shippingAddressLine2(request.getParameter("shipping_address[line2]"))
                    .shippingAddressCity(request.getParameter("shipping_address[city]"))
                    .shippingAddressState(request.getParameter("shipping_address[state]"))
                    .shippingAddressCountry(request.getParameter("shipping_address[country]")).
                    shippingAddressZip(request.getParameter("shipping_address[zip]")).request();

            out.write("{ \"forward\" : \"/ssp/subscription.jsp\" }");
        } catch (APIException e ) {
            out.write(e.toString());
            response.setStatus(e.httpCode);
        } catch ( Exception e) {
            out.write("{\" error_msg \" : \"Error in updating information\"}");
            response.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
        }
    }

    
    /*
     * Reactivate the subscription from cancel/non-renewing state to active state.
     */
    private void subscriptionReactivate(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        response.setHeader("Content-Type", "application/json;charset=utf-8");
        PrintWriter out = response.getWriter();
        try{
            Subscription.reactivate(getSubscriptionId(request))
                        .request();
            out.write("{ \"forward\" : \"/ssp/subscription.jsp\" }");
        } catch (APIException e ) {
            out.write(e.toString());
            response.setStatus(e.httpCode);
        } catch ( Exception e) {
            out.write("{\" error_msg \" : \"Error while reactivating subscription\"}");
            response.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
        } finally {
            out.flush();
        }
         
    }
    
    
    
    /*
     * Cancels the Subscription.
     */
    private void subscriptionCancel(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        String cancelStatus = request.getParameter("cancel_status");
        Subscription.CancelRequest subscriptionCancelParam = Subscription.cancel(getSubscriptionId(request));

        if ("cancel_on_next_renewal".equals(cancelStatus)) {
            subscriptionCancelParam.endOfTerm(Boolean.TRUE);
        }
        subscriptionCancelParam.request();
        response.sendRedirect("/ssp/subscription.jsp");
    }
    

    
    
    
    /*
     * Verifying subscription id is present in ChargeBee.
     */
    private boolean fetchSubscription(HttpServletRequest request) throws IOException {
        try {
            String username = request.getParameter("subscription_id");
            if(username == null || username.isEmpty()) {
                return false;
            }
            Result result = Subscription.retrieve(username).request();
            HttpSession session = request.getSession();
            session.setAttribute("subscription_id", result.subscription().id());
            session.setAttribute("customer_id", result.customer().id());
            return true;
        } catch (APIException ex) {
            if ("resource_not_found".equals(ex.code)) {
                return false;
            }
            throw ex;
        }
    }
    

    
    /*
     * Return Shipping Address if it is found in ChargeBee.
     */
    public static Address getShippingAddress(String subscriptionId) throws IOException {

        try {
            Result result = Address.retrieve().label("shipping_address")
                    .subscriptionId(subscriptionId).request();
            return result.address();
        } catch( APIException e )  {
            if(!e.code.equals("resource_not_found")) {
                throw e;
            }
            return null;
        } 
        
    }
    
    public static String countryCodeFilePath() {
        return "ssp/country_code.txt";
    }
    
    /*
     * Get the list of Country and its Codes.
     */
    public static Map<String,String> getCountryCode(String path) throws Exception {
        BufferedReader bufferedReader = null;
        File file = new File(path);
        System.out.println("Reading Country codes from file " + file.getAbsolutePath());
        Map<String, String> m = new HashMap();
        try {
            bufferedReader = new BufferedReader(new FileReader(file));
            String currentLine;
            while ((currentLine = bufferedReader.readLine()) != null) {
                String[] line = currentLine.split(":");
                for (String countryCode : line) {
                    String[] cc = countryCode.split(",");
                    if( cc.length == 2 ) {
                       m.put(cc[0], cc[1]);
                    }
                }
            }
        } catch(Exception e){
            throw new RuntimeException(e);
        } finally {
            if( bufferedReader != null) {
               try{
                   bufferedReader.close();
               } catch ( Exception e) {
                   throw new RuntimeException(e);
               }
            }
        }
        return m;
    }
}
