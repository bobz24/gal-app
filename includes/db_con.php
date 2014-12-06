<?php
$dbhost = 'localhost';
$dbname = 'galdb';
$dbuser = 'root';
$dbpass = '';
$db_con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
//Evaluate db connection
if(mysqli_connect_errno())
{
    echo "Could not connect to database...";
    echo mysqli_connect_error();
    exit();
}
//else echo "Connected to database";
?>