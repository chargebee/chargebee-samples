<?php
/*
 * Adding ChargeBee php libraries
 */
require(dirname(__FILE__) . "/lib/ChargeBee.php");
require(dirname(__FILE__) . "/ErrorHandler.php");

/* 
 * Sets the environment for calling the Chargebee API.
 * You need to sign up at ChargeBee app to get this credential.
 */
ChargeBee_Environment::configure("<your-site>","<your-api-key>");



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
