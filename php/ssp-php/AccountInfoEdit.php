<?php
 require_once('./header.php');
  $customerId = $_GET['customer_id'];
  $customer = ChargeBee_Customer::retrieve($customerId)->customer();
?>

<script src="../assets/javascript/ssp/ssp.js"></script>

<script type="text/javascript">
   
    $(document).ready(function() {
        $("#update-account-info").validate();
        $('#update-account-info').on('submit', ajaxHandler)
    })
</script>
<div id="cb-wrapper-ssp">
    <ul class="nav nav-tabs">
        <li class="active"><a href="">Customer Information</a></li>
        <li class="pull-right">
           <?php include("./Logout.php") ?>
        </li>
    </ul>       
    <div id="cb-main-content" class="clearfix">     
        <br> 
        <div class="clearfix col-sm-12">
            <form action ="update_account_info" method="post" id="update-account-info">                        
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <small for="first_name" class="pull-right text-danger">&nbsp;</small>
                            <input type="text" class="form-control" name="first_name" placeholder="Enter your first name" 
                                   value="<?php echo $customer->firstName ?>" required data-msg-required="cannot be blank">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <small for="last_name" class="pull-right text-danger">&nbsp;</small>
                            <input type="text" class="form-control" name="last_name" placeholder="Enter your last name" 
                                   value="<?php echo $customer->lastName ?>" required data-msg-required="cannot be blank">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label  for="email">Email</label>
                            <small for="email" class="pull-right text-danger">&nbsp;</small>
                            <input type="email" class="form-control" name="email" placeholder="Enter your email address" 
                                   value="<?php echo $customer->email ?>" required data-msg-required="cannot be blank">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="company">Organization</label>
                            <small for="company" class="pull-right text-danger">&nbsp;</small>
                            <input type="text" class="form-control" name="company" placeholder="Enter your organization name" 
                                   value="<?php echo $customer->company ?>" required data-msg-required="cannot be blank">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <small for="phone" class="pull-right text-danger">&nbsp;</small>
                            <input type="text" name="phone" class="form-control" placeholder="Enter your phone number" 
                                   value="<?php echo $customer->phone ?>" required data-msg-required="cannot be blank">
                        </div>
                    </div>
                </div>                              
                <hr class="clearfix">                                                
                <div class="form-inline">
                    <input type="hidden" value="<?php echo $customerId ?>" name="customer_id"/>
                    <span class="form-group">
                        <input type="submit" value="Update" class="btn btn-sm btn-primary">
                    </span>
                    <span class="form-group">
                        <a href="/ssp-php/subscription" class="btn btn-sm btn-link">Cancel</a>
                    </span>
                    <span class="alert-danger"></span>
                </div>
            </form> 
        </div>               
    </div>        
</div>
<?php
 require_once('./footer.php');
?>


