<?php
   $estimatParam = array("subscription" => array("id" => $subscriptionId));
   $estimate  = ChargeBee_Estimate::updateSubscription($estimatParam)->estimate();
    
?>
<div class="clearfix col-sm-12">            	                                 
    <ul id="cb-order-summary" class="list-unstyled">
        <?php foreach ($estimate->lineItems as $li) {?>
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
        if( isset($estimate->taxes)) {
            foreach ($estimate->taxes as $t) { ?>
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
        if(isset($estimate->discounts)) {
            foreach ($estimate->discounts as $dis) { ?>
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
            $ <?php echo number_format( $estimate->amount / 100, 2, '.', '') ?>
        </span>
    </div>
</div>
