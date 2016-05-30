<?php
   $estimatParam = array("subscription" => array("id" => $subscriptionId));

   $invoiceEstimate = null; 
   if( $result->subscription()->status != "cancelled" && $result->subscription()->status != "non_renewing" ) {
      $estimate  = ChargeBee_Estimate::renewalEstimate($subscriptionId, array("useExistingBalances" => "true"))->estimate();
      $invoiceEstimate = $estimate->invoiceEstimate; 
   } 
?>
<div class="clearfix col-sm-12">            	                                 
        <?php if($invoiceEstimate == null) { ?>
            <div class="text-right">
               Next Invoice Amount &nbsp;&nbsp;
             <span class="h2">
                $ 0.00 
               </span>
           </div> 
        <?php } else { ?>
           <ul id="cb-order-summary" class="list-unstyled">
        <?php foreach ($invoiceEstimate->lineItems as $li) {?>
        <li>
            <div class="row">
                <div class="col-xs-8">
                    <span class="cb-list-prefix"><?php echo $li->entityType ?>:</span>
                    <strong> <?php echo esc($li->description) . " ( x " . esc($li->quantity) . ")" ?></strong>
                </div>
                <div class="col-xs-4 text-right">$ 
                    <?php  echo number_format($li->amount / 100, 2, '.', '') ?>
                </div>
            </div>
        </li>
        <?php } 
        if( isset($invoiceEstimate->taxes)) {
            foreach ($invoiceEstimate->taxes as $t) { ?>
            <li class="row">
                <div class="col-xs-8">
                    <span class="cb-list-prefix">Tax:</span> 
                    <strong> <?php echo esc($t->description) ?> </strong>
                </div>
                <div class="col-xs-4 text-right"> 
                    $ <?php echo number_format($t->amount / 100, 2, '.', '') ?> 
                </div>
            </li>
        <?php } 
        }
        if(isset($invoiceEstimate->discounts)) {
            foreach ($invoiceEstimate->discounts as $dis) { ?>
                <li class="row">
                    <div class="col-xs-8">
                        <span class="cb-list-prefix">Discount:</span> 
                        <strong> <?php esc($dis->description) ?> </strong>
                    </div>
                    <div class="col-xs-4 text-right"> 
                        - $ <?php echo number_format($dis->amount / 100, 2, '.', '') ?> 
                    </div>
                </li>
        <?php } 
        }
        ?>
    </ul>
    <div class="text-right">
        Next Invoice Amount &nbsp;&nbsp;
        <span class="h2"> 
            $ <?php echo number_format( $invoiceEstimate->total / 100, 2, '.', '') ?>
        </span>
    </div>
    <?php } ?>
</div>
