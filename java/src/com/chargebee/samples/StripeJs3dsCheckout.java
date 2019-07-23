package com.chargebee.samples;

import com.chargebee.APIException;
import com.chargebee.Environment;
import com.chargebee.Result;
import com.chargebee.exceptions.InvalidRequestException;
import com.chargebee.exceptions.PaymentException;
import com.chargebee.models.Address;
import com.chargebee.models.Customer;
import com.chargebee.models.Estimate;
import com.chargebee.models.Subscription;
import java.io.IOException;
import java.io.PrintWriter;
import java.net.*;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import com.google.gson.JsonObject;
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
            String jsonBody = IOUtils.toString(request.getReader());
            JSONObject jsonObject = new JSONObject(jsonBody);
            Result result = createSubscription(jsonObject);

            addShippingAddress(jsonObject, result.subscription().id(), result.customer());

            
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
            } else if (path.endsWith("/confirm_payment")) {
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
    public void addShippingAddress(JSONObject jsonObject, String subscriptionId, Customer customer)
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
                    .addr(jsonObject.get("addr").toString())
                    .extendedAddr(jsonObject.get("extended_addr").toString())
                    .city(jsonObject.get("city").toString())
                    .state(jsonObject.get("state").toString())
                    .zip(jsonObject.get("zip_code").toString()).request();
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
    
    public Result createSubscription(JSONObject jsonObject)
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
            JSONObject customerObject = (JSONObject) jsonObject.get("customer");
            Result result = Subscription.create()
                    .planId(planId)
                    .customerEmail(customerObject.get("email").toString())
                    .customerFirstName(customerObject.get("first_name").toString())
                    .customerLastName(customerObject.get("last_name").toString())
                    .customerPhone(customerObject.get("phone").toString())
                    .paymentIntentGatewayAccountId("gw_HmgkoB8RWZ3usv68")
                    .paymentIntentGwToken(jsonObject.get("payment_intent_id").toString()).request();

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
            
            Stripe.apiKey = "<your-stripe-api-key>";
            
            String jsonBody = IOUtils.toString(req.getReader());
            JSONObject jsonObject = new JSONObject(jsonBody);
            PaymentIntent intent;
            if (jsonObject.has("payment_method_id")) {
                Estimate estimate =  getSubscriptionEstimate(jsonObject);
                
                Map<String, Object> paymentIntentParams = new HashMap<>();
                paymentIntentParams.put("amount", estimate.invoiceEstimate().total());
                paymentIntentParams.put("currency", estimate.invoiceEstimate().currencyCode());
                paymentIntentParams.put("capture_method", "manual");
                paymentIntentParams.put("confirmation_method", "manual");
                paymentIntentParams.put("setup_future_usage", "off_session");
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
    

    protected Estimate getSubscriptionEstimate(JSONObject jsonObject) throws Exception {
        try {
            
            Result result = Estimate.createSubscription()
                    .subscriptionPlanId("annual")
                    .billingAddressLine1(jsonObject.get("addr").toString())
                    .billingAddressLine2(jsonObject.get("extended_addr").toString())
                    .billingAddressCity(jsonObject.get("city").toString())
                    .billingAddressZip(jsonObject.get("zip_code").toString())
                    .billingAddressCountry("US")
                    .request();
            
            return result.estimate();
        }
        catch (Exception e) {
            throw e;
        }
    }


}
