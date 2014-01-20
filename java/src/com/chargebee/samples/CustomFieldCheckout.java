package com.chargebee.samples;

import com.chargebee.APIException;
import com.chargebee.Environment;
import com.chargebee.Result;
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
        
        String day = request.getParameter("dob_day");
        String month = request.getParameter("dob_month");
        String year = request.getParameter("dob_year");
        String dateString = day + "-" + month + "-" + year;
        DateFormat formatter = new SimpleDateFormat("dd-MM-yyyy");
        try{
            /*
             * Parsing the Date String and coverting it to Date.
             */
            Date dob = formatter.parse(dateString); 
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
                                    .param("customer[cf_comics_type]",
                                            request.getParameter("comics_type")) // custom field attributes
                                    .param("customer[cf_date_of_birth]", 
                                            String.valueOf(dob.getTime()/1000) ) // custom field attributes       
                                     .request();
            
            /*
             * Forwarding to thank you page after subscription created successfully.
             */
            
            String queryParameters = "subscription_id=" + 
                                     URLEncoder.encode(responseResult.subscription().id(),
                                                        "UTF-8");
            out.write("{\"forward\": \"thankyou.jsp?"+ queryParameters + "\"}");
            
        } catch(APIException e) { 
            /* ChargeBee exception is captured through APIException and 
             * the error messsage (as JSON) is sent to the client.
             */
            out.write(e.toString());
            response.setStatus(e.httpCode);
        }catch(Exception e) {
            /* Other errors are captured here and error messsage (as JSON) 
             * sent to the client.
             * Note: Here the subscription might have been created in ChargeBee 
             *       before the exception has occured.
             */
            response.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
            out.write("{\"error_msg\": \"Error while creating your subscription.\"}");
        } finally{
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
