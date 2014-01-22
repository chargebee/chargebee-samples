   jQuery.validator.setDefaults({
        errorClass: "text-danger",
        errorElement: "small"
    });

    function subscribeErrorHandler(jqXHR, textStatus, errorThrown) {
        try {
            var resp = JSON.parse(jqXHR.responseText);
            if ('error_param' in resp) {
                var errorMap = {};
                var errParam = resp.error_param;
                var errMsg = resp.error_msg;
                errorMap[errParam] = errMsg;
                $("#subscribe-form").validate().showErrors(errorMap);
            } else {
                var errMsg = resp.error_msg;
                $(".alert-danger").show().text(errMsg);
            }
        } catch (err) {
            $(".alert-danger").show().text("Error while processing your request");
        }
    }

    function subscribeResponseHandler(responseJSON) {
        window.location.replace(responseJSON.forward);
    }

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
                $('.error_msg').show();
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
                    try {
                        var error = JSON.parse(data.responseText);
                        $('.error_msg').text(error.error_msg);
                    } catch (e) {
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


        var validatePaymentDetails = function(form) {
            var errorMap = {};
            if (!Stripe.card.validateCardNumber($('#card_no').val())) {
                errorMap[$('#card_no').attr('name')] = 'invalid card number';
            }
            if (!Stripe.card.validateExpiry($('#expiry_month').val(), $('#expiry_year').val())) {
                errorMap[$('#expiry_month').attr('name')] = 'invalid expiry date';
            }
            if (!Stripe.card.validateCVC($('#cvc').val())) {
                errorMap[$('#cvc').attr('name')] = 'invalid cvc number';
            }
            if(jQuery.isEmptyObject(errorMap)){
                return true;
            }else{
                $(form).validate().showErrors(errorMap);
                return false;
            }
        };

        $("#subscribe-form").on('submit', function(e) {
            // form validation
            if (!$(this).valid() || !validatePaymentDetails(this)) {
                return false;
            }
            $(".alert-danger").hide();
            
            // Disable the submit button to prevent repeated clicks and form submit
            $('.submit-button').attr("disabled", "disabled");
            var options = {
                beforeSend: function(){ $('.ajax-loader').show(); },
                error: subscribeErrorHandler, // post-submit callback when error returns
                success: subscribeResponseHandler, // post-submit callback when success returns
                complete: function() {
                    $('.ajax-loader').hide();
                },
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                dataType: 'json'
            };
            // Doing AJAX form submit to your server.
            $(this).ajaxSubmit(options);
            return false;
        });

    });
