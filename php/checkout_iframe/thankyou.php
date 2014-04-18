<?php
  require_once('../partials/header.php');
  require_once(dirname(__FILE__) . '/../php_src/Util.php');
?>
<?php 
$result = ChargeBee_Subscription::retrieve($_GET['subscription_id']);
?>
<h1 class="text-center" style="font-size: 50px; margin: 40px;"> You have been successfully subscribed. </h1>
<div class="col-sm-8 col-sm-offset-2 cb-spacing-bottom">

             <?php if ($result->subscription()->status == "in_trial") { ?>
                  <span class="text-muted lead"> Your current subscription trial will end on  </span>
                  <span class="lead">
                  <?php echo date('m/d/y', $result->subscription()->trialEnd) ?>
                  </span>
             <?php } else if ($result->subscription()->status = "active") { ?>
                  <span class="text-muted lead"> Your subscription will be renewed on </span>
                  <span class="lead">
                       <?php echo date('m/d/y', $result->subscription()->currentTermEnd) ?>
                  </span>
                   <br>
                  <?php if( $result->subscription->remainingBillingCycles != null ) { ?>
			<span class="text-muted"> You subscription will be last for </span>
			<?php echo $result->subscription()->remainingBillingCycles ?> billing cycle(s).
                  <?php } ?>
             <?php } ?>
</div>
<?php
   require_once('../partials/footer.php');
?>

