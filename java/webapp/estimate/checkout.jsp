<%@page import="com.chargebee.samples.EstimateCheckout"%>
<%@page contentType="text/html" pageEncoding="UTF-8"%>
<%@include file="../partials/header.jspf" %>

<script type="text/javascript">
    jQuery.validator.setDefaults({
        errorClass: "text-danger",
        errorElement: "small"
    });

    $(document).ready(function() {

        $('.wallposters').change(function(e) {
            if ($(this).is(":checked")) {
                $('.wallposters-quantity').prop("disabled", false);
            } else {
                $('.wallposters-quantity').prop("disabled", true);
            }
            sendAjaxRequest();
        });

        $('#order_summary').on('click', '#apply-coupon', function(e) {
            if ($('#coupon').val().trim() == '') {
                $('.error_msg').text("invalid coupon code");
            } else {
                sendAjaxRequest();
            }
        })
        
        $('#order_summary').on('click', '#remove-coupon', function(e) {
           $('#coupon').removeAttr("value");
           sendAjaxRequest(); 
        })

        $('.addons').on('change', '.wallposters-quantity', function(e) {
            sendAjaxRequest();
        })

        $('.addons').on('change', '.ebook', function(e) {
            sendAjaxRequest();
        })

        function sendAjaxRequest() {
            var wallpostersQuantity, ebook, coupon;
            if ($('.wallposters').is(":checked")) {
                wallpostersQuantity = $('.wallposters-quantity').val();
            }
            if ($('.ebook').is(':checked')) {
                ebook = "true";
            }
            if ($('#coupon').val().trim() != '') {
                coupon = $('#coupon').val().trim();
            }
            parameters = {"wallposters-quantity": wallpostersQuantity,
                "ebook": ebook,
                "coupon": coupon
            }
            orderSummaryAjaxHandler(parameters)
        }

        function orderSummaryAjaxHandler(dataContent) {
            $.ajax({
                url: "order_summary",
                data: dataContent,
                beforeSend: function(data, textstatus, jqXHR) {
                    $('.text-danger').text('');
                    $('.ajax-loader').show();
                },
                success: function(data, textstatus, jqXHR) {
                    $('#order_summary').html(data);
                },
                error: function(data, textstatus, jqXHR) {
                   try{
                       var error = JSON.parse(data.responseText);
                       $('.error_msg').text(error.error_msg);
                   } catch(e) {
                       $('.error_msg').text("Internal Server Error");
                   }
                   $('.error_msg').show();
                },
                complete: function() {
                    $('.ajax-loader').hide();
                }

            });
        }

        $("#subscribe-form").validate({
            rules: {
                zip_code: {number: true},
                phone: {number: true}
            }
        });

    });
</script>
<div class="ajax-loader" style="display: none">
    <img src="/assets/images/loader.gif" class="center-img">
    <div id="lightbox"></div>
</div>
<form action="estimate_checkout" method="post"  id="subscribe-form">
    <div class="row">
        <div class="col-sm-4 col-xs-12 pull-right">                                                
            <div class="affix">
                <div id="order_summary">
                    <% Estimate estimate = EstimateCheckout.getOrderSummary(request); %>
                    <%@include file="order_summary.jspf" %>
                </div>
                <br>
                <img src="/assets/images/secure.png" alt="secure server"/>
                <br><br>
            </div>            
        </div>
        <div class="col-sm-7">           
            <div class="addons">
                <div class="page-header">
                    <h3>Want something extra for a few more bucks?</h3>
                </div>           
                <p><input id="wallposters" class="wallposters" type="checkbox" value="true"/> 
                    Send me  
                    <select class="wallposters-quantity" name="wallposters-quantity" disabled>
                        <option value="1"> 1 </option>
                        <option value="2"> 2 </option>
                        <option value="3"> 3 </option>
                        <option value="4"> 4 </option>
                        <option value="5"> 5 </option>
                    </select> wall poster(s) of the comic book's main character(s) <br> </p>
                <p>
                    <input id="ebook" type="checkbox" class="ebook" name="ebook" value="true"/>
                    Send me a ebook version of the comic.                            
                </p>
            </div>
            <div id="checkout_info">   
                <div class="page-header">
                    <h3>Tell us about yourself</h3>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" class="form-control" name="first_name" 
                                   required data-msg-required="cannot be blank">
                            <small for="first_name" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" class="form-control" name="last_name" 
                                   required data-msg-required="cannot be blank">
                            <small for="last_name" class="text-danger"></small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input id="email" type="text" class="form-control" name="email" 
                                   data-rule-required="true" data-rule-email="true" 
                                   data-msg-required="Please enter your email address" 
                                   data-msg-email="Please enter a valid email address">
                            <small for="email" class="text-danger"></small>
                        </div>
                    </div> 
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input id="phone" type="text" maxlength="10" class="form-control" name="phone" 
                                   required data-msg-required="cannot be blank">
                            <small for="phone" class="text-danger"></small>
                        </div>
                    </div>                   
                </div> 
                <div class="page-header">
                    <h3>Where would you like us to deliver</h3>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="addr">Address</label>
                            <input type="text" class="form-control" name="addr" 
                                   required data-msg-required="cannot be blank">
                            <small for="addr" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="extended_addr">Address2</label>
                            <input type="text" class="form-control" name="extended_addr">
                            <small for="extended_addr" class="text-danger"></small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" class="form-control" name="city" 
                                   required data-msg-required="cannot be blank">
                            <small for="city" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="state">State</label>
                            <input type="text" class="form-control" name="state" 
                                   required data-msg-required="cannot be blank">
                            <small for="state" class="text-danger"></small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="zip_code">Zip Code</label>
                            <input id="zip_code" type="text" class="form-control" name="zip_code" 
                                   required number data-msg-required="cannot be blank">
                            <small for="zip_code" class="text-danger"></small>
                        </div>
                    </div>                                                
                </div> 
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-header">
                            <h3>Payment Information</h3>
                        </div>
                    </div>
                </div>  
                <div class="row">                 	  
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="card_no">Credit Card Number</label>
                            <div class="row">
                                <div class="col-sm-6">
                                    <input type="text" class="card-number form-control" name="card_no" id="card_no" 
                                           required data-msg-required="cannot be blank"> 
                                </div>
                                <div class="col-sm-6">                      	
                                    <span class="cb-cards hidden-xs">                                        
                                        <span class="visa">  </span>                                        
                                        <span class="mastercard">  </span>
                                        <span class="american_express">  </span>
                                        <span class="discover">  </span>
                                    </span> 
                                </div>
                            </div>
                            <small for="card_no" class="text-danger"></small>
                        </div>
                    </div>                                                             
                </div>
                <div class="row">                
                    <div class="col-sm-6">                                	
                        <div class="form-group">
                            <label for="expiry_month">Card Expiry</label>
                            <div class="row">
                                <div class="col-xs-6">
                                    <select class="card-expiry-month form-control" name="expiry_month" id="expiry_month" 
                                            required data-msg-required="empty">
                                        <option selected>01</option>
                                        <option>02</option>
                                        <option>03</option>
                                        <option>04</option>
                                        <option>05</option>
                                        <option>06</option>
                                        <option>07</option>
                                        <option>08</option>
                                        <option>09</option>
                                        <option>10</option>
                                        <option>11</option>
                                        <option>12</option>
                                    </select>
                                </div>
                                <div class="col-xs-6">
                                    <select class="card-expiry-year form-control" name="expiry_year" id="expiry_year" 
                                            required data-msg-required="empty">
                                        <option>2013</option>
                                        <option>2014</option>
                                        <option>2015</option>
                                        <option>2016</option>
                                        <option>2017</option>
                                        <option>2018</option>
                                        <option>2019</option>
                                        <option selected="">2020</option>
                                        <option>2021</option>
                                        <option>2022</option>
                                        <option>2023</option>
                                    </select>
                                </div>
                            </div> 
                            <small for="expiry_month" class="text-danger"></small>
                        </div>                                       
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="ccv">CVC</label>
                            <div class="row">                                    	
                                <div class="col-xs-6">                                            
                                    <input type="text" class="card-cvc form-control" id="cvc" name="cvc" placeholder="CVC" 
                                           required data-msg-required="empty">
                                </div>
                                <div class="col-xs-6">                                            	
                                    <h6 class="cb-cvv"><small>(Last 3-4 digits)</small></h6>
                                </div>
                            </div>
                            <small for="cvc" class="text-danger"></small>
                        </div>
                    </div>                                      
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <hr>                            
                        <p>By clicking Subscribe, you agree to our privacy policy and terms of service.</p>
                        <p><small class="text-danger" style="display:none;">There were errors while submitting</small></p>
                        <p><input type="submit" class="btn btn-success btn-lg pull-left" value="Subscribe">&nbsp;&nbsp;&nbsp;&nbsp;
                            <span class="subscribe_process process" style="display:none;">Processing&hellip;</span>
                            <small class="alert-danger text-danger"></small>
                        </p><br><br>                          
                    </div>
                </div>
            </div>   
        </div>
    </div>
</form>
<%@include file="../partials/footer.jspf" %>

