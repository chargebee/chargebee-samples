<?php
/*
 * Adding ChargeBee php libraries and configuration files.
 */
require_once(dirname(__FILE__) . "/Config.php");
require_once(dirname(__FILE__) . "/Util.php");
require_once(dirname(__FILE__) . "/ErrorHandler.php");

/* Checkouts the existing subscription to active state for the passed 
 * subscription id which is in trial state.
 */
if($_POST) {
	  validateParameters($_POST);
	  try {
		  	
	        /* Request the ChargeBee server to get the hosted page url.
	         * Passing Timestamp as ZERO to the trial end will immediately change the 
	         * subscription from trial state to active state.
	         * Note: Parameter embed specifies the returned hosted page URL 
	         *       is shown in iframe or as seperate page.
	         */
			 $hostUrl = getHostUrl();
	         $result = Chargebee_HostedPage::checkoutExisting(
			     array("subscription"=> array( "id"=> $_POST["subscription_id"],
					       "trial_end"=> 0 
					  	),
					   "embed"=> "false",
					   "redirect_url"=> $hostUrl . "/checkout_existing/redirect_handler",
					   "cancel_url"=> $hostUrl . "/checkout_existing/profile.html"
	           ));
  	   	    
			
	        
	        $redirectURL=$result->hostedPage()->url;
	        /* 
	         * This will redirect to the ChargeBee server.
	         */
	        header("Location: $redirectURL");        
	          
	  } catch(Exception $e) {
		 customError500($e);
	  }      
}

?>
