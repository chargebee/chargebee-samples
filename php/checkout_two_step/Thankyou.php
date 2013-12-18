<?php
  require_once('../partials/header.php');
?>
<?php 

$result = ChargeBee_Address::retrieve(array("subscription_id" =>$_GET["subscription_id"],
                                            "label" => "Shipping Address"));
$address = $result->address();

?>
<h1 class="text-center" style="font-size: 50px; margin: 40px;"> Thank You </h1>
<div class="col-sm-8 col-sm-offset-2">
<h4> Your comics will be shipped to the following address each month:</h4>

<div class="form-horizontal">
            <div class="form-group">
                <label class="text-muted control-label col-sm-3">Address</label>
                <span class="col-sm-9 form-control-static">
		<?php echo $address->addr .", " . (($address->extendedAddr == null)? "" : $address->extendedAddr .",") ?></span>
            </div>
            <div class="form-group">
                <label class="text-muted control-label col-sm-3">City</label>
                <span class="col-sm-9 form-control-static"> <?php echo $address->city."," ?> </span>
            </div>
            <div class="form-group">
                <label class="text-muted control-label col-sm-3">State </label>
                <span class="col-sm-9 form-control-static"> <?php echo $address->state."," ?> </span>
            </div>
            <div class="form-group">
                <label class="text-muted control-label col-sm-3"> Zip Code </label>
                <span class="col-sm-9 form-control-static"> <?php echo $address->zip."." ?> </span>
            </div>
        
     </div>
</div>
<?php
   require_once('../partials/footer.php');
?>

