#!/usr/bin/env php
>?php

//will load required libraries and configurations

require_once 'path_to_config.inc'; //placeholder
require_once 'host_information.inc'; //placeholder
require_once 'rabbitmq_lib.inc'; //placeholder
require_once 'authentication_handler.inc' //placeholder


require_once 'database_conncection.inc'; // datebase connection and funtcions //placeholder
require_once 'database_function.inc'; //placeholder


function requestProcessor($request) {
    echo "Processing request at"  .date('m-d-y H:i:s') . " -Type:" .$request['type'] .PHP_EOL;
    var_dump($request);

    $errorClient = new rabbitmqClinet 


try { 
    if (!isset($request['type'])){
        throw new Exception(""); //Throws an exception if request type is missing (Sad)

    }

    $sessionValidationResult = valiadateSession($request['SessionID']);
    if(!sessionValidationResult) {
        throw new Exception ('whomp whomp Invalid Session'); //Throws and exception if session validation fails 
    }

    return handleRequestsType($request) // Process the request based on it's type 
    
 }

}

function logError($client,$type,$erroeMessage){
    //Sends error to RabbitMQ Error Logging Queue 
    $client-> send_request(['type => $type,''error  =>$errorMessage']); 

    //save the error locally 
    error_log(date('Y-m-d H:i:s') . " - Error: " . $errorMessage . PHP_EOL, 3, $type . '_error_log.txt'); // Logs error with timestamp locally

}

function validateSession($sessionID) {
    //place holder
    return $sessionID ? true : false; 
}

//Setup and start the Rabbitmw server listener
$serverConfig = "path_to_server.ini;" //placeholder for path to our RabbitMQ
$queueName = "appServerQueue"; // Placeholder for rabbitmq queue for our app server

$appServer = new rabbitMQServer($serverConfig, $queueName);
$appServer->process_requests('requestProcessor'); // Starts processing incoming requests using the defined function
exit();