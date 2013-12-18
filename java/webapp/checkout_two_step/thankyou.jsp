<%@page import="com.chargebee.samples.CheckoutTwoStep"%>
<%@page import="com.chargebee.Result"%>
<%@page import="com.chargebee.models.Address"%>
<%@page import="com.chargebee.Environment"%>
<%@page contentType="text/html" pageEncoding="UTF-8"%>
<%@ include file="../partials/header.jspf" %>
        <h1 class="text-center" style="font-size: 50px; margin: 40px;"> Thank you for subscribing!</h1>
        <div class="col-sm-8 col-sm-offset-2">
        <h4> Your comics will be shipped to the following address each month:</h4>
        
        <% 
            Address address = CheckoutTwoStep.retrieveAddress(request);
        %>
        <div class="form-horizontal">
            <div class="form-group">
                <label class="text-muted control-label col-sm-3">Address</label>
                <span class="col-sm-9 form-control-static">
                    <%= esc(address.addr()) + 
                         esc((address.extendedAddr() == null ? "," : ", " +address.extendedAddr() +","))
                     %></span>
            </div>
        
            <div class="form-group">
                <label class="text-muted control-label col-sm-3">City</label>
                <span class="col-sm-9 form-control-static"> <%= esc(address.city())+"," %> </span>
            </div>
            <div class="form-group">
                <label class="text-muted control-label col-sm-3">State </label>
                <span class="col-sm-9 form-control-static"> <%= esc(address.state())+"," %> </span>
            </div>
            <div class="form-group">
                <label class="text-muted control-label col-sm-3"> Zip Code </label>
                <span class="col-sm-9 form-control-static"> <%= esc(address.zip())+"." %> </span>
            </div>
        
        </div>
        </div>        
<%@include file="../partials/footer.jspf" %>
