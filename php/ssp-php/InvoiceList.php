<?php
 require_once('./header.php');
?>

<?php

    $listResult = ChargeBee_Invoice::invoicesForSubscription($subscriptionId,
                                                         array("limit" => 20));
?>


<div id="cb-wrapper-ssp">
    <ul class="nav nav-tabs">
        <li><a href="subscription">Subscription</a></li>
        <li class="active"><a href="invoice_list">Invoices</a></li>
        <li class="pull-right">
            <?php include("./Logout.php") ?>
        </li>
    </ul>  
    <div id="cb-main-content" class="clearfix">
        <?php  if( $listResult->count() == 0 ) { ?>
            <div class="text-center h4 cb-empty-space">
                No invoice found
            </div>
        <?php } else { ?>
        <h3 class="unstyle">Your Invoices</h3>       
        <table class="table table-hover" id="cb-payment-table">
            <tbody>
                <tr>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Number</th>
                    <th>Amount</th>
                    <th>Payment</th>
                    <th>Download</th>
                </tr>
                
                <?php
                    $i = 0;
                    foreach($listResult as $entry ) {
                        $invoice = $entry->invoice();
                        if ( $invoice->status == "pending" ) {
                            continue;
                        }
                        $i++;
                ?>
                
                <tr>
                    <td>
                        <?php if ( $invoice->status == "payment_due" ) { ?>
                            <span class="glyphicon glyphicon-exclamation-sign text-warning"></span>
                        <?php } else if ($invoice->status == "paid" ) { ?>
                            <span class="glyphicon glyphicon-ok text-success"></span>
                        <?php } else if ($invoice->status == "not_paid") { ?>
                            <span class="glyphicon glyphicon-remove text-danger"></span>
                        <?php } ?>
                    </td>
                    
                    <td>
                        <?php echo date('m/d/y', $invoice->date) ?>
                    </td>
                    <td> 
                        <?php echo $invoice->id ?>
                    </td>
                    <td >
                        $ <?php echo number_format($invoice->total / 100, 2, '.', '')  ?>
                    </td>
                    
                    <td class="text-muted"> 
                        <?php if ($invoice->status == "paid" ) { ?>
                            Paid on : <?php echo date('m/d/y', $invoice->paidAt) ?> 
                        <?php } else if ($invoice->status == "payment_due") { ?>
                            Next Retry at :
                            <?php echo ($invoice->nextRetry == null) ? "" : date('m/d/y', $invoice->nextRetry) ?>
                        <?php } else if ($invoice->status == "not_paid" ) { ?>
                            Not Paid 
                        <?php } ?>
                    </td>
                    <td style="vertical-align:middle;text-align:center;">
                        <a href="invoice_as_pdf?invoice_id=<?php echo $invoice->id ?>" class="download">
                            <span class="glyphicon glyphicon-cloud-download text-info"></span>
                        </a>
                    </td>
                </tr> 
                <?php } ?>
            </tbody>
        </table>
        
        <p class="pull-left">
            <span class="glyphicon glyphicon-ok text-success"></span>
            <span class="text-muted"> Paid</span>&nbsp;&nbsp;
            <span class="glyphicon glyphicon-exclamation-sign text-warning"></span>
            <span class="text-muted"> Payment Due</span>&nbsp;&nbsp;
            <span class="glyphicon glyphicon-remove text-danger"></span>
            <span class="text-muted"> Not Paid</span>            
        </p>
        <p class="pull-right">
            <span class="text-muted">Showing last </span><?php echo $i ?> <span class="text-muted">invoice(s)</span>
        </p>
        <?php } ?>
    </div>
</div>
<?php
 require_once('./footer.php');
?>

