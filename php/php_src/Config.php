<?php
require_once(dirname(__FILE__) . "/ErrorHandler.php");
/*
 * Adding ChargeBee php libraries
 */

require_once(dirname(__FILE__) . "/lib/ChargeBee.php");
/* 
 * Sets the environment for calling the Chargebee API.
 * You need to sign up at ChargeBee app to get this credential.
 * It is better if you fetch configuration from the environment properties instead of hard coding it
 * in code.
 */

ChargeBee_Environment::configure("<your-site>","<your-api-key>");


function endsWith($origStr, $suffix)
{
    return substr($origStr, -strlen($suffix)) === $suffix;
}

?>
