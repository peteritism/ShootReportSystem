<?php

include ('config.php');
include ('init.php');
include ('include.php');

$clubId = $_GET['clubId'];

?>



<!DOCTYPE html>
<html>
<head>

	<title>Shoot Editor</title>
	<?php
		include 'header.php'; 
	?>
	
</head>

<body>
	<h1>Shoot Editor</h1>
	<h2> List of Registered Shoots for 
	<?php 
	
	$query =	'SELECT clubName
				FROM ' . $mysql_database_name . '.club
				WHERE id=1';
	$result = dbquery($query);
	$row = mysql_fetch_assoc($result);
	echo $row['clubName'];

	?>
	</h2>
	<div class="shootTable"></div>

</body>

<?php

	include 'footer.php';

?>


<script type="text/javascript">

$(function() {

	var eventsTable = new EditableTable({
		db: '<?= $mysql_database_name ?>',
		dbTable: 'registeredshoot',
		columnHeaders: ['id','clubId','shootName','shootDate'],
		uneditableColumns: ['id','clubId'],
		element: $('.shootTable'),
		displayFunction: {
			id: function(id) {
				return $('<a href="eventEditor.php?shootId='+id+'">'+id+'</a>');
			}
		}
	});
	eventsTable.loadTable(0,100,'clubId = <?= $clubId ?>');
	
	$('#all').click(function(){
		$('.erow').trigger('click');
	});

});

</script>

</html>