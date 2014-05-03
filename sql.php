<?php

include('init.php');
include('include.php');

mysql_select_db($_GET["db"]);

$query = $_GET['sql'];
//temporary fix to allow NULL to be entered
//NULL is eliminated as soon as a record is edited
$query = str_replace('\'NULL\'','NULL',$query);

$firstWord = explode(' ',$query);

if($firstWord[0] == 'SELECT') {
	echo mysqlQueryToJsonArray($query);
} else if ($firstWord[0] == 'INSERT') {
	dbquery($query);
	$id['id'] = mysql_insert_id();
	echo json_encode($id);
} else {
	dbquery($query);
}

?>