<?php
  require_once(dirname(__FILE__) . "/../php_src/Config.php");
  require_once(dirname(__FILE__) . '/../php_src/Util.php'); 
  require_once('../partials/header.php');
?>
<?php
   
  $id = $_GET["customer_id"];
  $subscriptionDetail = ChargeBee_Subscription::retrieve($id); 
  
?>
<script type="text/javascript">
    $(document).ready(function(){
        $('.update_msg').show();
        setTimeout(function(){$('.update_msg').slideUp();},2000);
    });       
</script>
<br>
    <div id="cb-demo-ssp">
     <?php if ( isset($_GET["updated"]) && !is_null($_GET["updated"]) ) { ?>
          <div class="update_msg text-center">
            <p class="alert alert-success">
              <span class="glyphicon glyphicon-ok-sign"></span> Your card details have been saved successfully.
                      </p>
          </div> 
    <?php } ?>
        <h2 class="h3">
            <a align="right" href="/update_card" class="pull-right btn btn-danger"><span class="glyphicon glyphicon-off"></span> log out </a>   
            <span class="text-muted">Hi</span>  <?php echo esc($subscriptionDetail->customer()->firstName) ?>, 
              
         </h2>    
     <div class="row">
                <div class="col-sm-12">                    
                    <h3>Account Information</h3>
                    <div class="col-sm-6 form-horizontal">
                        <div class="form-group">
                          <label class="col-sm-5 control-label">Name</label>
                          <div class="col-sm-7">
                            <p class="form-control-static"><?php echo  esc($subscriptionDetail->customer()->firstName) ." ". esc($subscriptionDetail->customer()->lastName) ?></p>
                          </div>
                        </div>
                        <div class="form-group">
                          
                          <label class="col-sm-5 control-label">Email</label>
                          <div class="col-sm-7">
                            <p class="form-control-static"><?php echo esc($subscriptionDetail->customer()->email) ?></p>
                          </div>
                          
                        </div>                    
                        <div class="form-group">
                          <label class="col-sm-5 control-label">Organization</label>
                          <div class="col-sm-7">
                            <p class="form-control-static">  <?php echo esc($subscriptionDetail->customer()->company) ?></p>
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-sm-5 control-label">Phone</label>
                          <div class="col-sm-7">
                            <p class="form-control-static"> <?php echo esc($subscriptionDetail->customer()->phone) ?></p>
                          </div>
                        </div> 
                    </div>
             </div>
            </div>            
            <div class="row">            
                <div class="col-sm-12">                    
                    <h3>Subscription Information</h3>                                                        
                    <?php $planDetail = ChargeBee_Plan::retrieve($subscriptionDetail->subscription()->planId);
                          $plan = $planDetail->plan();
                     ?>
                    <div class="col-sm-6 form-horizontal">
                         <div class="form-group">
                          <label class="col-sm-5 control-label"> Plan Status</label>
                          <div class="col-sm-7">
                            <p class="form-control-static"> 
                                <span class="label-<?php echo $subscriptionDetail->subscription()->status ?>">
                                    <?php echo esc($subscriptionDetail->subscription()->status) ?>
                                </span>
                            </p>
                          </div>
                         </div>
                    <div class="form-group">
                          <label class="col-sm-5 control-label"> Plan Name</label>
                          <div class="col-sm-7">
                            <p class="form-control-static"><?php echo esc($plan->name) ?></p>
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-sm-5 control-label"> Plan Price</label>
                          <div class="col-sm-7">
                            <p class="form-control-static">$<?php echo $plan->price/100 ?></p>
                          </div>
                        </div>                                            
                    </div>                         
                    </div>
                  </div>                                        
               <div class="row">
                
               <?php if($subscriptionDetail->card() == null) { ?> 
                  <div class="col-sm-12">
                    <h3>Card Information</h3>
                    <br>
                        Please  <a href="update?customer_id=<?php echo esc($subscriptionDetail->customer()->id) ?>">
                       add your card details</a> before the trial ends to ensure uninterrupted service. 
                  </div>
                
               <?php } else { ?>
                <div class="col-sm-12">                    
                    <h3>Card Information &nbsp; 
            <a class="btn btn-primary btn-xs" href="update?customer_id=<?php echo esc($subscriptionDetail->customer()->id) ?>">
            Update Card
            </a>
                  </h3>
                    
                    <div class="col-sm-6 form-horizontal">
                        <div class="form-group">
                          <label class="col-sm-5 control-label">Card Holder Name</label>
                          <div class="col-sm-7">
                              <p class="form-control-static"><?php echo esc($subscriptionDetail->card()->firstName) ?> </p>
                            </div>
                        </div>
                        <div class="form-group">
                          <label class="col-sm-5 control-label">Card Type</label>
                          <div class="col-sm-7">
                              <p class="form-control-static"><?php echo esc($subscriptionDetail->card()->cardType) ?></p>
                           </div>
                        </div> 
                        <div class="form-group">
                          
                          <label class="col-sm-5 control-label">Card No</label>
                          <div class="col-sm-7">
                            <p class="form-control-static">  <?php echo "**** **** **** " . esc($subscriptionDetail->card()->last4) ?></p>
                          </div>
                          
                        </div>
                         <div class="form-group">
                          <label class="col-sm-5 control-label">Card Expiry Month</label>
                          <div class="col-sm-7">
                              <p class="form-control-static"> <?php echo $subscriptionDetail->card()->expiryMonth ?> </p>
                          </div>
                        </div> 
                        <div class="form-group">
                          <label class="col-sm-5 control-label">Card Expiry Year</label>
                          <div class="col-sm-7">
                              <p class="form-control-static"> <?php echo $subscriptionDetail->card()->expiryYear?> </p>
                          </div>
                        </div> 
                    </div> 
               </div>
          <?php } ?>          
       </div>                     
                            
</div>
<?php  
   require_once('../partials/footer.php');
?>
