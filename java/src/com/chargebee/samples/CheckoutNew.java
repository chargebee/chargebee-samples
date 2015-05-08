package com.chargebee.samples;
import com.chargebee.Environment;
import com.chargebee.Result;
import com.chargebee.models.HostedPage;
import java.io.IOException;
import java.io.PrintWriter;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import static com.chargebee.samples.common.Utils.*;

/* 
 * Demo on how to create a subscription using ChargeBee Hosted Page API
 * by getting Credit Card details.
 */

public class CheckoutNew extends HttpServlet {

    /**
     * Processes requests for HTTP
     * <code>GET</code> method and redirects the customer to ChargeBee Hosted
     * Page to create a new Subscription.
     *
     * @param request servlet request
     * @param response servlet response
     * @throws ServletException if a servlet-specific error occurs
     * @throws IOException if an I/O error occurs
     */
    protected void processRequest(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        response.setContentType("text/html;charset=UTF-8");

        
        /**
         * Calling Checkout New Hosted Page API to create a new Subscription for the
         * passed plan id.The customers are redirected to the ChargeBee hosted page
         * with the returned response hosted page URL. 
         * 
         * For demo purpose plan with id 'basic' is hard coded here.
         */
        String planId = "basic";
        String hostUrl = getHostUrl(request);
        Result responseResult = HostedPage.checkoutNew()
                .subscriptionPlanId(planId)
                .embed(Boolean.FALSE)
                .redirectUrl(hostUrl + "/checkout_new/redirect_handler")
                .cancelUrl(hostUrl + "/checkout_new/index.html")
                .request();
        
        
        
        String hostedPageUrl = responseResult.hostedPage().url();
        /* 
         * This will redirect the customers to the ChargeBee server.
         */
        response.sendRedirect(hostedPageUrl);
        

    }

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        processRequest(request, response);
    }

    /**
     * Returns a short description of the servlet.
     *
     * @return a String containing servlet description
     */
    @Override
    public String getServletInfo() {
        return "Demo on how to create a subscription using Hosted Page API by getting Credit Card details";
    }
}
