<div class="clearfix col-sm-12">
    <div class="row form-horizontal">
        <div class="col-sm-6">
            <div class="row">
                <label class="col-sm-5 control-label">Card holder name</label>
                <div class="col-sm-7 form-control-static">
                    <?php echo esc($result->card()->firstName . " " . $result->card()->lastName) ?> 
                </div>
            </div>
            <div class="row">
                <label class="col-sm-5 control-label">Card type </label>
                <div class="col-sm-7 form-control-static">
                    <?php echo esc($result->card()->cardType) ?>
                </div>
            </div> 
        </div> 
        <div class="col-sm-6">
            <div class="row">
                <label class="col-sm-5 control-label">Card no</label>
                <div class="col-sm-7 form-control-static">
                    **** **** **** <?php echo esc($result->card()->last4) ?>
                </div>
            </div>
            <div class="row">
                <label class="col-sm-5 control-label"> Expiry</label>
                <div class="col-sm-7 form-control-static">
                    <?php echo esc($result->card()->expiryMonth . "/"  .  $result->card()->expiryYear) ?>
                </div>
            </div> 
        </div>
    </div>
</div>

