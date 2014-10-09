package com.chargebee.samples;

import com.chargebee.APIException;
import com.chargebee.Result;
import com.chargebee.exceptions.InvalidRequestException;
import com.chargebee.exceptions.PaymentException;
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

import static com.chargebee.samples.common.ErrorHandler.*;
import static com.chargebee.samples.common.Utils.validateParameters;

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
        JSONObject respJson = new JSONObject();
        PrintWriter out = response.getWriter();
        
        validateParameters(request);
        try {
            
            /*
             * Forming create subscription request parameters to ChargeBee.
             */
            Subscription.CreateRequest createSubcriptionRequest = Subscription.create()
                                            .planId("monthly")
                                            .customerFirstName(request.getParameter("customer[first_name]"))
                                            .customerLastName(request.getParameter("customer[last_name]"))
                                            .customerEmail(request.getParameter("customer[email]"))
                                            .customerPhone(request.getParameter("customer[phone]"))
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
                createSubcriptionRequest.addonId(0, "wall-posters")
                          .addonQuantity(0, quantity);
            }
                        
            /*
             * Adding addon2 to the create subscription request, if it is set by user.
             */  
            if(request.getParameter("ebook")!=null &&
                    "true".equals(request.getParameter("ebook"))) {
                createSubcriptionRequest.addonId(1, "e-book");
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
            respJson.put("forward", "thankyou.html");
            out.write(respJson.toString());
                        
            
            
        } catch(PaymentException e){
            handlePaymentErrors(e, response, out);
        } catch(InvalidRequestException e){
            /*
             * Checking whether the error is due to coupon param. If the error is due to
             * coupon param then reason for error can be identified through "api_error_code" attribute.
             */
            if("coupon".equals(e.param)){
                handleCouponErrors(e, response, out);
            } else {
                handleInvalidRequestErrors(e, response, out, "plan_id", 
                        "addons[id][0]", "addons[id][1]");
            }
        }catch(Exception e) {    
            handleGeneralErrors(e, response, out);
        } 
    }

    /*
     * Add Shipping address using the subscription id returned from 
     * create subscription response.
     */
    private void addShippingAddress(String subscripitonId, HttpServletRequest request) throws IOException {
        Result result = Address.update().label("shipping_address")
                            .subscriptionId(subscripitonId)
                            .firstName(request.getParameter("customer[first_name]"))
                            .lastName(request.getParameter("customer[last_name]"))
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

        } catch(InvalidRequestException e) {
             
            /*
             * Checking whether the error is due to coupon param. If the error is due to
             * coupon param then reason for error can be identified through "api_error_code" attribute.
             */
            if("subscription[coupon]".equals(e.param)){
                handleCouponErrors(e, response, out);
            } else {
                handleInvalidRequestErrors(e, response, out, "subscription[plan_id]",
                        "addons[id][0]", "addons[id][1]");
            }
        } catch( Exception e) {
            handleGeneralErrors(e, response, out);
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
                estimateReq.addonId(0, "wall-posters")
                        .addonQuantity(0, quantity);
            }

            /*
             * Adding addon2 to the create subscription estimate request, if it is set by user.
             */
            if (request.getParameter("ebook") != null
                    && "true".equals(request.getParameter("ebook"))) {
                estimateReq.addonId(1, "e-book");
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
