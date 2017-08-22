<?php
/*
 * Adding ChargeBee php libraries and configuration files.
 */
require_once(dirname(__FILE__) . "/Config.php");
require_once(dirname(__FILE__) . "/Util.php");
require_once(dirname(__FILE__) . "/ErrorHandler.php");


/*
 * Calling Checkout New Hosted Page API to create a new Subscription for the
 * passed plan id. The customers are redirected to the ChargeBee hosted page
 * with the returned response hosted page url. 
 *
 * Since it is demo, plan with id 'basic' is hard coded here.
 */
$planId = "basic";
$hostUrl = getHostUrl();
$result = Chargebee_HostedPage::checkoutNew(array(
    "subscription" => array("planId" => $planId),
    "embed" => "false",
    "redirectUrl" => $hostUrl . "/checkout_new/redirect_handler",
    "cancelUrl" => $hostUrl . "/checkout_new/index.html" 
));



$hostedPageUrl = $result->hostedPage()->url;
/* 
 * This will redirect the customers to the ChargeBee server.
 */
header("Location: $hostedPageUrl");



?>
