<div class="clearfix col-sm-12">
  
  <?php $billingAddress = $result->customer()->billingAddress ?>
  <?php echo (isset($billingAddress->firstName) ? $billingAddress->firstName : "" )?>
  <?php echo (isset($billingAddress->lastName) ? $billingAddress->lastName : "" )?>
  <?php echo (isset($billingAddress->lastName) || isset($billingAddress->firstName) ? "<br>" : "") ?>
  <?php echo (isset($billingAddress->line1) ? $billingAddress->line1 . "<br>" : "") ?>
  <?php echo (isset($billingAddress->line2) ? $billingAddress->line2 . "<br>" : "") ?>
  <?php echo (isset($billingAddress->city) ? $billingAddress->city . "<br>" : "") ?>
  <?php echo (isset($billingAddress->state) ? $billingAddress->state . "<br>" : "") ?>
  <?php echo (isset($billingAddress->country) ? getCountryCodes()[$billingAddress->country] . "<br>" : "" ) ?>
  <?php echo (isset($billingAddress->zip) ? $billingAddress->zip . "<br>" : "") ?>

</div>

