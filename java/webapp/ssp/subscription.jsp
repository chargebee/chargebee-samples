<%@page import="com.chargebee.models.enums.SubscriptionStatus"%>
<%@page import="java.util.Date"%>
<%@page import="com.chargebee.Result"%>
<%@page import="com.chargebee.models.Subscription"%>
<%@page import="com.chargebee.samples.common.Utils"%>


<%@include file="header.jspf" %>
<script>
            $(document).ready(function() {

    function cardUpdateResponseHandler(response) {
                   var hostedPageId = response.hosted_page_id;
                   ChargeBee.bootStrapModal(response.url, "honeycomics-test", "myModal").load({
                        hostSuffix: ".chargebee.com", //only for local testing
                        protocol: "https",
                    onLoad: function(width, height) {
                       //
                    },
                    
                    /* This will be triggered after subscribe button is clicked 
                     * and checkout is completed in the iframe checkout page
                     */
                    onSuccess: function() {
                        window.location.reload();
                    },
                    
                    /* This will be triggered after cancel button is clicked in 
                     * the iframe checkout page.
                     */
                    onCancel: function() {
                        $(".alert-danger").show().text("Payment Aborted !!");
                        $('.submit-btn').removeAttr("disabled");
                    }
                  });
               }

               $("#update_card").on("click", function(e) {
                    $.ajax({
                        url: "update_card",
                        success: cardUpdateResponseHandler,
                        dataType: 'json'
                    });
                  return false;
               });
            });

    </script>

            <% 
                Result result = Subscription.retrieve(subscriptionId)
                            .request();
            %>

            <div id="cb-wrapper-ssp">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="subscription.jsp">Subscription</a></li>
                    <li><a href="invoice_list.jsp">Invoices</a></li>
                    <li class="pull-right">
                        <%@include file="logout.jspf" %>
                    </li>
                </ul>   
                <div id="cb-main-content" class="clearfix">

                    <div id="acc-info">
                        <h3>Customer Information<a href="acc_info_edit.jsp" class="pull-right h6">
                           <span class="glyphicon glyphicon-pencil">
                                </span> Edit</a></h3>
                                <%@include file="acc_info_show.jspf"%>
                    </div>

                    <div id="card-info">
                        <h3>Payment Details <a id="update_card" href="javascript:void(0)" class="pull-right h6">
                                <span class="glyphicon glyphicon-pencil"></span>
                                <% if (result.card() == null) {%>
                                Add </a></h3>
                        <div class="text-center">
                            <p class="alert alert-info">
                                <span class="glyphicon glyphicon-info-sign"></span>
                                Please add your card details
                            </p>
                        </div>
                        <% } else {%>
                        Update </a></h3>
                        <%@include file="card_info_show.jspf"%>
                        <% }%>
                    </div>

                    <div id="payment-info">
                        <h3>Billing Address <a href="bill_info.jsp"  class="pull-right h6">
                                <span class="glyphicon glyphicon-pencil"></span> 
                                <% if (result.customer().billingAddress() == null) {%>
                                     Add </a></h3>
                                     <div class="text-center">
                                        <p class="alert alert-info">
                                            <span class="glyphicon glyphicon-info-sign"></span>
                                            Please add your billing address
                                        </p> 
                                    </div>
                                <% } else {%>
                                    Edit </a></h3>
                                    <%@include file="billing_address_show.jspf" %>
                                <% }%>
                     </div>

                    <div id="subscription-info">
                        <h3> Subscription Details 
                            <span class="<%= Utils.subscriptionStatus().get(result.subscription().status()) %> label">
                           <span class="hidden-xs"><%= result.subscription().status()%></span></span>
                            <!--a href="#" target="_blank" class="pull-right h6"><span class="glyphicon glyphicon-pencil"></span> Edit</a-->
                        </h3>
                            <%@include file="subscription_info_show.jspf"%> 
                    </div>

                    <div id="shipping-address">
                        <%@include file="shipping_address_show.jspf" %>                
                    </div>

                    <h3>Account Summary</h3>                
                    <div class="clearfix col-sm-12">
                        <% Subscription.Status subscriptionStatus = result.subscription().status();%>
                            <p><span class="text-muted">Subscription created at </span> 
                            <%= Utils.getHumanReadableDate(result.subscription().createdAt())%></p>

                        <% if (subscriptionStatus.equals(Subscription.Status.IN_TRIAL)) {%>
                            <p><span class="text-muted">Trial period is between </span> 
                                <%= Utils.getHumanReadableDate(result.subscription().trialStart()) %> 
                                <span class="text-muted"> and</span>
                                <%= Utils.getHumanReadableDate(result.subscription().trialEnd()) %> 
                            </p>
                            <p> <span class="text-muted">The subscription will be changed to active state from </span>
                            <%= Utils.getHumanReadableDate(result.subscription().trialEnd()) %> 
                            </p>
                        <% } else if (subscriptionStatus.equals(Subscription.Status.CANCELLED)) {%>
                            <p><span class="text-muted">Your subscription has been canceled on </span> 
                            <%= Utils.getHumanReadableDate(result.subscription().cancelledAt())%> </p>
                        <% } else if (subscriptionStatus.equals(Subscription.Status.ACTIVE)) {%>    
                            <p><span class="text-muted">The current term of your subscription is between </span>
                            <%= Utils.getHumanReadableDate(result.subscription().currentTermStart()) %>
                            <span class="text-muted"> and</span> <%= Utils.getHumanReadableDate(result.subscription().currentTermEnd())%>
                            </p>
                            <p><span class="text-muted">Your next billing date is</span> 
                                <%= Utils.getHumanReadableDate(result.subscription().currentTermEnd())%> </p>
                        <% } else if ( subscriptionStatus.equals(Subscription.Status.NON_RENEWING) ) {%>
                             <p><span class="text-muted"> The subscription will be canceled on next renewal </span>
                             <%= Utils.getHumanReadableDate(result.subscription().currentTermEnd())%> 
                            </p>
                        <% } else if ( subscriptionStatus.equals(Subscription.Status.FUTURE) ) {%>
                            <p><span class="text-muted"> The subscription will start at </span>
                             <%= Utils.getHumanReadableDate(result.subscription().startDate())%> 
                            </p>
                        <% } %>
                    </div>         
                    <hr class="clearfix"> 
                    <div class="text-right">
                     <% if( subscriptionStatus.equals(Subscription.Status.CANCELLED) )  { %>
                            <a href="/ssp/subscription_reactivate.jsp" class="text-primary">Re-activate</a>       
                     <% } else if( subscriptionStatus.equals(Subscription.Status.IN_TRIAL)) { %>
                            <a href="subscription_cancel.jsp?next_renewal=false" class="text-danger">Cancel</a>
                     <% } else if( subscriptionStatus.equals(Subscription.Status.NON_RENEWING) ) { %>
                            <a href="/ssp/subscription_reactivate.jsp" class="text-primary">Re-activate</a> 
                            / <a href="subscription_cancel.jsp?next_renewal=false" class="text-danger">Cancel</a>
                    <% } else if (subscriptionStatus.equals(Subscription.Status.FUTURE) ){ %>
                              <a href="subscription_cancel.jsp?next_renewal=false" class="text-danger">Cancel</a>
                    <% } else {%>
                            <a href="subscription_cancel.jsp" class="text-danger">Cancel</a>
                    <% } %> this Subscription.
                    </div> 
                </div>
            </div>
           <!-- Modal -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content" style="max-width: 540px;">
                    <div class="modal-header">
                        <h4 class="modal-title text-center">
                            Payment Information
                        </h4>
                    </div>
                    <!--add custom attribute data-cb-modal-body="body" to modal body -->
                    <div class="modal-body"  data-cb-modal-body="body" style="padding-left: 0px;padding-right: 0px;">
                    </div>
                </div> 
            </div>
        </div> 
                    
<%@include file="footer.jspf" %>
