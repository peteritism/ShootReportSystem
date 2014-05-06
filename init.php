<?php

include('config.php');

$link = mysqli_connect($mysql_server_address,$mysql_server_username,$mysql_server_password) or die("<h1>Coud not connect to MYSQL server. Please ensure server details in config.php are correct.</h1>");
mysqli_select_db($link, $mysql_database_name) or die ("<h1>Could not select database " . $mysql_database_name . ". Please ensure that a database is defined in config.php and that the database exists.");

?>