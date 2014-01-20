<?php
require_once(dirname(__FILE__) . "/../php_src/Config.php");

/*
 * This file uses ChargeBee Estimate API to estimate the total amount during checkout.
 * When the user adds addon or coupon, this method is called via AJAX and the 
 * total order summary is estimated and resposne is sent back to the browser.
 */
try {
    
    /*
     * Forming create subscription estimate parameters to ChargeBee.
     */
    $subParams = array("planId" => "monthly");
    /*
     * Adding coupon to the create subscription estimate request, if it is set by user.
     */
    if (isset($_GET['coupon']) && $_GET['coupon'] != "") {
        $subParams["coupon"] = $_GET['coupon'];
    }
    $addons = array();
    /*
     * Adding addon1 to the addons array, if it is set by user.
     */
    if (isset($_GET['wallposters-quantity']) && $_GET['wallposters-quantity'] != "") {
        $wallPosters = array("id" => "wall-posters", "quantity" => $_GET['wallposters-quantity']);
        array_push($addons, $wallPosters);
    }
    /*
     * Adding addon2 to the addons array, if it is set by user.
     */
    if (isset($_GET['ebook']) && $_GET['ebook'] != "") {
        $ebook = array("id" => "e-book");
        array_push($addons, $ebook);
    }
    /*
     * Adding subscription and addons params to the create subscription estimate request
     */
    $params = array("subscription" => $subParams);
    $params["addons"] = $addons;
    $result = ChargeBee_Estimate::createSubscription($params);
    $estimate = $result->estimate();
    
} catch (ChargeBee_APIError $e) {
    
    /*
     * ChargeBee Exception are caught here.
     */
    $jsonError = $e->getJsonObject();
    $msg = "";
    /*
     * Checking whether the error is due to coupon. If the error is
     * due to coupon then http code returned by ChargeBee is sent back.
     * Other errors( i.e addon error ) are treated as Internal Server Error
     * and status code 500 is returned.
     */
    if ($jsonError["error_param"] == "subscription[coupon]" && $jsonError["error_code"] == "referenced_resource_not_found") {
        $msg = "Oops ! Looks like you have entered a wrong coupon code.";
        header('HTTP/1.0 ' . $jsonError["http_status_code"] . ' Error');
    } else if ($jsonError["error_code"] == "coupon_expired") {
        $msg = "Sorry. The coupon code that you entered has expired.";
        header('HTTP/1.0 ' . $jsonError["http_status_code"] . ' Error');
    } else if ($jsonError["error_code"] == "max_redemptions_reached") {
        $msg = "Oops ! Looks like your coupon code has been exhausted";
        header('HTTP/1.0 ' . $jsonError["http_status_code"] . ' Error');
    } else {
        $msg = "Sorry, There was some problem processing the request. We will get back to you shortly.";
        header('HTTP/1.0 ' . '500 Error');
    }
    $jsonResponse = array("error_msg" => $msg);
    print(json_encode($jsonResponse, true));
    
    return;
} catch (Exception $e) {
    /*
     * Other errors are caught here and handled as Internal Server Error.
     */
    $jsonError = array("error_msg" => "Sorry, There was some problem processing the request. We will get back to you shortly.");
    header("HTTP/1.0 500 Error");
    print json_encode($jsonError, true);
    return;
}
?>
<div class="row">
    <div class="col-xs-12">        
        <div class="page-header"><h3>Your Order Summary</h3></div>
        <ul class="text-right list-unstyled">
            
            <?php
            foreach ($estimate->lineItems as $li) {
                ?>
                <li class="row">
                    <span class="col-xs-8"> 
                        <?php echo $li->description . " &times; " . $li->quantity . " item(s)" ?> 
                    </span>
                    <span class="col-xs-4">$ <label> <?php echo number_format($li->amount / 100, 2, '.', '') ?> </label></span>
                </li>
                <?php
            }
            ?>
            
            <?php
            if (isset($estimate->discounts)) {
                foreach ($estimate->discounts as $dis) {
                    ?>
                    <li class="row">
                        <span class="col-xs-8"><?php echo $dis->description; ?> </span>
                        <span class="col-xs-4">(-) $ <?php echo number_format($dis->amount / 100, 2, '.', ''); ?> </span>
                    </li>
                <?php }
            }
            ?>
            <hr class="dashed">
            <li class="row">
                <h4>
                    <strong class="col-xs-8">Total Amount </strong>
                    <strong class="col-xs-4">$ <?php echo number_format($estimate->amount / 100, 2, '.', '') ?></strong>
                </h4>
            </li>
        </ul>
        <hr>
        <?php if (!isset($_GET['coupon']) || $_GET['coupon'] == "") { ?> 
            <p>Have coupon code?</p>
            <div class="input-group">
                <input id="coupon" type="text" class="form-control" name="coupon">
                <span class="input-group-btn">
                    <input id="apply-coupon" class="btn btn-info" type="button" value="Apply Coupon">
                </span>
            </div>
            <h6 class="coupon_process process" style="display:none;">Processing&hellip;</h6>
            <hr>
        <?php } else { ?>
            <a id="remove-coupon" class="remove-coupon pull-right btn"> Remove coupon </a>
            <input type="hidden" id="coupon" name="coupon" value="<?php echo $_GET["coupon"] ?>" /><br><br>
        <?php } ?>
        <small class="error_msg text-danger" style="display: none">Internal Server Error</small>
    </div>
</div>
