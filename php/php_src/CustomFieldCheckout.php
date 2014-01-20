<?php
/*
 * Adding ChargeBee php libraries
 */
require_once(dirname(__FILE__) . "/Config.php");

/*
 * Demo on how to use Custom Field created at your ChargeBee site and also  
 * create a new subscription in ChargeBee.
 */

try {
     $day = $_POST["dob_day"];
     $month = $_POST["dob_month"];
     $year = $_POST["dob_year"];
     /*
      * Parsing the Date String and coverting it to Date.
      */
     $dob = strtotime($day. "-" . $month . "-" . $year);
     /*
      * Calling ChargeBee Create Subscription API to create a new subscription
      * in ChargeBee for the passed plan id and customer attributes. 
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
     $result = ChargeBee_Subscription::create(    array("plan_id" => "basic",
                                                        "customer" => $customer) );
     
     /*
      * Forwarding to thank you page after subscription created successfully.
      */
     
     $queryParameters = "subscription_id=" . urlencode($result->subscription()->id);
     $jsonResp["forward"] = "thankyou?" . $queryParameters;
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


?>
