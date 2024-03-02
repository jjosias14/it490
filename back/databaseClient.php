#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function dataBaseClient($request)
{
    $client = new rabbitMQClient("serverInfoMQ.ini","DMZ");
    $response = $client->send_request($request);
    return $response;
}
?>
