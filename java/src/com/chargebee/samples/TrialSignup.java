package com.chargebee.samples;

import com.chargebee.APIException;
import com.chargebee.Environment;
import com.chargebee.Result;
import com.chargebee.exceptions.InvalidRequestException;
import com.chargebee.models.Subscription;
import com.chargebee.models.enums.AutoCollection;
import java.io.IOException;
import java.io.PrintWriter;
import java.net.URLEncoder;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import static com.chargebee.samples.common.ErrorHandler.*;
import static com.chargebee.samples.common.Utils.validateParameters;
/**
 *
 * Demo on how to create trial sign up with basic plan and without the card details.
 */
public class TrialSignup extends HttpServlet {

    /**
     * Processes request for both HTTP
     * <code>POST</code> methods and creates a trial subscription at 
     * ChargeBee
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
        try { 
            
            /* Forwarding to success page after trial subscription created successfully in ChargeBee.
             */
            Result responseResult = createTrialSignUp(request);
            
            String queryParameters = "name=" + URLEncoder.encode(responseResult.customer().firstName(), "UTF-8") +
                                     "&planId=" + URLEncoder.encode(responseResult.subscription().planId(),"UTF-8");
            out.write("{\"forward\": \"thankyou.html?"+ queryParameters + "\"}");
            
        } catch(InvalidRequestException e){
            handleInvalidRequestErrors(e, response, out, "plan_id");
        } catch (Exception e) {
            handleGeneralErrors(e, response, out);
        } finally {
            out.close();
        }
    }
        
    /* 
     * Creates the trial subscription at ChargeBee using the request parameters with 
     * trial plan 'basic' in ChargeBee app.
     */
    private Result createTrialSignUp(HttpServletRequest req) throws IOException {
        
        /*
         * Constructing the request parameters and sending request to ChargeBee server 
         * to create a trial subscription. 
         * For demo purpose plan with id 'basic' with trial period 30 days at 
         * ChargeBee app is hard coded here.
         */
        String planId = "basic";
        Result result = Subscription.create().customerFirstName(req.getParameter("customer[first_name]"))
                              .customerLastName(req.getParameter("customer[last_name]"))
                              .planId(planId)
                              .customerEmail(req.getParameter("customer[email]"))
                              .customerPhone(req.getParameter("customer[phone]"))
                              .request();
        
        return result;
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
        return "Short demo on how to create trial subscription with trial plan";
    }

}
