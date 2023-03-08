<?php

$con = mysqli_connect("localhost", "root", "", "localapps");

if (!$con) 
{
    die("Connection failed: " . mysqli_connect_error());
}

$GLOBALS['db_conn'] = $con;

?>