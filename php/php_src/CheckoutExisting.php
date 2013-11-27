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


if($_POST) {
   try {
        
        $redirectURL=getCheckoutExistingUrl();
        /* 
         * This will redirect to the ChargeBee server.
         */
        header("Location: $redirectURL");	
        
   } catch(Exception $e) {
      customError($e);
   }
}
/* Checkouts the existing subscription to active state for the passed 
 * subscription id which is in trial state.
 */

function getCheckoutExistingUrl() {
        /* Request the ChargeBee server to get the hosted page url.
         * Passing Timestamp as ZERO to the trial end will immediately change the 
         * subscription from trial state to active state.
         * Note: Parameter embed(Boolean.TRUE) can be shown in iframe
         *       whereas parameter embed(Boolean.FALSE) can be shown as seperate page.
         */
         $result = Chargebee_HostedPage::checkoutExisting(array(
                                                "subscription"=> array(
                                                "id"=> $_POST["subscription_id"],
                                                "trial_end"=>0
                                        ),
                                        "embed"=>"false"
                                       ));
	return $result->hostedPage()->url;
}

?>
