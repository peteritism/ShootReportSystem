<?php

include ('config.php');
include ('init.php');
include ('include.php');

$eventId = $_GET['eventId'];
$eventShooterId = $_GET['eventShooterId'];

//get key:value (id:targetsBroken) from POST and UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	$keys = array_keys($_POST);
	foreach ($keys as $value){
		$query = 	'UPDATE shootereventstationscore
					SET targetsBroken =' . $_POST[$value] . '
					WHERE id=' . $value;
		dbquery($query);
	}
	$url = 'Location:eventShooterEditor.php?eventId=' . $eventId;
	header($url);
	exit;
}

//get quantity of stations from event
$query =	'SELECT stations
			FROM shootevent
			WHERE id='. $eventId;
$result = dbquery($query);
$row = mysqli_fetch_assoc($result);
$stations = $row['stations'];
$i = 1;
//add rows for scores
//eventShooterId and eventId must be unique so repeated values are rejected
while ($i <= $stations){
	//get individual station id
	$query = 	'SELECT id
				FROM eventstation
				WHERE shootEventid=' . $eventId . '
				AND stationNumber=' . $i;
	$result = dbquery($query);
	$row = mysqli_fetch_assoc($result);
	//insert individual station id with event shooter id into scores table
	$query = 'INSERT INTO shootereventstationscore (`id`,`eventShooterId`,`eventStationId`) VALUES (NULL,\'' . $eventShooterId . '\',\'' . $row['id'] . '\')';
	dbqueryl($query);
	$i++;
}

?>

<!DOCTYPE html>
<html>
<head>

	<title>Score Editor</title>
	<?php
		include 'header.php'; 
	?>
	<style>
		td{
			padding: 1px 5px;
			text-align:center;
		}
	</style>
</head>

<body>
	<h1>Score</h1>
	<?php
	//put if event exists statement here
	
	//editing scores for NAME in the EVENTTYPE of the SHOOTNAME registered shoot
	
		echo '<h2> Editing Shooters in the ';
		
		$query =	'SELECT eventType
					FROM shootevent
					WHERE id='. $eventId;
		$result = dbquery($query);
		$row = mysqli_fetch_assoc($result);
		echo $row['eventType'];

		echo ' Event of the ';
		
		$query = 	'SELECT registeredshoot.shootName
					FROM registeredshoot
					JOIN shootevent
					ON registeredshoot.id=shootevent.shootId
					WHERE shootevent.id = ' . $eventId;
		$result = dbquery($query);
		$row = mysqli_fetch_array($result);
		echo $row['shootName'];
		
		echo ' Registered Shoot</h2>';
	?>

	
	<?php

	echo '<table border=\'1\'><thead>';
	$j = 1;
	while ($j <= $stations){
		echo '<td>' . $j . '</td>';
		$j++;	}
	echo '<td>Total</td></thead>';
	$query =	'SELECT id,targetsBroken
				FROM shootereventstationscore
				WHERE eventShooterId =' . $eventShooterId;
	$result = dbquery($query);
	$m = 1;
	echo '<tr><form method=\'post\' action=\'scoreEditor.php?eventId=' . $eventId . '&eventShooterId=' . $eventShooterId . '\' >';
	while ($row = mysqli_fetch_array($result)){
		echo '<td><input type=\'text\' size=\'1\' class=\'station' . $m . '\' name=\'' . $row['id'] . '\' value=\'' . $row['targetsBroken'] . '\'></td>';
		$m++;
	}
	echo '<td id=\'totalScore\'></td>';
	echo '<td><input type=\'submit\' value=\'Save Scores\'></td></form></tr>';
	echo '</table>';
	
	
	?>

</body>

<?php

	include 'footer.php';

?>

<script>
	//total score on the fly
	function calculateScore () {
		var score = parseInt(0);
		$('input[type="text"]').each(function(k,v){
			var val = $(v).val();
			score += parseInt(val);
		});
		$('#totalScore').html(score);
	};

	$( document ).ready(function() {
		//check score on page load
		calculateScore();
		//check score after data entered
		$('input[type="text"]').change(function(){
			calculateScore();
		});
	});

</script>
</html>