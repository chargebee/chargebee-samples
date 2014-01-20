<%@page import="com.chargebee.org.json.JSONObject"%>
<%@page import="com.chargebee.APIException"%>
<%@page import="com.chargebee.samples.EstimateCheckout"%>
<%@page import="com.chargebee.models.Estimate.Discount"%>
<%@page import="com.chargebee.models.Estimate.LineItem"%>
<%@page import="java.util.List"%>
<%@page import="com.chargebee.Result"%>
<%@page import="com.chargebee.models.Estimate"%>
<%@page contentType="text/html" pageEncoding="UTF-8"%>


<% 
  String estimateResult = (String)request.getAttribute("estimate_result");
  Estimate estimate = new Estimate(new JSONObject(estimateResult));
%>
<%@include file="order_summary.jspf" %>

