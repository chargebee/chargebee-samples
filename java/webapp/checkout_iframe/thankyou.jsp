<%@page import="com.chargebee.samples.common.Utils"%>
<%@page import="com.chargebee.models.Subscription"%>
<%@page import="com.chargebee.samples.CheckoutTwoStep"%>
<%@page import="com.chargebee.Result"%>
<%@page import="com.chargebee.models.Address"%>
<%@page import="com.chargebee.Environment"%>
<%@page contentType="text/html" pageEncoding="UTF-8"%>
<%@ include file="../partials/header.jspf" %>
        <h1 class="text-center" style="font-size: 50px; margin: 40px;"> 
            You have been successfully subscribed.
        </h1>
        <div class="col-sm-8 col-sm-offset-2 cb-spacing-bottom">
            <% Result result =  Subscription.retrieve(request.getParameter("subscription_id"))
                                                    .request(); %>
            
                <% if( result.subscription().status().equals(Subscription.Status.IN_TRIAL)) { %>
                        <span class="text-muted lead">Your subscription trial will end on </span>
                        <span class="lead">  
                            <%= Utils.getHumanReadableDate(result.subscription().trialEnd()) %> 
                        </span>
                <% } else if( result.subscription().status().equals(Subscription.Status.ACTIVE))  { %>
                    <span class="text-muted lead"> Your subscription will be renewed on  </span>
                    <span class="lead"> 
                        <%= Utils.getHumanReadableDate(result.subscription().currentTermEnd()) %> 
                    </span>
                        <br>
                        <% if(result.subscription().remainingBillingCycles() != null) { %>
                                <span class="text-muted lead"> You subscription will be last for </span>
                                <%= result.subscription().remainingBillingCycles() %> billing cycle(s). 
                         <% } %>
                <% } %>
        </div>        
<%@include file="../partials/footer.jspf" %>
