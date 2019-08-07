package com.chargebee.samples;

import com.chargebee.APIException;
import com.chargebee.Environment;
import com.chargebee.Result;
import com.chargebee.models.HostedPage;
import java.io.IOException;
import java.io.PrintWriter;
import java.sql.Timestamp;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import static com.chargebee.samples.common.Utils.*;
/*
 * Demo on how to Checkout the existing subscription in Trial to Active
 * state by getting card details.
 */
public class CheckoutExisting extends HttpServlet {

    /**
     * Processes HTTP request 
     * <code>POST</code> method and redirects to the Checkout existing page.
     *
     * @param request servlet request
     * @param response servlet response
     * @throws ServletException if a servlet-specific error occurs
     * @throws IOException if an I/O error occurs
     */
    protected void processRequest(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException, Exception {
        
        
        String redirectURL = getCheckoutExistingUrl(request);
        /* 
         * This will redirect to the ChargeBee server.
         */
        response.sendRedirect(redirectURL);
        
    }

    /**
     * Checkouts the existing subscription to active state for the passed 
     * subscription id which is in trial state.
     * @param req HttpServletRequest
     * @return url to checkout the particular subscription
     * @throws IOException 
     */
    
    public String getCheckoutExistingUrl(HttpServletRequest req) 
            throws IOException, Exception {
        String subscriptionId = req.getParameter("subscription_id");
        /* Requesting ChargeBee for the hosted page url.
         * Passing Timestamp as ZERO to the trial end will immediately change the 
         * subscription from trial state to active state.
         * Note: Parameter embed specifies the returned hosted page URL 
         *       is shown in iframe or as seperate page.
         */
        String hostUrl = getHostUrl(req);
        Result responseResult =  HostedPage.checkoutExisting()
                .subscriptionId(subscriptionId)
                .subscriptionTrialEnd(new Timestamp(0))
                .embed(Boolean.FALSE)
                .redirectUrl(hostUrl + "/checkout_existing/redirect_handler")
                .cancelUrl(hostUrl + "/checkout_existing/profile.html")
                .request();
       return responseResult.hostedPage().url();
       
    }
    

    /**
     * Handles the HTTP
     * <code>POST</code> method.
     *
     * @param request servlet request
     * @param response servlet response
     * @throws ServletException if a servlet-specific error occurs
     * @throws IOException if an I/O error occurs
     */
    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        try{
            processRequest(request, response);
        } catch (Exception e) {
            throw new RuntimeException(e);//Will be handled in error servlet.
        }
    }

    /**
     * Returns a short description of the servlet.
     *
     * @return a String containing servlet description
     */
    @Override
    public String getServletInfo() {
        return "redirects to the checkout page for updating the card for the subscription in trial without card.";
    }
}
