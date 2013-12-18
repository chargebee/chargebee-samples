<%@page import="java.text.SimpleDateFormat"%>
<%@page import="java.util.Calendar"%>
<%@page import="java.util.Date"%>
<%@page import="java.sql.Timestamp"%>
<%@page import="com.chargebee.Result"%>
<%@page import="com.chargebee.models.Subscription"%>
<%@ include file="../partials/header.jspf" %>


<% 
   String subscriptionId = request.getParameter("subscription_id"); 
   Result result = Subscription.retrieve(subscriptionId).request();
   Long dobAsLong = result.customer().optLong("cf_date_of_birth");  // retrieving the custom fields from response
   String comicsType = result.customer().optString("cf_comics_type"); // retrieving the custom fields from response
   Date date = new Date(dobAsLong *1000);
   
   SimpleDateFormat dateFormat = new SimpleDateFormat("dd-MMM");
%>

<div class="jumbotron text-center">
    	<h2><span class="text-muted">Congrats! You've successfully</span> signed up <span class="text-muted">to Honey Comics.</span></h2>
        <h4 class="text-muted"><%= comicsType %> comics will be delivered to your email address.</h4>
        <h3> Expect a surprise on your birthday (<%= dateFormat.format(date) %>) :) </h3>
        <h1>Thank You!</h1>
</div>

<%@include file="../partials/footer.jspf" %>
