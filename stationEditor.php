<?php

include ('config.php');
include ('init.php');
include ('include.php');

$eventId = $_GET['eventId'];

//get quantity of stations from event
	$query =	'SELECT stations
				FROM ' . $mysql_database_name . '.shootevent
				WHERE id='. $eventId;
	$result = dbquery($query);
	$row = mysql_fetch_assoc($result);
	$stations = $row['stations'];
	$i = 1;
//generate that amount of stations each time the page is loaded
//shootevent+stationNumber are UNIQUE, so repeated INSERTs are ignored
	while ($i <= $stations){
		$query = 'INSERT INTO `' . $mysql_database_name . '`.`eventstation`
		(`id`, `shootEventId`, `stationNumber`, `maxScore`, `tieBreakerPosition`, `stationDetail`)
		VALUES (NULL, \''. $eventId .'\', \''. $i .'\', NULL, NULL, NULL);';
		
		dbqueryl($query);
		$i++;
	};
?>

<!DOCTYPE html>
<html>
<head>

	<title>Station Editor</title>
	<?php
		include 'header.php'; 
	?>
	<style type="text/css">
		tr {
			height:30px;
		}
		tr.erow td, tr.nrow td {
			width:100px;
			text-overflow: ellipsis;
			white-space: nowrap;
			overflow:hidden;
		}
		td.editing input {
			width: inherit;
		}
		td.action, th.action {
			width:120px;
		}
		th {
			text-align: left;
		}
		.id, .shootEventId{
			width:50px !important;

		}
		
		.stationNumber, .maxScore, .tieBreakerPosition{
			width:90px !important;
		}
		.stationDetail {
			width:150px !important;
		}
	</style>
</head>

<body>
	<h1>Station Editor</h1>
	<h2> Editing Stations in the 
	<?php 
	
	$query =	'SELECT eventType
				FROM ' . $mysql_database_name . '.shootevent
				WHERE id='. $eventId;
	$result = dbquery($query);
	$row = mysql_fetch_assoc($result);
	echo $row['eventType'];

	?>
	Event of the 
	
	<?php
		//code here to get shootName
		echo 'put some code here'
	?>
	
	Registered Shoot</h2>
	<div class="stationTable"></div>

</body>

<?php

	include 'footer.php';

?>


<script type="text/javascript">


$(function() {
	var eventsTable = new EditableTable({
		db: '<?= $mysql_database_name ?>',
		dbTable: 'eventstation',
		columnHeaders: ['ID','Event ID','Station No.','Max Score','Tiebreaker','Station Details'],
		uneditableColumns: ['id','shootEventId'],
		element: $('.stationTable'),

	});
	eventsTable.loadTable(0,100,'shootEventId = <?= $eventId ?>');
	$('#all').click(function(){
		$('.erow').trigger('click');
	});

});

</script>

</html>