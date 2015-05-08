<?php
/*
 * Adding ChargeBee php libraries
 */
require_once(dirname(__FILE__) . "/Config.php");
require_once(dirname(__FILE__) . "/Util.php");

 validateParameters($_POST);
/*
 * Demo on how to use Custom Field created at your ChargeBee site and also  
 * create a new trial subscription in ChargeBee.
 */
try {
     $day = ($_POST["dob_day"] > 9 ? "" : "0") . $_POST["dob_day"] ;
     $month = ($_POST["dob_month"] > 9 ? "" : "0") . $_POST["dob_month"];
     $year = $_POST["dob_year"];
     /*
      * Parsing the Date String and coverting it to Date.
      */
     $dob = $year. "-" . $month . "-" . $day;
     /*
      * Calling ChargeBee Create Subscription API to create a new subscription
      * in ChargeBee for the passed plan ID and customer attributes. 
      * Additionally you can send the custom field parameters created for your
      * ChargeBee site.
      * 
      * To create custom field for your site go to Settings-> Request Custom Field
      * and fill the request form.
      * 
      * For demo puropose plan with id 'basic' is hard coded here.
      * Note : Here customer object received from client side is sent directly 
      *        to ChargeBee.It is possible as the html form's input names are 
      *        in the format customer[<attribute name>] eg: customer[first_name] 
      *        and hence the $_POST["customer"] returns an associative array of the attributes.
      *               
      */
     
     $customer =  $_POST["customer"];
     $customer["cf_date_of_birth"] = $dob;
     $result = ChargeBee_Subscription::create( 
	 					array("plan_id" => "basic",
                              "customer" => $customer) );
     
     /*
      * Forwarding to thank you page after subscription created successfully.
      */
     
	 $subscription = $result->subscription();
     $queryParameters = "subscription_id=" . urlencode($subscription->id);
     $jsonResp["forward"] = "thankyou?" . $queryParameters;
     echo json_encode($jsonResp, true);
     
 } catch(ChargeBee_InvalidRequestException $e) {
   handleInvalidRequestErrors($e, "plan_id");
 } catch(Exception $e) {
	handleGeneralErrors($e);
 }

?>
