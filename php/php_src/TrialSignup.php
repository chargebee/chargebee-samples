<?php
/*
 * Adding ChargeBee php libraries and configuration files.
 */
require_once(dirname(__FILE__) . "/Config.php");
require_once(dirname(__FILE__) . "/ErrorHandler.php");
require_once(dirname(__FILE__) . "/Util.php");

if($_POST) { 
   validateParameters($_POST);
   try{
        $jsonResp = array();
        $result = createTrialSubscription();
        
        /*
         * Forwarding to success page after trial subscription created successfully in ChargeBee.
         */
        $queryParameters = "name=" . urlencode($result->customer()->firstName) 
                           . "&planId=" . urlencode($result->subscription()->planId);        
        $jsonResp["forward"] = "thankyou.html?" . $queryParameters;
        echo json_encode($jsonResp, true);
        
    } catch(ChargeBee_InvalidRequestException $e) {
		handleInvalidRequestErrors($e,"plan_id");
    } catch(Exception $e) {
	    handleGeneralErrors($e);
    }
}
/* 
 * Creates the trial subscription from the request parameters with trial plan 'basic'
 * and also adds the billing address of the customer in ChargeBee.
 */
function createTrialSubscription() {
    
    /* 
    * Constructing the parameters to be send to the ChargeBee. For demo 
    * purpose a plan with id 'basic' is hard coded which has 15 month trial
    * in ChargeBee app. 
    * Note : Here customer object received from client side is sent directly 
    *        to ChargeBee.It is possible as the html form's input names are 
    *        in the format customer[<attribute name>] eg: customer[first_name] 
    *        and hence the $_POST["customer"] returns an associative array of the attributes.
    *               
    */    
    $createTrialReqParams = array( "plan_id" => "basic",
                                   "customer" => $_POST["customer"] );
    /* 
     * Sends request to the ChargeBee server to create trial subscription
     * using the trial plan id set in createTrialReqParams array.
     */
    $result = ChargeBee_Subscription::create($createTrialReqParams);
    
    return $result;        
}


?>
