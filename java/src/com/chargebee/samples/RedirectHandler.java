package com.chargebee.samples;

import com.chargebee.APIException;
import com.chargebee.Environment;
import com.chargebee.Result;
import com.chargebee.models.HostedPage;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.Enumeration;
import java.util.Iterator;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

/**
 * This Servlet file is configured as redirect url for the hosted page in
 * ChargeBee app. Hosted page Id and state of the hosted page will be sent along
 * the request
 *
 */
public class RedirectHandler extends HttpServlet {

    /**
     * Processes request HTTP
     * <code>GET</code> and handles the redirection from ChargeBee server.
     *
     * @param request servlet request
     * @param response servlet response
     * @throws ServletException if a servlet-specific error occurs
     * @throws IOException if an I/O error occurs
     */
    protected void processRequest(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        response.setContentType("text/html;charset=UTF-8");
        PrintWriter out = response.getWriter();
        try {
            
            /* Sets the environment for calling the Chargebee API.
             * You need to sign up at ChargeBee app to get this credential.
             */
            Environment.configure("<your-site>","<your-api-key>");
            
            
            
            /* The request will have hosted page id and state of the checkout   
             * which helps in getting the details of subscription created using 
             * ChargeBee checkout hosted page.
             */
            if ("succeeded".equals(request.getParameter("state"))) {
                /* Request the ChargeBee server about the Hosted page state and give the details
                 * about the subscription created.
                 */
                String hostedPageId = request.getParameter("id");
                Result hostedPageContent = HostedPage.retrieve(hostedPageId).request();
                String queryParameters = "name=" + hostedPageContent.hostedPage().content().customer().firstName()
                        + "&planId=" + hostedPageContent.hostedPage().content().subscription().planId();
                response.sendRedirect("thankyou.html?" + queryParameters);
            } else {
                /* If the state is not success then error page is shown to the customer.
                 */
                response.setStatus(400);
                response.sendRedirect("error.html");
            }
            
        } finally {
            out.close();
        }
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
        return "This Servlet handles the redirection from ChargeBee server";
    }
}
