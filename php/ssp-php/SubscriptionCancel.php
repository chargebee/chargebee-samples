<?php
 require_once('./header.php');
?>


<div id="cb-wrapper-ssp">
    <ul class="nav nav-tabs">
        <li class="active"><a href="">Cancel Subscription</a></li>
        <li class="pull-right">
            <?php include("./Logout.php") ?>
        </li>
    </ul>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.radio-group').on('click', '.radio', function() {
                $('input[type=radio]').each(function() {
                    var alertClass = $(this).attr("id");
                    if ($(this).is(':checked')) {
                        $('.' + alertClass).show();
                    } else {
                        $('.' + alertClass).hide();
                    }
                })
            })

        })
    </script>
    <div id="cb-main-content" class="clearfix"> 
        <br>
        <div class="col-sm-12 clearfix">	
            <form action="/ssp-php/sub_cancel" method="post">
               <p>We're sorry to lose you as a customer.</p>
               <p>When do you want to cancel your subscription?</p>            	
                <div class="radio-group col-sm-12">
                    <div class="radio">
                        <label class="text-muted">
                            <input type="radio" id="cancel-immediately" name="cancel_status" value="cancel_immediately" checked>
                            Cancel Immediately	
                        </label>
                    </div>
                    <?php if ( !($_GET['next_renewal'] == "false") ) { ?>
                    <div class="radio">
                        <label>
                            <input type="radio" id="cancel-on-next-renewal" name="cancel_status" value="cancel_on_next_renewal">
                            <span class="text-muted">Cancel on next renewal </span>
                        </label>
                    </div>  
                    <?php } ?>
                </div>
                <div class="alert alert-warning">
                    <span class="glyphicon glyphicon-exclamation-sign"></span>
                    <span class="cancel-immediately">
                        Your subscription will be canceled right away!
                    </span>
                    <span class="cancel-on-next-renewal" style="display: none">
                        Your subscription will be canceled at the end of your current billing period.
                    </span>
                </div> 
                <div class="form-inline">
                    <input type="hidden" name="subscription_id" value="<?php echo $subscriptionId ?>" />
                    <span class="form-group"><input type="submit" value="Cancel my Subscription" class="btn btn-danger btn-sm"></span>
                    <span class="form-inline">
                        <a href="/ssp-php/subscription" class="btn btn-link btn-sm">Go back</a>
                    </span>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
 require_once('./footer.php');
?>

