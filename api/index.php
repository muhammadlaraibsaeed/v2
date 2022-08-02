<?php

// Now any request with our api require a validation key if api key is not exist the request will rejected
// with an appropriate response body

declare(strict_types=1);
require __DIR__."/boostrap.php";

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$parts = explode("/", $path);

$resource = $parts[3];

$id = $parts[4] ?? null;

if ($resource != "tasks") {
    
    http_response_code(404);
    exit;
}


 // create object of database class for connnecting with database on server
// following class is way for dependency management which is depend in first database class
//  then taskgateway class after that controller
$database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASS"]);
$user_gateway = new UserGateway($database);

$codec = new JWTCodec($_ENV["SECRET_KEY"]);
$Auth = new Auth($user_gateway,$codec);

if(!$Auth->authenticateAccessToken()){
    exit;
}
$user_id = $Auth->getUserID();
// create object of gateway class 

$task_gateway = new TaskGateway($database);
// this include getconnection() in  task_gateway object and pass object into 
// taskcnotroller contructor    
$controller = new TaskController($task_gateway,$user_id);

$controller->processRequest($_SERVER['REQUEST_METHOD'], $id);



?>





