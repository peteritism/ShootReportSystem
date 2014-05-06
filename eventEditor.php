<?php

include ('config.php');
include ('init.php');
include ('include.php');

$shootId = $_GET['shootId'];


?>

<!DOCTYPE html>
<html>
<head>

	<title>Event Editor</title>
	<?php
		include 'header.php'; 
	?>
	<style type="text/css">
		tr {
			height:30px;
		}
		tr.erow td, tr.nrow td {
			width:100px;
			max-width:150px;
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
		.id, .shootId, .registrationCost, .hicCost, .hoaCost, .lewisCost, .lewisGroups, .stations, .targets{
			width:50px !important;
		}
		.eventType {
			width:80px !important;
		}
	</style>
</head>

<body>
	<h1>Event Editor</h1>
	<h2> Events in the 
	<?php 
	
	$query =	'SELECT shootName
				FROM registeredshoot
				WHERE id=1';
	$result = dbquery($query);
	$row = mysqli_fetch_assoc($result);
	echo $row['shootName'];

	?>
	Registered Shoot</h2>
	<div class="eventTable"></div>

</body>

<?php

	include 'footer.php';

?>


<script type="text/javascript">

$(function() {

	var eventsTable = new EditableTable({
		db: '<?= $mysql_database_name ?>',
		dbTable: 'shootevent',
		columnHeaders: ['ID','Shoot ID','eventType','Reg$','HIC$','HOA$','Lewis$','Lewis Groups','Stations','Targets'],
		uneditableColumns: ['id','shootId'],
		element: $('.eventTable'),
		displayFunction: {
			id: function(id) {
				return $('<a href="stationEditor.php?eventId='+id+'">'+id+'</a>');
			}
		}
	});
	eventsTable.loadTable(0,100,'shootId = <?= $shootId ?>');
	
	$('#all').click(function(){
		$('.erow').trigger('click');
	});

});

</script>

</html>