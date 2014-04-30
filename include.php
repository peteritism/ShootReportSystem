<?php

include ('init.php');


function dbquery($query) {
	$result = mysql_query($query) or die("<b>Error with MySQL Query:</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	return $result;
}

function dbqueryl($query) {
	$result = mysql_query($query);
	return $result;
}

function queryAssoc($query) {
	$queryResult = dbquery($query);
	$array = array();
	while($row = mysql_fetch_assoc($queryResult)) {
		$array[] = $row;
	}
	return $array;
}

function mysqlQueryToJsonArray($query) {
	$result = dbquery($query);
	$columns = array();
	$rows = array();
	while ($row = mysql_fetch_assoc($result)) {
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
