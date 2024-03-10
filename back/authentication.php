#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');//check for the log in script
require_once('login.php.inc');
require_once('dataBaseconnect.php');//db stuff
require_once('databaseFunctions.php');// db stuff

function requestStuff($request)
{
    echo "got request".PHP_EOL;
    $errorClient = new rabbitMQClient("serverInfoMQ.ini","Errors");
    // try catch stuff
   try {
       var_dump($request);
       if (!isset($request['type'])) {
           return "Unsupported message type";
       }
       switch ($request['type']) {
           case "login":
               return doLoginto($request['email'], $request['password']);
           case "sessionValidation":
               return validateSession($request['sessionID']);
           case "registerUser":
               return registerUser($request['f_name'], $request['l_name'], $request['email'] . $request['password']);
           default:
               return errorLogging($request['type'], $request['error']);
       }
   }

   catch(Exception $e)
       {
           $errorClient->send_request(['type' => 'DBErrors', 'error' => $e->getMessage()]);
       }
    return array("returnCode" => '0', 'message' => "Server got the request and processed");


function errorLogging($type, $error)
{
    $file_data = $error;
    $file_data .= file_get_contents($type.'txt');
    file_put_contents($type.'txt',$file_data);
    return json_encode(["message" => "Error: "]);
}
}
$authentication = new  rabbitMQServer("serverInfoMQ.ini","authentication");
$authentication->process_requests('requestProcessor');
exit();
