<?php
/*
 * Adding ChargeBee php libraries and configuration files.
 */
require_once(dirname(__FILE__) . "/Config.php");
require_once(dirname(__FILE__) . "/Util.php");
require_once(dirname(__FILE__) . "/ErrorHandler.php");


/*
 * Calling ChargeBee Hosted Page API to create a new Subscription for the
 * specified planId and redirecting the customer to the ChargeBee server
 * using the url returned by ChargeBee Hosted Page API. 
 *
 * For demo purpose plan with id 'basic' is hard coded here.
 */
$planId = "basic";
$result = Chargebee_HostedPage::CheckoutNew(array("subscription"=>array("planId"=>$planId),
                                                   "embed"=>"false"));



$hostedPageUrl = $result->hostedPage()->url;
/* 
 * This will redirect to the ChargeBee server.
 */
header("Location: $hostedPageUrl");



?>
