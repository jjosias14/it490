#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('');//check for the log in script
require_once();//db stuff
require_once();// db stuff

function requestStuff($request)
{
    echo "got request".PHP_EOL;
    //error stuff here
    // try catch stuff
    {
    //end error stuff
    //case statements
    switch ($request['type'])
    {
        case "login":
            return;
        case "sessionValidation":
            return;
        case "registerUser":
            return;
        default:
            return;
    }
    }
    //catch the error here
}
function errorLogging($type,$error)
{

}
$authentication = new  raabitMQServer("serverinfoMQ.ini","authentication");
$authentication->process_requests('requestProcessor');
exit();
?>