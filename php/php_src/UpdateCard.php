<?php
/*
 * Adding ChargeBee php libraries and configuration files.
 */
require_once(dirname(__FILE__) . "/Config.php");

try {
   $uri = $_SERVER["REQUEST_URI"];

   if(endsWith(substr($uri,0,strpos($uri,"?")) , "/update")) {
       updateCardHostedPage();     
   } else if(endsWith(substr($uri,0,strpos($uri,"?")),"/redirect_handler")) {
       redirectFromChargeBee();
   } else {
       header("HTTP/1.0 400 Error");
       include($_SERVER["DOCUMENT_ROOT"]."/error_pages/400.html");
   }
} catch(Exception $e) {
   header("HTTP/1.0 500 Error");
   include($_SERVER["DOCUMENT_ROOT"]."/error_pages/500.html");
}


/*
 * Redirects the customer to ChargeBee Update Card Hosted Page API.
 */
function updateCardHostedPage() {
   /*
    * Calling the ChargeBee Update Card Hosted Page API to update card for 
    * a customer by passing the particular customers' customer id.
    * 
    * Note : To use this API return url for Update Card API's page must be set.
    */
   
   $result = ChargeBee_HostedPage::updateCard(array(
  				"customer"=>array( "id"=>$_GET['customer_id'] ), 
                                "embed"=>"false" ));
   
   
   $url = $result->hostedPage()->url;
   header("Location: $url");
   
}

/*
 * Handles the redirection from ChargeBee server.
 */
function redirectFromChargeBee(){
   /* The request will have hosted page id and state of the customer card
    * update status.
    */
   
   if("succeeded" == $_GET['state'] ) {
     /* Request the ChargeBee server about the Update Card Hosted Page state 
      * and provides details about the customer.
      */
     $result = ChargeBee_HostedPage::retrieve($_GET['id']);
    
     
     $customerId = $result->hostedPage()->content()->customer()->id;
     $queryParameters = "customer_id=" . urlencode($customerId) . "&updated=" . urlencode("true");
     header("Location: profile?".$queryParameters);
     
   }
   else {
     header("HTTP/1.0 400 Error");
     include($_SERVER["DOCUMENT_ROOT"]."/error_pages/400.html");
   }

}

?>
