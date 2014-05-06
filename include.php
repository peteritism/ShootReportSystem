<?php

include ('init.php');


function dbquery($query) {
	$result = mysqli_query($GLOBALS['link'], $query) or die("<b>Error with MySQL Query:</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysqli_errno() . ") " . mysqli_error());
	return $result;
}

function dbqueryl($query) {
	$result = mysqli_query($GLOBALS['link'], $query);
	return $result;
}

function queryAssoc($query) {
	$queryResult = dbquery($query);
	$array = array();
	while($row = mysqli_fetch_assoc($queryResult)) {
		$array[] = $row;
	}
	return $array;
}

function mysqlQueryToJsonArray($query) {
	$result = dbquery($query);
	$columns = array();
	$rows = array();
	while ($row = mysqli_fetch_assoc($result)) {
		$columns = array();
		$dataRow = array();
		foreach ($row as $key => $value) {
			$columns[] = $key;
			$dataRow[] = $value;
		}
		$rows[] = $dataRow;
	}
	$data['rows'] = $rows;
	$data['columns'] = $columns;
	return json_encode($data);
}

?>
