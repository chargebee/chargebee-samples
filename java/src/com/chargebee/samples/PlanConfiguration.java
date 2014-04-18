/*
 * Copyright (c) 2014 ChargeBee Inc
 * All Rights Reserved.
 */
package com.chargebee.samples;

import com.chargebee.APIException;
import com.chargebee.Result;
import com.chargebee.models.Addon;
import com.chargebee.models.Plan;
import com.chargebee.models.Subscription;
import com.chargebee.org.json.JSONObject;
import com.chargebee.samples.common.Utils;
import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.servlet.RequestDispatcher;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

/*
 * This servlet configures/adds the plan or subscription needed for each demo.
 */
public class PlanConfiguration extends HttpServlet {
    
    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        
        String demoName = request.getParameter("demo_name");
        
        try {
            String returnParameters = "";
            if ("trial_signup".equals(demoName)  ) {
                createPlan("Basic", "basic", 1000, 15);
                returnParameters = "demo_name=Trial Signup&plan=Basic";
            } else if( "checkout_new".equals(demoName) ) {
                createPlan("Basic", "basic", 1000, 15);
                returnParameters = "demo_name=Checkout New&plan=Basic";
            } else if ("checkout_two_step".equals(demoName) ){
                createPlan("Basic", "basic", 1000, 15);
                returnParameters = "demo_name=Two-step Checkout&plan=Basic";
            } else if( "checkout_existing".equals(demoName)){
                createSubscription("Kim","Burner","kim@acme.com");
                returnParameters = "demo_name=Checkout Existing&plan=Basic&customer=Kim Burner";
            } else if ("update_card".equals(demoName)) {
                createSubscription("John","Wayne","john@acmeinc.com");
                returnParameters = "demo_name=Update Card&plan=Basic&customer=John Wayne";
            } else if("custom_field".equals(demoName)) {
                createPlan("Basic", "basic", 1000, 15);
                returnParameters = "msg=This tutorial requires custom fields to be created for your ChargeBee site. "
                        + "Submit your custom field request from your site settings."
                        + "This demo requires a <b>\"DOB\"</b> and <b>\"Comics Type\"</b> custom fields but you can request for any other fields too.";
            } else if("stripe_js".equals(demoName)) {
                createPlan("Annual", "annual", 2000, null);
                returnParameters = "demo_name=Stripe Js&plan=Basic";
            } else if ("estimate".equals(demoName)) {
                createAddon("Wall Posters","wall-posters", 300, Addon.Type.QUANTITY);
                createAddon("E Book","e-book", 200, Addon.Type.ON_OFF);
                createPlan("Monthly","monthly",600,null);
                returnParameters = "demo_name=Estimate api&plan=Monthly&addon=E-book&addon=Wall Posters";
            } else if("usage_based_billing".equals(demoName)) {
                returnParameters = "msg=To generate a <b>\"Pending\" </b> invoice, you need to enable <b>\"Notify and wait to close invoice\"</b> in your "
                               + "site settings. Once enabled, try to generate an invoice for a subscription by changing the subscription's plan.";
            } else if("ssp".equals(demoName)) {
                createSubscription("John", "Doe", "john@acmeinc.com");
                returnParameters="demo_name=Self service portal&plan=Basic&customer=John Doe";
            } else if("stripe-popup-js".equals(demoName)) {
                createPlan("Basic", "basic", 1000, 15);
                returnParameters = "demo_name=Stripe checkout popup&plan=Basic";
            } else if("braintree-js".equals(demoName)) {
                createPlan("Professional", "professional", 20, 10);
                returnParameters = "demo_name=Braintree js Checkout&plan=Professional";
            } else if("checkout_iframe".equals(demoName)) {
                    createPlan("Basic", "basic", 1000, 15);
                    returnParameters = "demo_name=Checkout using iFrame&plan=Basic";
            } else {
                response.sendError(HttpServletResponse.SC_BAD_REQUEST);
                return;
            }
            response.sendRedirect("index.html?"+ Utils.encodeParam(returnParameters));
        } catch (APIException e ) {
            System.out.println(e.toString());
            response.sendError(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
        } catch (Exception e ) {
            e.printStackTrace();
            response.sendError(HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
        }
    }
    
    public Result createSubscription(String firstName,String lastName, String email) throws Exception{
        
        Plan plan = createPlan("Basic", "basic", 1000, 15);
        
        System.out.println("Creating Subscription with id "+ email);
        Result result = null;
        try{
            result = Subscription.create().planId(plan.id()).id(email)
                        .customerFirstName(firstName).customerLastName(lastName)
                        .customerEmail(email).request();
            return result;
        } catch(APIException e) {
             if(e.param.equals("id") && e.code.equals("param_not_unique")) {
                 result = Subscription.retrieve(email).request();
                 return result;
             } else {
                throw e;
             }
        }
    }
    
    public Plan createPlan(String name, String id, Integer price,
            Integer trialPeriod) throws Exception {
        
        System.out.println("Creating plan with id "+ id );
        Plan.CreateRequest planCreateParam = Plan.create().name(name).id(id)
                                             .invoiceName(name).price(price);
        
        if(trialPeriod != null) {
            planCreateParam.trialPeriod(trialPeriod)
                    .trialPeriodUnit(Plan.TrialPeriodUnit.DAY);
        }
        
        Result result = null;
        try{    
            result = planCreateParam.request();
            return result.plan();
        } catch(APIException e) {
            if(e.param.equals("id") && e.code.equals("param_not_unique")) {
                result = Plan.retrieve(id).request();
                return result.plan();
            } else {
                throw e;
            } 
        }
         
    }
    
    public Addon createAddon(String name, String id,Integer price,
             Addon.Type type ) throws Exception {
        
        System.out.println("Creating addon with id " + id);
        Addon.CreateRequest addonCreateParam = Addon.create().name(name).id(id)
                                                .invoiceName(name)
                                                .chargeType(Addon.ChargeType.RECURRING)
                                                .price(price).period(1)
                                                .periodUnit(Addon.PeriodUnit.MONTH)
                                                .type(type);
        
        if(type.equals(Addon.Type.QUANTITY)) {
            addonCreateParam.unit("nos");
        }
        
        Result result = null;
        try{
           result = addonCreateParam.request();
           return result.addon();
        } catch(APIException e) {
            if((e.param.equals("name") || e.param.equals("id")) 
                    && e.code.equals("param_not_unique")) {
                result = Addon.retrieve(id).request();
                return result.addon();
            } else {
                throw e;
            } 
        }
                
    }
    
    
    @Override
    public String getServletInfo() {
        return "This servlet configures the plan or subscription needed for each demo";
    }
}
