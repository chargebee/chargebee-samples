package com.chargebee.samples;

import com.chargebee.APIException;
import com.chargebee.Environment;
import com.chargebee.Result;
import com.chargebee.exceptions.InvalidRequestException;
import com.chargebee.models.HostedPage;
import com.chargebee.models.Subscription;
import java.io.IOException;
import java.io.PrintWriter;
import java.net.URLEncoder;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import static com.chargebee.samples.common.ErrorHandler.*;
import static com.chargebee.samples.common.Utils.validateParameters;

/*
 * Demo on how to use Custom Field created at your ChargeBee site and also 
 * create a new subscription in ChargeBee.
 */
public class CustomFieldCheckout extends HttpServlet {
    
    /**
     * Processes request for HTTP
     * <code>POST</code> method.
     *
     * @param request servlet request
     * @param response servlet response
     * @throws ServletException if a servlet-specific error occurs
     * @throws IOException if an I/O error occurs
     */
    protected void processRequest(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        response.setHeader("Content-Type", "application/json;charset=utf-8");
        PrintWriter out = response.getWriter();
        
        validateParameters(request);
        String day = String.format("%02d", new Integer(request.getParameter("dob_day")));
        String month = String.format("%02d", new Integer(request.getParameter("dob_month")));
        String year = request.getParameter("dob_year");
        
        String dateString = year + "-" + month + "-" + day;
        
        try{   
            /*
             * Calling ChargeBee Create Subscription API to create a new subscription
             * in ChargeBee for the passed plan id and customer attributes. 
             * Additionally you can send the custom field parameters created for your
             * ChargeBee site.
             * 
             * To create custom field for your site go to Settings-> Request Custom Field
             * and fill the request form.
             * 
             * For demo puropose plan with id 'basic' is hard coded here.
             */
            
            Result responseResult = Subscription.create().planId("basic")
                    .customerFirstName(request.getParameter("customer[first_name]"))
                    .customerLastName(request.getParameter("customer[last_name]"))
                    .customerEmail(request.getParameter("customer[email]"))
                    .customerPhone(request.getParameter("customer[phone]"))
                    // custom field attribute       
                    .param("customer[cf_comics_type]", 
                            request.getParameter("customer[cf_comics_type]")) 
                    // custom field attribute       
                    .param("customer[cf_date_of_birth]", dateString ) 
                    .request();
            
            /*
             * Forwarding to thank you page after subscription created successfully.
             */
            
            String queryParameters = "subscription_id=" + 
                    URLEncoder.encode(responseResult.subscription().id(), "UTF-8");
            out.write("{\"forward\": \"thankyou.jsp?"+ queryParameters + "\"}");
            
        } catch(InvalidRequestException e) {
            handleInvalidRequestErrors(e, response, out, "plan_id");
        }catch(Exception e) {
            handleGeneralErrors(e, response, out);
        } finally {
            out.flush();
        }    
    }

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
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
        return "Demo on how to use Custom Field created at ChargeBee site "
                + "and also creates a new subscription in ChargeBee.";
    }
}
