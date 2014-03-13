<?php  require_once('../php_src/Util.php') ?>

<?php $address = getShippingAddress($subscriptionId); ?>
    
<h3>Shipping Address<a href="shipping_address_edit" class="h6 pull-right">
        <span class="glyphicon glyphicon-pencil"></span> 
 <?php if (isset($address) || $address != "" ) { ?>
        Edit</a></h3>                 
<div class="clearfix col-sm-12">
    <?php echo (isset($address->firstName) ? $address->firstName : "") ?>
    <?php echo (isset($address->lastName) ? " " . $address->lastName . "<br>" : ""); ?>
    <?php echo ( isset($address->addr) ? $address->addr ."<br>" : "" ) ?>
    <?php echo (isset($address->extendedAddr) ? $address->extendedAddr . "<br>" : "") ?>
    <?php echo (isset($address->city) ? $address->city . "<br>" : "" ) ?>
    <?php echo (isset($address->state) ? $address->state . "<br>" : "") ?>
    <?php echo (isset($address->country) ? getCountryCodes()[$address->country] . "<br>" : "" ) ?>
    <?php echo (isset($address->zip) ? $address->zip . "<br>" : "" ) ?>
    
</div>
<?php } else { ?>
Add</a></h3>                 
<div class="clearfix col-sm-12">
    <div class="text-center">
        <p class="alert alert-info">
            <span class="glyphicon glyphicon-info-sign"></span>
            Please add your shipping address
        </p> 
    </div>
</div>
<?php } ?>

