<?php 

require dirname(__DIR__) . "/vendor/autoload.php";
set_error_handler("ErrorHandler::handleError");


set_exception_handler("ErrorHandler::handleException");

// for .env file which is used for set e
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
 
header("Content-type: application/json; charset=UTF-8");

?>