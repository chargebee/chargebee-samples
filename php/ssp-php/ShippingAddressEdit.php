<?php
 require_once('./header.php');
 require_once('../php_src/Util.php');
?>

<?php 
    $address= getShippingAddress($subscriptionId); 
?>
<script src="../assets/javascript/ssp/ssp.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $("#update-shipping-address").validate();
        $("#update-shipping-address").on('submit', ajaxHandler);
    })
    
</script>
<div id="cb-wrapper-ssp">
    <ul class="nav nav-tabs">
        <li class="active"><a href="">Shipping Address</a></li>
        <li class="pull-right">
           <?php include("./Logout.php") ?>
        </li>
    </ul>       
    <div id="cb-main-content" class="clearfix">                   
        <br>
        <div class="col-sm-12 clearfix">
            <form action="update_shipping_address" method="post" id="update-shipping-address">
                <br> 
                 <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="shipping_address[first_name]">First Name</label>
                             <small for="shipping_address[first_name]" class="pull-right text-danger">&nbsp;</small>
                            <input type="text" class="form-control" name="shipping_address[first_name]" placeholder="Enter your first name" 
                                   value="<?php echo ($address != null? esc($address->firstName) : "") ?>"
                                   required data-msg-required="cannot be blank" />
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="shipping_address[last_name]">Last Name</label>
                            <small for="shipping_address[last_name]" class="pull-right text-danger">&nbsp;</small>
                            <input type="text" class="form-control" name="shipping_address[last_name]" placeholder="Enter your last name" 
                                   value="<?php echo ($address != null ? esc($address->lastName) : "") ?>"
                                   required data-msg-required="cannot be blank" />
                        </div>
                    </div>
                </div>
                <div class="row">                            
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="shipping_address[line1]" >Address Line 1</label>
                            <small for="shipping_address[line1]" class="pull-right text-danger">&nbsp;</small>
                            <input type="text" name="shipping_address[line1]" class="form-control" placeholder="Enter your address line 1"
                                   value="<?php echo ($address != null ? esc($address->addr) : "" )  ?>"
                                   required data-msg-required="cannot be blank" />
                        </div>
                    </div>                            
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="shipping_address[line2]" >Address Line 2</label>
                            <small for="shipping_address[line2]" class="pull-right text-danger">&nbsp;</small>
                            <input type="text" name="shipping_address[line2]"  class="form-control" placeholder="Enter your address line 2" 
                                   value="<?php echo ($address != null ? esc($address->extendedAddr) : "" )  ?>" 
                                   required data-msg-required="cannot be blank" />
                        </div>
                    </div>                            
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="shipping_address[city]">City</label>
                            <small for="shipping_address[city]" class="pull-right text-danger">&nbsp;</small>
                            <input type="text" name="shipping_address[city]" class="form-control" placeholder="Enter your city"
                                   value="<?php echo $address != null ? esc($address->city) : ""  ?>" 
                                   required data-msg-required="cannot be blank" />
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="shipping_address[state]" >State</label>
                            <small for="shipping_address[state]"class="pull-right text-danger">&nbsp;</small>
                            <input type="text" name="shipping_address[state]" class="form-control" placeholder="Enter your state" 
                                   value="<?php echo $address != null ? esc($address->state) : ""  ?>"
                                   required data-msg-required="cannot be blank" />
                        </div>
                    </div>
                </div>                    
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="shipping_address[zip]" >Pincode</label>
                            <small for="shipping_address[zip]" class="pull-right text-danger">&nbsp;</small>
                            <input type="text" name="shipping_address[zip]" class="form-control" placeholder="Enter your zip" 
                                   value="<?php echo $address != null ? esc($address->zip) : "" ?>" 
                                   required data-msg-required="cannot be blank" />
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="shipping_address[country]">Country</label>
                            <small for="shipping_address[country]" class="pull-right text-danger">&nbsp;</small>
                            <select class="form-control" name="shipping_address[country]" placeholder="true" 
                                    required data-msg-required="cannot be blank">
                                <?php 
                                    $countryCodes = getCountryCodes();
                                    $shippingCountry = null;
                                    if( isset($address) && isset($address->country) ) {
                                        $shippingCountry = esc($address->country);
                                    }
                                ?>
                                <option value="" <?php echo ($shippingCountry == null ) ? "selected"  : "" ?> >
                                    Select your country
                                </option>
                                <?php foreach ($countryCodes as $code => $country ) { ?>
                                    <option value="<?php echo $code ?>" <?php echo ($code == $shippingCountry) ? "selected": "" ?> >
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

