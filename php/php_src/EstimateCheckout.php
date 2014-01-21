<?php
/*
 * Adding ChargeBee php libraries and configuration files.
 */
require_once(dirname(__FILE__) . "/Config.php");

try{
 
  /*
   * Forming create subscription request parameters to ChargeBee.
   * Note : Here customer object received from client side is sent directly 
   *        to ChargeBee.It is possible as the html form's input names are 
   *        in the format customer[<attribute name>] eg: customer[first_name] 
   *        and hence the $_POST["customer"] returns an associative array of the attributes.	
   */
  $createSubscriptionParam = array("planId" => "monthly",
				   "customer" => $_POST['customer'],
				   "card" => array("number" => $_POST['card_no'],
				                       "expiryMonth" => $_POST['expiry_month'],
						       "expiryYear" => $_POST['expiry_year'],
						       "cvv" => $_POST['cvc'] )
				   );
  
            
              
   $addons = array();
   /*
    * Adding addons to the create subscription request parameters
    */
   $createSubscriptionParam['addons'] = $addons;
   /*
    * Adding addon1 to the addons array, if it is set by user.
    */
   if(isset($_POST['wallposters-quantity']) && $_POST['wallposters-quantity'] != "") {
     $wallPosters =  array("id" => "wall-papers", "quantity" => $_POST['wallposters-quantity']) ;
     array_push($addons, $wallPosters);
   }
               
   /*
    * Adding addon2 to the addons array, if it is set by user.
    */
   if(isset($_POST['ebook']) && $_POST['ebook'] != "" ) {
     $ebook = array("id" => "e-book");
     array_push($addons, $ebook);
   }
   /*
    * Adding coupon to the create subscription request, if it is set by user.
    */
   if( isset($_POST['coupon']) && $_POST['coupon'] != "") {
     $createSubscriptionParam['coupon'] = $_POST['coupon'];
   }
                
   /*
    * Sending request to the ChargeBee.
    */
   $result = ChargeBee_Subscription::create($createSubscriptionParam);
   /*
    * Adds shipping address to the subscription using the subscription Id 
    * returned during create subscription response.
    */
   addShippingAddress($result->subscription()->id, $result->customer());
   /*
    * Forwarding to thank you page.
    */
   $jsonResp = array("forward"=> "thankyou.html");
   print(json_encode($jsonResp,true));
                
} catch(ChargeBee_APIError $e) {
    /*
     * ChargeBee Exception are caught here.
     */
    $jsonError = $e->getJsonObject();
    $status = $jsonError["http_status_code"];
    header('HTTP/1.0 ' . $status . ' Error');
    print(json_encode($jsonError, true));
} catch(Exception $e) {
    /*
     * Other than ChargeBee Exception are caught and handled here.
     */
    $jsonError = array("error_msg"=>"Sorry, There was some problem processing the request. We will get back to you shortly.");
    header("HTTP/1.0 500 Error");
    print json_encode($jsonError,true);
}
/*
 * Add Shipping address using the subscription id returned from 
 * create subscription response.
 */
function addShippingAddress($subscriptionId, $customer) {
    ChargeBee_Address::update(array(
                "label" => "shipping_address",
                "subscriptionId" => $subscriptionId,
                "firstName" => $customer->firstName,
                "lastName" => $customer->lastName,
                "addr" => $_POST['addr'],
                "extended_addr" => $_POST['extended_addr'],
                "city" => $_POST['city'],
                "state" => $_POST['state'],
                "zip" => $_POST['zip_code']
                ));
}
?>
