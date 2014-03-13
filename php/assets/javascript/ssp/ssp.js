jQuery.validator.setDefaults({
    errorClass: "text-danger",
    errorElement: "small"
});

var ajaxHandler = function(e) {
    e.preventDefault();
    var formElement = $(this);
    if( !formElement.valid()) {
        return false;
    }
    $.ajax({
        type: "POST",
        url: formElement.attr("action"),
        data: formElement.serializeArray(),
        beforeSend: function() {
            $('.ajax-loader').show();
            $('.alert-danger').hide();
        },
        success: function(response) {
            window.location.replace(response.forward);
        },
        
        error: function(jqXHR, textStatus, errorThrown) {
            try {
                var resp = JSON.parse(jqXHR.responseText);
                if ('error_param' in resp) {
                    var errorMap = {};
                    var errParam = resp.error_param;
                    var errMsg = resp.error_msg;
                    errorMap[errParam] = errMsg;
                    formElement.validate().showErrors(errorMap);
                } else {
                    var errMsg = resp.error_msg;
                    $(".alert-danger").show().text(errMsg);
                }
            } catch (err) {
                $(".alert-danger").show().text("Error while processing your request");
            }
        },
        
        complete: function() {
            $('.ajax-loader').hide();
        }

    })
}
