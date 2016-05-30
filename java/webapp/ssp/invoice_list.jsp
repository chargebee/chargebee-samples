<%@page import="com.chargebee.samples.common.Utils"%>
<%@page import="com.chargebee.ListResult"%>
<%@page import="java.util.List"%>
<%@page import="com.chargebee.models.Invoice"%>
<%@include file="header.jspf" %>

<%

    ListResult result = Invoice.invoicesForSubscription(subscriptionId)
            .limit(20).request();
%>


<div id="cb-wrapper-ssp">
    <ul class="nav nav-tabs">
        <li><a href="subscription.jsp">Subscription</a></li>
        <li class="active"><a href="invoice_list.jsp">Invoices</a></li>
        <li class="pull-right">
            <%@include file="logout.jspf" %>
        </li>
    </ul>  
    <div id="cb-main-content" class="clearfix">
        <% if (result.size() == 0) {%>
            <div class="text-center h4 cb-empty-space">
                No invoice found
            </div>
        <% } else {%>
        <h3 class="unstyle">Your Invoices</h3>       
        <table class="table table-hover" id="cb-payment-table">
            <tbody>
                <tr>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Number</th>
                    <th>Amount</th>
                    <th>Payment</th>
                    <th>Download</th>
                </tr>
                
                <%
                    int i = 0;
                    for (ListResult.Entry entry : result) {
                        Invoice invoice = entry.invoice();
                        if (invoice.status().equals(Invoice.Status.PENDING)) {
                            continue;
                        }
                        i++;
                %>
                
                <tr>
                    <td>
                        <% if (invoice.status().equals(Invoice.Status.PAYMENT_DUE)) {%>
                            <span class="glyphicon glyphicon-exclamation-sign text-warning"></span>
                        <% } else if (invoice.status().equals(Invoice.Status.PAID)) {%>
                            <span class="glyphicon glyphicon-ok text-success"></span>
                        <% } else if (invoice.status().equals(Invoice.Status.NOT_PAID)) {%>
                            <span class="glyphicon glyphicon-remove text-danger"></span>
                        <% }%>
                    </td>
                    
                    <td>
                        <%= Utils.getHumanReadableDate(invoice.date())%>
                    </td>
                    <td> 
                        <%=invoice.id()%>
                    </td>
                    <td >
                        $ <%= String.format("%d.%02d", invoice.total()/ 100, invoice.total() % 100)%>
                    </td>
                    
                    <td class="text-muted"> 
                        <% if (invoice.status().equals(Invoice.Status.PAID)) {%>
                            Paid on : <%=  Utils.getHumanReadableDate(invoice.paidAt())%> 
                        <% } else if (invoice.status().equals(Invoice.Status.PAYMENT_DUE)) {%>
                            Next Retry at :
                            <%= (invoice.paidAt() == null) ? "" : Utils.getHumanReadableDate(invoice.nextRetryAt())%>
                        <% } else if (invoice.status().equals(Invoice.Status.NOT_PAID)) {%>
                            Not Paid 
                        <% }%>
                    </td>
                    <td style="vertical-align:middle;text-align:center;">
                        <a href="invoice_as_pdf?invoice_id=<%=invoice.id()%>" class="download">
                            <span class="glyphicon glyphicon-cloud-download text-info"></span>
                        </a>
                    </td>
                </tr> 
                <% }%>
            </tbody>
        </table>
        
        <p class="pull-left">
            <span class="glyphicon glyphicon-ok text-success"></span>
            <span class="text-muted"> Paid</span>&nbsp;&nbsp;
            <span class="glyphicon glyphicon-exclamation-sign text-warning"></span>
            <span class="text-muted"> Payment Due</span>&nbsp;&nbsp;
            <span class="glyphicon glyphicon-remove text-danger"></span>
            <span class="text-muted"> Not Paid</span>            
        </p>
        <p class="pull-right">
            <span class="text-muted">Showing last </span><%= i%> <span class="text-muted">invoice(s)</span>
        </p>
        <% }%>
    </div>
</div>
<%@include file="footer.jspf" %>
