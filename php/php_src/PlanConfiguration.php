<?php
/*
 * Adding ChargeBee php libraries and configuration files.
 */
require_once(dirname(__FILE__) . "/Config.php");

if($_GET) {
    $demoName = $_GET['demo_name'];
    try{
        $returnParameters = "";
	if( $demoName == "trial_signup" ) {
	    createPlan("Basic","basic",1000,15);      
            $returnParameters = "demo_name=Trial Signup&plan=Basic";
	} else if ($demoName == "checkout_new" ) {
	    createPlan("Basic","basic",1000,15);      
            $returnParameters = "demo_name=Checkout New&plan=Basic"; 
        } else if($demoName == "checkout_two_step" ) {
	    createPlan("Basic","basic",1000,15);      
            $returnParameters = "demo_name=Two-step Checkout&plan=Basic";
        } else if($demoName == "checkout_existing") {
	    createSubscription("Kim","Burner","kim@acme.com"); 
            $returnParameters = "demo_name=Checkout Existing&plan=Basic&customer=Kim Burner";  
	} else if( $demoName == "update_card" ) {
	    createSubscription("John","Wayne","john@acmeinc.com");
            $returnParameters = "demo_name=Update Card&plan=Basic&customer=John Wayne";
	} else if( $demoName == "custom_field" ) {
	    createPlan("Basic","basic",1000,15);      
	   $returnParameters = "msg=This tutorial requires custom fields to be created for your ChargeBee site. "
                              . "Submit your custom field request from your site settings."
                              . "This demo requires a <b>\"DOB\"</b> and <b>\"Comics Type\"</b> "
                              . "custom fields but you can request for any other fields too."; 
	} else if ( $demoName == "stripe_js" ) {
	    createPlan("Annual","annual",2000);
            $returnParameters = "demo_name=Stripe Js&plan=Basic"; 
	} else if( $demoName == "estimate") {
	    $addon1 = createAddon("Wall Posters", "wall-posters", 300, "quantity");
	    $addon2 = createAddon("E Book", "e-book", 200);
	    $plan = createPlan("Monthly", "monthly", 600);
            $returnParameters = "demo_name=Estimate api&plan=Monthly&addon=E-book&addon=Wall Posters";
	} else if( $demoName == "usage_based_billing" ) {
	    $returnParameters = "msg=To generate a <b>\"Pending\" </b> invoice, you need to enable <b>\"Notify and wait to close invoice\"</b> "
                                . "in your site settings. Once enabled, try to generate an invoice for a subscription by changing "
                                . "the subscription's plan.";
	} else if( $demoName == "ssp" ) {
           createSubscription("John","Doe","john@acmeinc.com");
           $returnParameters = "demo_name=Self service portal&plan=Basic&customer=John Doe";
        } else if( $demoName == "stripe-popup-js" ) {
           createPlan("Basic","basic",1000,15);
           $returnParameters = "demo_name=Stripe checkout popup&plan=Basic";
        } else if ( $demoName == "braintree-js" ) {
           createPlan("Professional", "professional", 20, 10);
           $returnParameters = "demo_name=Braintree js Checkout&plan=Professional";
        } else if( $demoName == "checkout_iframe" ) { 
            createPlan("Basic", "basic", 1000, 15);
            $returnParameters = "demo_name=Checkout using iFrame&plan=Basic";
        } else {
	    header("HTTP/1.0 400 Error");
	    return;
	}
        header("Location: /index.html?" . urlencode($returnParameters));
    } catch(ChargeBee_APIError $e) {
	    header("HTTP/1.0 500 Error");
    } catch(Exception $e) {
	    header("HTTP/1.0 500 Error");
    }

}

function createSubscription($firstName, $lastName, $email) {
    $plan =  createPlan("Basic", "basic", 1000, 15);
    $createSubscriptionParam = array("plan_id" => $plan->id,
	    "id" => $email,"customer[first_name]" => $firstName,
	    "customer[last_name]" => $lastName, "customer[email]" => $email);

    try{
		$result = ChargeBee_Subscription::create($createSubscriptionParam);
		return $result;
	} catch(ChargeBee_InvalidRequestException $e) {
		if($e->getApiErrorCode() == "duplicate_entry" && $e->getParam() != null
			 && $e->getParam() == "id") {
 	    	$result = ChargeBee_Subscription::retrieve($email);
 	    	return $result;		
		} else {
			throw $e;
		}
    } 
}

function createPlan($name, $id, $price, $trialPeriod=null) {
    $createPlanArray = array("name" => $name, "id" => $id, 
	    "invoice_name"=>$name, "price" => $price );

    if( $trialPeriod != null ) {
		$createPlanArray['trial_period'] = $trialPeriod;
		$createPlanArray['trial_period_unit'] = "day";
    } 

    try {
		$result = ChargeBee_Plan::create($createPlanArray);
		return $result->plan();
	} catch(ChargeBee_InvalidRequestException $e) {
		if($e->getApiErrorCode() == "duplicate_entry" && $e->getParam() != null
			 && $e->getParam() == "id") {
		 	$result = ChargeBee_Plan::retrieve($id);  
		 	return $result->plan();
		} else {
			throw $e;
		}
    } 
}

function createAddon($name, $id, $price, $addonType = "on_off" ) {
    $createAddonArray = array( "name" => $name, "id" => $id, "invoice_name" => $name,
	    "price" => $price, "charge_type" => "recurring","period" => 1,
	    "period_unit" => "month", "type" => $addonType );

    if($addonType == "quantity" ) {
		$createAddonArray['unit'] = "nos";
    }

    try{
		$result = ChargeBee_Addon::create($createAddonArray);
		return $result->addon();
	} catch(ChargeBee_InvalidRequestException $e) {
		if($e->getApiErrorCode() == "duplicate_entry" && $e->getParam() != null
			 && $e->getParam() == "id") {
		 	 $result = ChargeBee_Addon::retrieve($id);
		 	 return $result->addon();
		} else {
			throw $e;
		}
    }
}

?>
