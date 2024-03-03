#!/usr/bin/php
<?php
//will load required libraries and configurations
require_once ('path.inc');
require_once ('get_host_info.inc');
require_once ('rabbitMQLib.inc');
require_once ('login.php.inc');
require_once ('dataBaseconnect.php');
require_once ('databaseFunctions.php');


function requestProcessor($request) {
    echo "Processing request at"  .date('m-d-y H:i:s') . " -Type:" .$request['type'] .PHP_EOL;
    var_dump($request);

    $errorClient = new rabbitmqClinet ("path_to_error_logging_config.ini", "ErrorLoggingQueue"); //  placeholder


try { 
    if (!isset($request['type'])){
        throw new Exception("unsupported message type"); //Throws an exception if request type is missing (Sad)

    }

    $sessionValidationResult = valiadateSession($request['SessionID']);
    if(!sessionValidationResult) {
        throw new Exception ('whomp whomp Invalid Session'); //Throws and exception if session validation fails 
    }

    return handleRequestsType($request); // Process the request based on it's type
} catch (Exception $e) {
    logError($errorClient, 'DBerrors', $e->getMessage()); 
}
 }




function logError($client, $type, $errorMessage) { 
    // Sends error to RabbitMQ Error Logging Queue
    $client->send_request(['type' => $type, 'error' => $errorMessage]); 

    // Save the error locally
    error_log(date('Y-m-d H:i:s') . " - Error: " . $errorMessage . PHP_EOL, 3, $type . '_error_log.txt'); // Logs error with timestamp locally
}


function validateSession($sessionID) {
    //place holder
    return $sessionID ? true : false; 
}

//Setup and start the Rabbitmw server listener
$serverConfig = "path_to_server_config.ini";  //placeholder for path to our RabbitMQ
$queueName = "appServerQueue"; // Placeholder for rabbitmq queue for our app server

$appServer = new rabbitMQServer($serverConfig, $queueName);
$appServer->process_requests('requestProcessor'); // Starts processing incoming requests using the defined function
exit();