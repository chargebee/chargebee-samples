<?php
 require_once('./header.php');
?>

<?php
 $result = ChargeBee_Subscription::retrieve($subscriptionId);
?>

            <div id="cb-wrapper-ssp">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="subscription">Subscription</a></li>
                    <li><a href="invoice_list">Invoices</a></li>
                    <li class="pull-right">
                        <?php include("./Logout.php") ?>
                    </li>
                </ul>   
                <div id="cb-main-content" class="clearfix">

                    <div id="acc-info">
                        <h3>Customer Information<a href="acc_info_edit" class="pull-right h6">
                           <span class="glyphicon glyphicon-pencil">
                           </span> Edit </a> </h3>
                                <?php include("AccountInfoShow.php") ?>
                    </div>

                    <div id="card-info">
                        <h3>Payment Details <a href="update_card" class="pull-right h6">
                        <span class="glyphicon glyphicon-pencil"></span>
                        <?php if ($result->customer()->paymentMethod == null) { ?>
                                Add </a></h3>
				<div class="text-center">
				<p class="alert alert-info">
				    <span class="glyphicon glyphicon-info-sign"></span>
				    Please add your payment details
				</p>
				</div>
                        <?php } else {?>
                             Update </a></h3>
			    <?php include("CardInfoShow.php") ?>
                        <?php } ?>
                    </div>

                    <div id="payment-info">
                        <h3>Billing Address <a href="bill_info"  class="pull-right h6">
                                <span class="glyphicon glyphicon-pencil"></span>
                                <?php if ( !isset($result->customer()->billingAddress) ) { ?>
                                     Add </a></h3>
                                     <div class="text-center">
                                        <p class="alert alert-info">
                                            <span class="glyphicon glyphicon-info-sign"></span>
                                            Please add your billing address
                                        </p> 
                                    </div>
                                <?php } else { ?>
                                    Edit </a></h3>
                                    <?php include("./BillingAddressShow.php")?>
                                <?php } ?>
                     </div>

                    <div id="subscription-info">
			<?php $subStatusCss = subscriptionStatus(); ?>
                        <h3> Subscription Details 
                           <span class="<?php echo $subStatusCss[$result->subscription()->status] ?> label">
                                <span class="hidden-xs"><?php echo $result->subscription()->status ?>
                                </span>
                           </span>
                            <!--a href="#" target="_blank" class="pull-right h6"><span class="glyphicon glyphicon-pencil"></span> Edit</a-->
                        </h3>
                            <?php include("SubscriptionInfoShow.php") ?> 
                    </div>

                    <div id="shipping-address">
                        <?php include("ShippingAddressShow.php")?>                
                    </div>

                    <h3>Account Summary</h3>                
                    <div class="clearfix col-sm-12">
                        <?php $subscriptionStatus = $result->subscription()->status ?>
                            <p><span class="text-muted">Subscription created at </span> 
                            <?php echo date('m/d/y', $result->subscription()->createdAt) ?></p>

                        <?php if ($subscriptionStatus == "in_trial" ) { ?>
                            <p><span class="text-muted">Trial period is between </span> 
                                <?php echo date('m/d/y', $result->subscription()->trialStart) ?> 
                                <span class="text-muted"> and</span>
                                <?php echo date('m/d/y', $result->subscription()->trialEnd ) ?> 
                            </p>
                            <p> <span class="text-muted">The subscription will be changed to active state from </span>
                            <?php  echo date('m/d/y', $result->subscription()->trialEnd) ?> 
                            </p>
                        <?php } else if ( $subscriptionStatus == "cancelled" ) { ?>
                            <p><span class="text-muted">Your subscription has been canceled on </span> 
                            <?php echo date('m/d/y', $result->subscription()->cancelledAt) ?> </p>
                        <?php } else if ( $subscriptionStatus == "active") { ?>    
                            <p><span class="text-muted">The current term of your subscription is between </span>
                            <?php echo date('m/d/y', $result->subscription()->currentTermStart) ?>
                            <span class="text-muted"> and</span> 
                                 <?php echo date('m/d/y', $result->subscription()->currentTermEnd) ?>
                            </p>
                            <p><span class="text-muted">Your next billing date is</span> 
                                <?php echo date('m/d/y', $result->subscription()->currentTermEnd) ?> </p>
                        <?php } else if ( $subscriptionStatus == "non_renewing") { ?>
                             <p><span class="text-muted"> The subscription will be canceled on next renewal </span>
                             <?php echo date('m/d/y', $result->subscription()->currentTermEnd) ?> 
                            </p>
                        <?php } else if ( $subscriptionStatus == "future")  { ?>
                            <p><span class="text-muted"> The subscription will start at </span>
                             <?php echo date('m/d/y', $result->subscription()->startDate) ?> 
                            </p>
                        <?php } ?>
                    </div>         
                    <hr class="clearfix"> 
                    <div class="text-right">
                     <?php if( $subscriptionStatus == "cancelled" )  { ?>
                            <a href="/ssp-php/subscription_reactivate" class="text-primary">Re-activate</a>       
                    <?php } else if( $subscriptionStatus == "non_renewing" ) { ?>
                            <a href="/ssp-php/subscription_reactivate" class="text-primary">Re-activate</a> 
                            / <a href="subscription_cancel?next_renewal=false" class="text-danger">Cancel</a>
                    <?php } else if ($subscriptionStatus == "in_trial") { ?>
                            <a href="subscription_cancel?next_renewal=false" class="text-danger">Cancel</a>  
                    <?php } else if( $subscriptionStatus == "future") { ?>
                            <a href="subscription_cancel?next_renewal=false" class="text-danger">Cancel</a>
                    <?php } else { ?>
                            <a href="subscription_cancel" class="text-danger">Cancel</a>
                    <?php } ?> this Subscription.
                    </div> 
                </div>
            </div>
<?php
 require_once('./footer.php');
?>


