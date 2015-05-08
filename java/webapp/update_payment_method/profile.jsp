<%@page import="com.chargebee.models.Customer"%>
<%@page import="com.chargebee.samples.UpdatePaymentMethod"%>
<%@page import="com.chargebee.models.Subscription"%>
<%@page import="com.chargebee.models.Plan"%>
<%@page import="java.sql.Timestamp"%>
<%@page import="java.util.Date"%>
<%@page import="com.chargebee.Result"%>
<%@page contentType="text/html" pageEncoding="UTF-8"%>

<%@ include file="../partials/header.jspf" %>
<script type="text/javascript">
    $(document).ready(function(){
        $('.update_msg').show();
        setTimeout(function(){$('.update_msg').slideUp();},2000);
    });
                
</script>
<%  
    Result subscriptionDetail = UpdatePaymentMethod.fetchSubscriptionDetail(request);
%>

<br>
    <div id="cb-demo-ssp">        
        
        <% if (request.getParameter("updated") != null) { %>
        <div class="update_msg text-center" style="display: none">
              <p class="alert alert-success">
              <span class="glyphicon glyphicon-ok-sign"></span> Your card details have been saved successfully.  </p>
          </div> 
        <% } %>
        <h2>
            <a align="right" href="index.jsp" class="pull-right btn btn-danger"><span class="glyphicon glyphicon-off"></span> log out </a>   
            <span>Hi</span> <%= esc(subscriptionDetail.customer().firstName()) %>,               
        </h2>                    
         <h3 class="page-header">Account Information</h3>
                <div class="row form-horizontal">
		            <div class="col-sm-6">
		                <div class="row">
		                  <label class="col-xs-5 control-label">Name</label>
		                  <div class="col-xs-7">
                            <p class="form-control-static"><%= esc(subscriptionDetail.customer().firstName()) + " " + esc(subscriptionDetail.customer().lastName())%></p>
                          </div>
                        </div>
		                <div class="row">
		                  
		                  <label class="col-xs-5 control-label">Email</label>
		                  <div class="col-xs-7">
                            <p class="form-control-static"><%= esc(subscriptionDetail.customer().email()) %></p>
                          
                          </div>
                        </div>                    
		                <div class="row">
		                  <label class="col-xs-5 control-label">Organization</label>
		                  <div class="col-xs-7">
                            <p class="form-control-static"> <%= esc(subscriptionDetail.customer().company()) %></p>
                          </div>
                        </div>
		                <div class="row">
		                  <label class="col-xs-5 control-label">Phone</label>
		                  <div class="col-xs-7">
                            <p class="form-control-static"> <%= esc(subscriptionDetail.customer().phone()) %></p>
                          </div>
                       </div> 
                   </div>
             </div>                              
              <h3 class="page-header">Subscription Information</h3>                                                    
                    <% Result planDetail = Plan.retrieve(subscriptionDetail.subscription().planId()).request();
                       Plan plan = planDetail.plan(); %>
                    <div class="col-sm-6 form-horizontal">
                         <div class="form-group">
                          <label class="col-sm-5 control-label"> Plan Status</label>
                          <div class="col-sm-7">
                            <p class="form-control-static"> 
                            <span class="label-<%= esc(subscriptionDetail.subscription().status().name().toLowerCase()) %> "> <%= subscriptionDetail.subscription().status() %></span>
                            </p>
                
                          </div>
                        </div> 
                        <div class="form-group">
                          <label class="col-sm-5 control-label"> Plan Name</label>
                          <div class="col-sm-7">
                            <p class="form-control-static"><%= esc(plan.name()) %></p>
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-sm-5 control-label"> Plan Price</label>
                          <div class="col-sm-7">
                            <p class="form-control-static">$<%= esc(String.valueOf(plan.price()/100)) %></p>
                          </div>
                        </div>                                        
                    </div>               	                	
            
            <% if( subscriptionDetail.customer().paymentMethod() == null ) { %>
            <div class="col-sm-12">
                <h3>Payment Information</h3>
                <br>
                  Please 
                  <a  href="update?customer_id=
                      <%= esc(subscriptionDetail.customer().id()) %>">
                            add your payment method
                  </a>
                  before the trial ends to ensure uninterrupted service. 
             </div>
            
            <% } else {%>
                <div class="col-sm-12">                    
                    <h3>Payment Information &nbsp; 
			<a class="btn btn-primary btn-xs" href="update?customer_id=<%= esc(subscriptionDetail.customer().id()) %>">
			Update Payment Method </a>
                  </h3>
                    <div class="row form-horizontal">
                        <div class="col-sm-6">
                            <% if(subscriptionDetail.customer().paymentMethod().type().equals(Customer.PaymentMethod.Type.CARD)) {%>
                            <div class="row">
                                <label class="col-xs-5 control-label">Card Holder Name</label>
                                <div class="col-xs-7">
                                    <p class="form-control-static"><%= esc(subscriptionDetail.card().firstName())%> </p>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-xs-5 control-label">Card Type</label>
                                <div class="col-xs-7">
                                    <p class="form-control-static"><%= esc(subscriptionDetail.card().cardType().name()) %></p>
                                </div>
                            </div>
                        <div class="row">
                          
                          <label class="col-xs-5 control-label">Card No</label>
                          <div class="col-xs-7">
                            <p class="form-control-static">**** **** **** 
                                <%=esc(subscriptionDetail.card().last4()) %>
                            </p>
                          </div>
                          
                        </div>
                         <div class="row">
                          <label class="col-xs-5 control-label">Card Expiry Month</label>
                          <div class="col-xs-7">
                              <p class="form-control-static"> <%= esc(subscriptionDetail.card().expiryMonth().toString()) %></p>
                          </div>
                        </div> 
                        <div class="row">
                          <label class="col-xs-5 control-label">Card Expiry Year</label>
                          <div class="col-xs-7">
                              <p class="form-control-static"> <%= esc(subscriptionDetail.card().expiryYear().toString())    %> </p>
                          </div>
                        </div> 
                       <% } else { %>
                       <div class="row">
                          <label class="col-xs-5 control-label">Payment Method</label>
                          <div class="col-xs-7">
                              <p class="form-control-static"> 
                                  <%  if(subscriptionDetail.customer().paymentMethod().type().equals(Customer.PaymentMethod.Type.PAYPAL_EXPRESS_CHECKOUT)) {  %> 
                                     <%= esc("PayPal Express Checkout") %>
                                  <% } %>
                                  <% if(subscriptionDetail.customer().paymentMethod().type().equals(Customer.PaymentMethod.Type.AMAZON_PAYMENTS)) {%>
                                     <%= esc("Amazon Payment") %>
                                  <% } %>
                              </p>
                          </div>
                        </div>
                       <div class="row">
                          <label class="col-xs-5 control-label">Billing Agreement Id</label>
                          <div class="col-xs-7">
                              <p class="form-control-static"> 
                                     <%= esc(subscriptionDetail.customer().paymentMethod().referenceId()) %>
                              </p>
                          </div>
                        </div>       
                       <% } %>
                    </div>
                    </div> 
               </div>
             <% } %>                   
                            
</div>
<%@include file="../partials/footer.jspf" %> 
