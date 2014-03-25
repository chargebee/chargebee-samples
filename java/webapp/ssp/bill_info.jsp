<%@page import="java.util.Map"%>
<%@page import="com.chargebee.samples.SelfServicePortal"%>
<%@page import="com.chargebee.samples.common.Utils"%>
<%@page import="com.chargebee.models.Customer"%>
<%@include file="header.jspf" %>  	
<%
    Customer customer = Customer.retrieve(customerId).request().customer();
    Customer.BillingAddress billingAddress = customer.billingAddress();

%>
<script src="../assets/javascript/ssp/ssp.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $("#update-billing-info").validate();
        $("#update-billing-info").on('submit', ajaxHandler);
    })

</script>
<div id="cb-wrapper-ssp">
    <ul class="nav nav-tabs">
        <li class="active"><a href="">Billing Address</a></li>
        <li class="pull-right">
            <%@include file="logout.jspf" %>
        </li>
    </ul>      
    <div id="cb-main-content" class="clearfix">                   
        <br>
        <div class="col-sm-12 clearfix">
            <form action="update_billing_info" method="post" id="update-billing-info">
                <hr class="clearfix">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="billing_address[first_name]">First Name</label>
                            <small for="billing_address[first_name]" class="pull-right text-danger">&nbsp;</small>
                            <input type="text" class="form-control" name="billing_address[first_name]" placeholder="Enter your first name" 
                                   value="<%= Utils.esc(billingAddress != null ? billingAddress.firstName() : "") %>" required data-msg-required="cannot be blank">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="billing_address[last_name]">Last Name</label>
                            <small for="billing_address[last_name]" class="pull-right text-danger">&nbsp;</small>
                            <input type="text" class="form-control" name="billing_address[last_name]" placeholder="Enter your last name" 
                                   value="<%= Utils.esc(billingAddress != null ? billingAddress.lastName() : "") %>" required data-msg-required="cannot be blank">
                        </div>
                    </div>
                </div>
                <div class="row">                            
                    <div class="col-sm-12">
                        
                        <div class="form-group">
                            <label for="billing_address[line1]" >Address Line 1</label>
                             <small for="billing_address[line1]" class="pull-right text-danger">&nbsp;</small>
                             <input type="text" name="billing_address[line1]" class="form-control"
                                     placeholder="Enter your address line 1" 
                                     value="<%= Utils.esc(billingAddress != null ? billingAddress.line1() : "")%>"
                                     required data-msg-required="cannot be blank" />
                        </div>
                        
                    </div>                            
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="billing_address[line2]" >Address Line 2</label><small for="billing_address[line2]"class="pull-right text-danger">&nbsp;</small>
                            <input type="text" name="billing_address[line2]" class="form-control" placeholder="Enter your address line 2" 
                                   value="<%= Utils.esc(billingAddress != null ? billingAddress.line2() : "")%>"
                                   required data-msg-required="cannot be blank" />
                        </div>
                    </div>                            
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="billing_address[city]">City</label><small for="billing_address[city]" class="pull-right text-danger">&nbsp;</small>
                            <input type="text" name="billing_address[city]" class="form-control" placeholder="Enter your city" 
                                   value="<%= Utils.esc(billingAddress != null ? billingAddress.city() : "")%>"/>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="billing_address[state]" >State</label><small for="billing_address[state]"class="pull-right text-danger">&nbsp;</small>
                            <input type="text" name="billing_address[state]"class="form-control" placeholder="Enter your state" 
                                   value="<%= Utils.esc(billingAddress != null ? billingAddress.state() : "")%>"
                                   required data-msg-required="cannot be blank"/>
                        </div>
                    </div>
                </div>                    
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="billing_address[zip]" >Pincode</label><small for="billing_address[zip]" class="pull-right text-danger">&nbsp;</small>
                            <input type="text" name="billing_address[zip]" class="form-control" placeholder="Enter your first name" 
                                   value="<%= Utils.esc(billingAddress != null ? billingAddress.zip() : "")%>" 
                                   required data-msg-required="cannot be blank" />
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="billing_address[country]">Country</label>
                            <small for="billing_address[country]" class="pull-right text-danger">&nbsp;</small>
                            <select class="form-control" name="billing_address[country]" placeholder="true" 
                                    required data-msg-required="cannot be blank">
                                <% 
                                    Map<String, String> countryCodes = SelfServicePortal.getCountryCode(getServletContext().getRealPath(SelfServicePortal.countryCodeFilePath()));
                                    String billingCountry = null;
                                    if( billingAddress != null && billingAddress.country() != null) {
                                        billingCountry = Utils.esc(billingAddress.country());
                                    }
                                %>
                                <option value="" <%= (billingCountry == null ) ? "selected"  : "" %> >
                                    Select your country
                                </option>
                                <% for (Map.Entry<String, String> entry : countryCodes.entrySet()) { %>
                                    <option value="<%= entry.getKey() %>" <%= entry.getKey().equals(billingCountry) ? "selected": "" %> > 
                                        <%= entry.getValue() %>
                                    </option>
                                <% } %>  
                            </select>
                        </div>
                    </div>
                </div>
                <hr class="clearfix">                                                
                <div class="form-inline">
                    <span class="form-group"><input type="submit" value="Update Address" class="btn btn-sm btn-primary"></span>
                    <span class="form-group"><a href="/ssp/subscription.jsp" class="btn btn-sm btn-link">Cancel</a></span>
                    <span class="alert-danger"></span>
                </div>
            </form>
        </div>                                        
    </div>
</div>
<%@include file="footer.jspf" %>