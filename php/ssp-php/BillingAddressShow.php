<div class="clearfix col-sm-12">
  
  <?php $billingAddress = $result->customer()->billingAddress ?>
  <?php echo (isset($billingAddress->firstName) ? esc($billingAddress->firstName) : "" )?>
  <?php echo (isset($billingAddress->lastName) ? esc($billingAddress->lastName) : "" )?>
  <?php echo (isset($billingAddress->lastName) || isset($billingAddress->firstName) ? "<br>" : "") ?>
  <?php echo (isset($billingAddress->line1) ? esc($billingAddress->line1) . "<br>" : "") ?>
  <?php echo (isset($billingAddress->line2) ? esc($billingAddress->line2) . "<br>" : "") ?>
  <?php echo (isset($billingAddress->city) ? esc($billingAddress->city) . "<br>" : "") ?>
  <?php echo (isset($billingAddress->state) ? esc($billingAddress->state) . "<br>" : "") ?>
  <?php $countryCodes = getCountryCodes(); ?>
  <?php echo (isset($billingAddress->country) ? esc($countryCodes[$billingAddress->country]) . "<br>" : "" ) ?>
  <?php echo (isset($billingAddress->zip) ? esc($billingAddress->zip) . "<br>" : "") ?>

</div>

