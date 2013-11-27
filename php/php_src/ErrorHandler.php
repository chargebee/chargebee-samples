<?php
/* This method is used to handle error and display custom error page.
 */
function customError($e){
  include($_SERVER["DOCUMENT_ROOT"]."/error_pages/500.html");
        header("HTTP/1.0 500 Internal Server Error");
        throw new Exception($e);
        die();
}
?>
