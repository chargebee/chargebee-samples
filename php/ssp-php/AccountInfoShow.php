<div class="clearfix col-sm-12">
    <div class="row form-horizontal">
        <div class="col-sm-6">
           
            <div class="row">
                <label class="col-sm-5 control-label">Name</label>
                <div class="col-sm-7 form-control-static">
                    <?php echo $result->customer()->firstName
                                    . " " . $result->customer()->lastName ?> 
                </div>
            </div>
           
            <div class="row">
                <label class="col-sm-5 control-label">Email</label>
                <div class="col-sm-7 form-control-static">
                    <?php echo $result->customer()->email ?>
                </div>
            </div> 
        </div> 
        <div class="col-sm-6">
            <div class="row">
                <label class="col-sm-5 control-label">Organization</label>
                <div class="col-sm-7 form-control-static">
                    <?php echo $result->customer()->company ?>
                </div>
            </div>
            <div class="row">
                <label class="col-sm-5 control-label">Phone</label>
                <div class="col-sm-7 form-control-static">
                    <?php echo $result->customer()->phone ?>
                </div>
            </div> 
        </div>
    </div>
</div>
