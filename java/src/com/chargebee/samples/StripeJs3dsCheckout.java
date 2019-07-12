package com.chargebee.samples;

import com.chargebee.APIException;
import com.chargebee.Environment;
import com.chargebee.Result;
import com.chargebee.exceptions.InvalidRequestException;
import com.chargebee.exceptions.PaymentException;
import com.chargebee.models.Address;
import com.chargebee.models.Customer;
import com.chargebee.models.Subscription;
import java.io.IOException;
import java.io.PrintWriter;
import java.net.*;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import com.stripe.Stripe;
import com.stripe.exception.StripeException;
import com.stripe.model.Charge;
import com.stripe.net.RequestOptions;
import com.stripe.model.PaymentIntent;
import java.util.*;
import com.chargebee.org.json.JSONObject;

import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;
import org.apache.commons.io.IOUtils;

import static com.chargebee.samples.common.Utils.*;
import static com.chargebee.samples.common.ErrorHandler.*;

/**
 * Demo on how to create subscription with ChargeBee API using stripe temporary token and
 * adding shipping address to the subscription for shipping of product.
 */
public class StripeJs3dsCheckout extends HttpServlet {

    /**
     * Processes HTTP request
     * <code>POST</code> method, creates the subscription and also adds address
     * to the subscription
     *
     * @param request servlet request
     * @param response servlet response
     * @throws ServletException if a servlet-specific error occurs
     * @throws IOException if an I/O error occurs
     */
    protected void processRequest(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        /*
         * Setting the Content-Type as application/json.
         */
        response.setHeader("Content-Type", "application/json;charset=utf-8");
        PrintWriter out = response.getWriter();
        
        validateParameters(request);
        try {
//            Result result = createSubscription(request);
//
//            addShippingAddress(request, result.subscription().id(), result.customer());

            /* Forwarding to thank you page after successful create subscription.
             */
            //Writing json. Suggestion: Use proper json library
            out.write("{\"forward\": \"/stripe_js_3ds/thankyou.html\"}");
            
        } catch(PaymentException e) {
            handleTempTokenErrors(e, response, out);
        } catch(InvalidRequestException e){
            handleInvalidRequestErrors(e, response, out, "plan_id");
        } catch(Exception e) {
            handleGeneralErrors(e, response, out);
        } finally {
            out.close();
        }
    }

    protected void confirmPaymemt(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        /*
         * Setting the Content-Type as application/json.
         */
        JSONObject respJson = new JSONObject();
        response.setHeader("Content-Type", "application/json;charset=utf-8");
        PrintWriter out = response.getWriter();

        validateParameters(request);
        try {
            respJson = createPaymentIntent(request);
            out.write(respJson.toString());
        } catch(PaymentException e) {
            handleTempTokenErrors(e, response, out);
        } catch(InvalidRequestException e){
            handleInvalidRequestErrors(e, response, out, "plan_id");
        } catch(Exception e) {
            handleGeneralErrors(e, response, out);
        } finally {
            out.close();
        }
    }
    
    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        try {
            String path = request.getServletPath();
            if (path.endsWith("/checkout")) {
                processRequest(request, response);
            } else if (path.endsWith("/confirm")) {
                confirmPaymemt(request, response);
            }
            else {
                response.sendError(HttpServletResponse.SC_NOT_FOUND);
            }
        } catch (Exception e) {
            throw new RuntimeException(e);//Will be handled in error servlet.
        }
    }

    @Override
    public String getServletInfo() {
        return "Demo on how to create subscription with the ChargeBee API using stripe temporary token";
    }

    /**
     * Adds the shipping address to an existing subscription. The first name
     * & the last name for the shipping address is get from the customer 
     * account information in ChargeBee.
     *
     * @param req the HttpServletRequest from the client
     * @param subscriptionId Identifier of the subscription to attach the shipping address to.
     * @param customer Customer
     * @throws IOException
     */
    public void addShippingAddress(HttpServletRequest req, String subscriptionId, Customer customer) 
            throws Exception {
        try {
            /*
             * Adding address to the subscription for shipping product to the customer.
             * Sends request to the ChargeBee server and adds the shipping address
             * for the given subscription Id.
             */
            Result result = Address.update()
                    .label("shipping_address")
                    .subscriptionId(subscriptionId)
                    .firstName(customer.firstName())
                    .lastName(customer.lastName())
                    .addr(req.getParameter("addr"))
                    .extendedAddr(req.getParameter("extended_addr"))
                    .city(req.getParameter("city"))
                    .state(req.getParameter("state"))
                    .zip(req.getParameter("zip_code")).request();
        }
        catch (Exception e) {
            throw e;
        }
    }

    /**
     * Creates the subscription in ChargeBee using the checkout details and
     * stripe temporary token provided by stripe.
     *
     * @param req HttpServletRequest
     * @return Result
     * @throws IOException
     */
    public Result createSubscription(HttpServletRequest req)
            throws Exception {
        try {
            /*
             * planId is id of the plan present in the ChargeBee app.
             * For demo purpose a plan with id 'annual' is hard coded and should exist
             * at your ChargeBee site. It can be also be received from request by
             * making customer selecting the plan of their choice at client side.
             */
            String planId = "annual";

            /* Sends request to the ChargeBee server to create the subscription from
             * the parameters received. The result will have subscription attributes,
             * customer attributes and card attributes.
             */
            Result result = Subscription.create()
                    .planId(planId)
                    .customerEmail(req.getParameter("customer[email]"))
                    .customerFirstName(req.getParameter("customer[first_name]"))
                    .customerLastName(req.getParameter("customer[last_name]"))
                    .customerPhone(req.getParameter("customer[phone]"))
                    .cardTmpToken(req.getParameter("stripeToken")).request();

            return result;
        }
        catch (Exception e) {
            throw e;
        }
    }

    /**
     * Creates the payment intent in Stripe using the payment method id and
     * confirms the payment intent.
     *
     * @param req HttpServletRequest
     * @return Result
     * @throws Exception
     */
    public JSONObject createPaymentIntent(HttpServletRequest req)
            throws Exception {
        try {
            Stripe.apiKey = "< stripe_api_key >";
            String jsonBody = IOUtils.toString(req.getReader());
            JSONObject jsonObject = new JSONObject(jsonBody);
            PaymentIntent intent;
            if (jsonObject.has("payment_method_id")) {
                Map<String, Object> paymentIntentParams = new HashMap<>();
                paymentIntentParams.put("amount", 100);
                paymentIntentParams.put("currency", "usd");
                paymentIntentParams.put("capture_method", "manual");
                paymentIntentParams.put("confirmation_method", "manual");
                paymentIntentParams.put("confirm", true);
                paymentIntentParams.put("payment_method", jsonObject.get("payment_method_id"));

                intent = PaymentIntent.create(paymentIntentParams);
            }
            else if (jsonObject.has("payment_intent_id")) {
                intent = PaymentIntent.retrieve(jsonObject.get("payment_intent_id").toString());
                Map<String, Object> params = new HashMap<String, Object>();
                intent = intent.confirm(params);
            }
            else {
                return null;
            }
            return generatePaymentResponse(intent);
        }
        catch (Exception e) {
            throw e;
        }
    }

    protected JSONObject generatePaymentResponse(PaymentIntent intent) throws Exception {
        try {
            JSONObject respJson = new JSONObject();
            if (("requires_source_action".equals(intent.getStatus()) || "requires_action".equals(intent.getStatus())) &&
                    "use_stripe_sdk".equals(intent.getNextAction().getType())) {

                respJson.put("requires_action", true);
                respJson.put("payment_intent_client_secret", intent.getClientSecret());
            } else if ("requires_capture".equals(intent.getStatus())) {
                respJson.put("success", true);
                respJson.put("payment_intent_id", intent.getId());
            } else {
                respJson.put("success", false);
                respJson.put("error", intent.getStatus());
            }
            return respJson;
        }
        catch (Exception e) {
            throw e;
        }
    }
}
