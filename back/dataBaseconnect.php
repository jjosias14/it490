#!/usr/bin/php
<?php
function connectDB()
{
    $hostname = 'localhost';
    $user = 'joshua@localhost';
    $password = '12345';
    $database = 'IT490DB';

    $mydb = new mysqli($hostname,$user,$password,$database);

    if ($mydb->errno !=0){
        echo "Failed to make a connection to database: ". $mydb->error.PHP_EOL;
        exit(0);
    }
    echo "Success on connection".PHP_EOL;
    return $mydb;
}
?>