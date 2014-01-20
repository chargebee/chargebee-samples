<?php
/*
 * Adding ChargeBee php libraries and configuration files.
 */
require_once(dirname(__FILE__) . "/Config.php");

if($_POST) { 
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
        
   } catch(ChargeBee_APIError $e) {
        /* ChargeBee exception is captured through APIException and 
         * the error messsage( as JSON) is sent to the client.
         */
        $jsonError = $e->getJsonObject();
        header('HTTP/1.0 ' . $jsonError["http_status_code"] . ' Error');
        print(json_encode($jsonError,true));
   } catch(Exception $e) {
        /* Other errors are captured here and error messsage (as JSON) 
         * sent to the client.
         * Note: Here the subscription might have been created in ChargeBee 
         *       before the exception has occured.
         */
        $jsonError = array("error_msg"=>"Error while creating your subscription");
        header("HTTP/1.0 500 Error");
        print(json_encode($jsonError,true));
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
