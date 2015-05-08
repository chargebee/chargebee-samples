package com.chargebee.samples;

import com.chargebee.APIException;
import com.chargebee.Environment;
import com.chargebee.Result;
import com.chargebee.models.HostedPage;
import com.chargebee.models.HostedPage.Content;
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
            
            /*
             * The redirect URL will have hosted page id and state of the checkout
             * added to it. Using the hosted page id customer, subscription and 
             * other information provided in the checkout could be retrieved.
             */
            if ("succeeded".equals(request.getParameter("state"))) {
                /* 
                 * Retrieving the hosted page and getting the details
                 * of the subscription created through hosted page.
                 */
                String hostedPageId = request.getParameter("id");
                Result result = HostedPage.retrieve(hostedPageId).request();
                HostedPage hostedPage = result.hostedPage();
                if(!hostedPage.state().equals(HostedPage.State.SUCCEEDED)) {
                    response.sendError(HttpServletResponse.SC_BAD_REQUEST);
                    return;
                }
                /*
                 * Forwarding the user to thank you page.
                */
                Content content = hostedPage.content(); 
                String queryParameters = "name=" + content.customer().firstName()
                        + "&planId=" + content.subscription().planId();
                response.sendRedirect("thankyou.html?" + queryParameters);
            } else {
                /* 
                 * If the state is not success then displaying 
                 * error page to the customer.
                 */
                response.sendError(HttpServletResponse.SC_BAD_REQUEST);
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
        return "This Servlet handles the redirection after checkout from ChargeBee";
    }
}
