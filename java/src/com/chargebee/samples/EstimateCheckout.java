package com.chargebee.samples;

import com.chargebee.APIException;
import com.chargebee.Result;
import com.chargebee.internal.Request;
import com.chargebee.models.Address;
import com.chargebee.models.Estimate;
import com.chargebee.models.Subscription;
import com.chargebee.org.json.JSONObject;
import com.chargebee.samples.common.*;
import java.io.IOException;
import java.io.PrintWriter;
import javax.servlet.RequestDispatcher;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

/*
 * Demo on how to use ChargeBee Estimate API during Checkout.
 */
public class EstimateCheckout extends HttpServlet {

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        try {
            /*
             * Creates the subscription at ChargeBee.
             */
            String path = request.getServletPath();
            if (path.endsWith("/estimate_checkout")) {
                createSubscription(request, response);
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
            /*
             * Calculate the order summary by calling ChargeBee estimate API.
             */
            String path = request.getServletPath();
            if (path.endsWith("/order_summary")) {
                estimateOrderSummary(request, response);
            } else {
                response.sendError(HttpServletResponse.SC_NOT_FOUND);
            }
        } catch (Exception e) {
            throw new RuntimeException(e);//Will be handled in error servlet.
        }
    }

    /*
     * Creates the subscription at ChargeBee with addons and coupon also,
     * if passed along with the request.
     */
    protected void createSubscription(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        try {
            
            /*
             * Forming create subscription request parameters to ChargeBee.
             */
            Subscription.CreateRequest createSubcriptionRequest = Subscription.create()
                                            .planId("monthly")
                                            .customerFirstName(request.getParameter("first_name"))
                                            .customerLastName(request.getParameter("last_name"))
                                            .customerEmail(request.getParameter("email"))
                                            .customerPhone(request.getParameter("phone"))
                                            .cardNumber(request.getParameter("card_no"))
                                            .cardExpiryMonth(Integer.parseInt(request.getParameter("expiry_month")))
                                            .cardExpiryYear(Integer.parseInt(request.getParameter("expiry_year")))
                                            .cardCvv(request.getParameter("cvc"));
            
            
                        
            /*
             * Adding addon1 to the create subscription request, if it is set by user.
             */
            if(request.getParameter("wallposters-quantity") != null && 
                    !"".equals(request.getParameter("wallposters-quantity"))) {
                Integer quantity = Integer.parseInt(request.getParameter("wallposters-quantity"));
                createSubcriptionRequest.addonId(1, "wall-posters")
                          .addonQuantity(1,quantity);
            }
                        
            /*
             * Adding addon2 to the create subscription request, if it is set by user.
             */  
            if(request.getParameter("ebook")!=null &&
                    "true".equals(request.getParameter("ebook"))) {
                createSubcriptionRequest.addonId(2, "e-book");
            }
            
            /*
             * Adding coupon to the create subscription request, if it is set by user.
             */
            if(request.getParameter("coupon") != null &&
                    !"".equals(request.getParameter("coupon"))) {
                createSubcriptionRequest.coupon(request.getParameter("coupon"));
            }
            
                        
            /*
             * Sending request to the ChargeBee.
             */
            Result result = createSubcriptionRequest.request();
            /*
             * Adds shipping address to the subscription using the subscription Id 
             * returned during create subscription response.
             */
            addShippingAddress(result.subscription().id(), request);
            /*
             * Forwarding to thank you page.
             */
            response.sendRedirect("thankyou.html");
                        
            
            
        } catch(APIException e) {
            /*
             * ChargeBee Exception are caught here.
             */
           System.out.println(e.toString());
           response.sendError(e.httpCode);
        }catch(Exception e) {    
            /*
             * Other than ChargeBee Exception are caught and handled here.
             */
           e.printStackTrace();
           response.sendError(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
        } 
    }

    /*
     * Add Shipping address using the subscription id returned from 
     * create subscription response.
     */
    private void addShippingAddress(String subscripitonId, HttpServletRequest request) throws IOException {
        Result result = Address.update().label("shipping_address")
                            .subscriptionId(subscripitonId)
                            .firstName(request.getParameter("first_name"))
                            .lastName(request.getParameter("last_name"))
                            .addr(request.getParameter("addr"))
                            .extendedAddr(request.getParameter("extended_addr"))
                            .city(request.getParameter("city"))
                            .state(request.getParameter("state"))
                            .zip(request.getParameter("zip_code"))
                            .request();
    }

    /*
     * This method uses ChargeBee Estimate API to estimate the total amount during checkout.
     * When the user adds addon or coupon, this method is called via AJAX and the 
     * total order summary is estimated and resposne is sent back to the browser.
     * 
     */
    private void estimateOrderSummary(HttpServletRequest request, HttpServletResponse response) 
            throws IOException {
        PrintWriter out = response.getWriter();
        try {
                        
            Estimate estimate = getOrderSummary(request);
            
            RequestDispatcher forwarder = request.getRequestDispatcher("order_summary.jsp");
            request.setAttribute("estimate_result", estimate.toString());

            forwarder.forward(request, response);

        } catch(APIException apiException) {
            

            
            /*
             * ChargeBee Exception are caught here.
             */
            try{
                JSONObject errorJSON = new JSONObject();
                String msg = "";
                /*
                 * Checking whether the error is due to coupon. If the error is
                 * due to coupon then http code returned by ChargeBee is sent back.
                 * Other errors( i.e addon error ) are treated as Internal Server Error
                 * and status code HttpServletResponse.SC_INTERNAL_SERVER_ERROR is returned.
                 */
                if( apiException.param.equals("subscription[coupon]") 
                        && apiException.code.equals("referenced_resource_not_found")) {
                    msg = "Oops ! Looks like you have entered a wrong coupon code.";
                    response.setStatus(apiException.httpCode);
                } else if( apiException.code.equals("coupon_expired")) {
                    msg = "Sorry. The coupon code that you entered has expired.";
                    response.setStatus(apiException.httpCode);
                } else if( apiException.code.equals("max_redemptions_reached")) {
                    msg = "Oops ! Looks like your coupon code has been exhausted";
                    response.setStatus(apiException.httpCode);
                } else {
                    apiException.printStackTrace();
                    msg = "Sorry, There was some problem processing the request. We will get back to you shortly.";
                    response.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
                }
                errorJSON.put("error_msg",msg);
                out.write(errorJSON.toString());                
            } catch( Exception e) {
                /*
                 * If something goes wrong when sending the error, it is also 
                 * treated as Internal Server Error.
                 */
                response.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
            } 
            
            
        } catch(Exception e) {
            /*
             * Other errors are caught here and handled as Internal Server Error.
             */
            e.printStackTrace();
            response.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
        } finally {
            out.flush();
        }
    }

    
    /*
     * Returns estimate object by applying the addons and coupons set by user.
     */
    public static Estimate getOrderSummary(HttpServletRequest request) throws IOException {
            /* 
             * Forming create subscription estimate parameters to ChargeBee.
             */
            Estimate.CreateSubscriptionRequest estimateReq = Estimate.createSubscription()
                    .subscriptionPlanId("monthly");

            /*
             * Adding addon1 to the create subscription estimate request, if it is set by user.
             */
            if (request.getParameter("wallposters-quantity") != null
                    && !"".equals(request.getParameter("wallposters-quantity"))) {
                Integer quantity = Integer.parseInt(request.getParameter("wallposters-quantity"));
                estimateReq.addonId(1, "wall-posters")
                        .addonQuantity(1, quantity);
            }

            /*
             * Adding addon2 to the create subscription estimate request, if it is set by user.
             */
            if (request.getParameter("ebook") != null
                    && "true".equals(request.getParameter("ebook"))) {
                estimateReq.addonId(2, "e-book");
            }

            /*
             * Adding coupon to the create subscription estimate request, if it is set by user.
             */
            if (request.getParameter("coupon") != null
                    && !"".equals(request.getParameter("coupon"))) {
                estimateReq.subscriptionCoupon(request.getParameter("coupon"));
            }

            /*
             * Sending request to the ChargeBee.
             */
            Result result = estimateReq.request();
            
           return result.estimate();
    }
    
    
    
    
    

    /**
     * Returns a short description of the servlet.
     *
     * @return a String containing servlet description
     */
    @Override
    public String getServletInfo() {
        return "Demo on how to use ChargeBee Estimate API during Checkout";
    }
}
