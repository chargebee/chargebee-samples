<?php
 require_once('./header.php');
?>

<?php 
    $customer = ChargeBee_Customer::retrieve($customerId)->customer();    
    $billingAddress = $customer ->billingAddress;

?>
<script src="../assets/javascript/ssp/ssp.js"></script>

<script type="text/javascript">
    $(document).ready(function(){
        $("#update-billing-info").validate();    
        $("#update-billing-info").on('submit', ajaxHandler);    
    })
    
</script>
<div id="cb-wrapper-ssp">
    <ul class="nav nav-tabs">
        <li class="active"><a href="">Billing Information</a></li>
        <li class="pull-right">
            <?php include("./Logout.php") ?>
        </li>
    </ul>      
    <div id="cb-main-content" class="clearfix">                   
        <br>
        <div class="col-sm-12 clearfix">
            <form action="update_billing_info" method="post" id="update-billing-info">
                <hr class="clearfix">
                 <div class="row">
                    <div class="col-sm-6">
                        
                        <div class="form-group">
                            <label for="billing_address[first_name]">First Name</label>
                            <small for="billing_address[first_name]" class="pull-right text-danger">&nbsp;</small>
                            <input type="text" class="form-control" name="billing_address[first_name]" placeholder="Enter your first name" 
                                   value="<?php echo isset($billingAddress->firstName) ?  esc($billingAddress->firstName) :  "" ?>" 
                                   required data-msg-required="cannot be blank">
                        </div>
                        
                     </div>
                     <div class="col-sm-6">
                        <div class="form-group">
                            <label for="billing_address[last_name]">Last Name</label>
                            <small for="billing_address[last_name]" class="pull-right text-danger">&nbsp;</small>
                            <input type="text" class="form-control" name="billing_address[last_name]" placeholder="Enter your last name" 
                                   value="<?php echo isset( $billingAddress->lastName) ? esc($billingAddress->lastName) :  "" ?>"
                                   required data-msg-required="cannot be blank" >
                        </div>
                    </div>
                </div>
                <div class="row">                            
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="billing_address[line1]">Address Line 1</label>
                            <small for="billing_address[line1]" class="pull-right text-danger">&nbsp;</small>
                            <input type="text" name="billing_address[line1]" class="form-control" placeholder="Enter your address line 1" 
                                   value="<?php echo isset( $billingAddress->line1 ) ?  esc($billingAddress->line1) :  "" ?>"
                                   required data-msg-required="cannot be blank" />
                        </div>
                    </div>                            
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="billing_address[line2]">Address Line2</label>
                            <small for="billing_address[line2]"class="pull-right text-danger">&nbsp;</small>
                            <input type="text" name="billing_address[line2]" class="form-control" placeholder="Enter your address line 2" 
                                   value="<?php echo isset( $billingAddress->line2) ? esc($billingAddress->line2) : ""  ?>"
                                   required data-msg-required="cannot be blank" />
                        </div>
                    </div>                            
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="billing_address[city]">City</label>
                            <small for="billing_address[city]" class="pull-right text-danger">&nbsp;</small>
                            <input type="text" name="billing_address[city]" class="form-control" placeholder="Enter your city" 
                                   value="<?php echo isset($billingAddress->city) ? esc($billingAddress->city) : "" ?>"/>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="billing_address[state]" >State</label>
                            <small for="billing_address[state]"class="pull-right text-danger">&nbsp;</small>
                            <input type="text" name="billing_address[state]"class="form-control" placeholder="Enter your state" 
                                   value="<?php echo isset($billingAddress->state) ? esc($billingAddress->state) : "" ?>"
                                   required data-msg-required="cannot be blank"/>
                        </div>
                    </div>
                </div>                    
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="billing_address[zip]" >Zip</label>
                            <small for="billing_address[zip]" class="pull-right text-danger">&nbsp;</small>
                            <input type="text" name="billing_address[zip]" class="form-control" placeholder="Enter your zip" 
                                   value="<?php echo isset($billingAddress->zip) ? esc($billingAddress->zip) : "" ?>" 
                                   required data-msg-required="cannot be blank" />
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="billing_address[country]">Country</label>
                           <small for="billing_address[country]" class="pull-right text-danger">&nbsp;</small>
                           <select class="form-control" name="billing_address[country]" placeholder="true" 
                                    required data-msg-required="cannot be blank">
                                <?php 
                                    $countryCodes = getCountryCodes();
                                    $billingCountry = null;
                                    if( isset($billingAddress) && isset($billingAddress->country) ) {
                                        $billingCountry = esc($billingAddress->country);
                                    }
                                ?>
                                <option value="" <?php echo ($billingCountry == null ) ? "selected"  : "" ?> >
                                    Select your country
                                </option>
                                <?php foreach ($countryCodes as $code => $country ) { ?>
                                    <option value="<?php echo $code ?>" <?php echo ($code == $billingCountry) ? "selected": "" ?> >
                                        <?php echo $country ?>
                                    </option>
                                <?php  } ?>  
                            </select>
                        </div>
                    </div>
                </div>
                <hr class="clearfix">                                                
                <div class="form-inline">
                    <span class="form-group"><input type="submit" value="Update Address" class="btn btn-sm btn-primary"></span>
                    <span class="form-group"><a href="/ssp-php/subscription" class="btn btn-sm btn-link">Cancel</a></span>
                    <span class="alert-danger"></span>
                </div>
            </form>
        </div>                                        
    </div>
</div>
<?php
 require_once('./footer.php');
?>

