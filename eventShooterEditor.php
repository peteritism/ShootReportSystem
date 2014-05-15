<?php

include ('config.php');
include ('init.php');
include ('include.php');

$eventId = $_GET['eventId'];

//get quantity of stations from event
	$query =	'SELECT stations
				FROM shootevent
				WHERE id='. $eventId;
	$result = dbquery($query);
	$row = mysqli_fetch_assoc($result);
	$stations = $row['stations'];
	
	if (isset($_POST['nscaConcurrentLady'])){
		$nscaConcurrentLady = '1';
	}else{
		$nscaConcurrentLady = '0';
	}
	if (isset($_POST['hoaOption'])){
		$hoaOption = '1';
	}else{
		$hoaOption = '0';
	}
	if (isset($_POST['hicOption'])){
		$hicOption = '1';
	}else{
		$hicOption = '0';
	}
	if (isset($_POST['lewisOption'])){
		$lewisOption = '1';
	}else{
		$lewisOption = '0';
	}

	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		//add shooter
		$query =	'INSERT INTO `shooter` (`id`, `nscaId`, `firstName`, `lastName`, `suffix`, `address`, `state`, `nscaClass`, `nscaConcurrent`, `nscaConcurrentLady`) 
					VALUES (NULL, \''. $_POST['nscaId'] .'\', \''. $_POST['firstName'] .'\', \''. $_POST['lastName'] .'\',\''. $_POST['suffix'] .'\', \''. $_POST['address'] .'\', \''. $_POST['state'] .'\', \''. $_POST['nscaClass'] .'\', \''. $_POST['nscaConcurrent'] .'\', \''. $nscaConcurrentLady .'\')';
		dbquery($query);
		
		//get new shooterId
		//cannot search where nscaid = $_POST[nscaId] because NSCA ID may not exist
		$query = 	'SELECT id
					FROM shooter
					ORDER BY id DESC
					LIMIT 1';
		$result= dbquery($query);
		$row = mysqli_fetch_assoc($result);
		$shooterId = $row['id'];
		
		//add event shooter
		$query =	'INSERT INTO `eventshooter` (`id`, `shootEventId`, `shooterId`, `hoaOption`, `hicOption`, `lewisOption`, `class`, `concurrent`) VALUES (NULL, \''. $eventId .'\', \''. $shooterId .'\', \''. $hoaOption .'\', \''. $hicOption .'\', \''. $lewisOption .'\', \''. $_POST['nscaClass'] .'\', \''. $_POST['nscaConcurrent'] .'\')';
		dbquery($query);

	}
?>

<!DOCTYPE html>
<html>
<head>

	<title>Event Shooters Editor</title>
	<?php
		include 'header.php'; 
	?>
	<style>
		td{
			padding: 1px 5px;
			text-align:center;
		}
		td.firstName{
			text-align:right;
		}
		td.lastName{
			text-align:left;
		}
		.altLines tr:nth-child(odd){
			background: #EEE;
		}
	</style>
</head>

<body>
	<h1>Event Shooters Editor</h1>
	<?php
	//put if event exists statement here
	
		echo '<h2> Editing Shooters in the ';
		
		$query =	'SELECT eventType
					FROM shootevent
					WHERE id='. $eventId;
		$result = dbquery($query);
		$row = mysqli_fetch_assoc($result);
		$eventType = $row['eventType'];
		echo $eventType;

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

	<table>
		<tr class='columnNames'>
			<td>NSCA#</td>
			<td>First</td>
			<td>Last</td>
			<td>Suffix</td>
			<td>Postal Address</td>
			<td>State</td>
			<td>Class</td>
			<td>Concurrent</td>
			<td>Lady</td>
			<td>HOA</td>
			<td>HIC</td>
			<td>Lewis</td>
			<td></td>
		</tr>
		<tr><form method='post' action='eventShooterEditor.php?eventId=<?= $eventId ?>'>
			<td><input type='text' size='5' name='nscaId' autofocus='autofocus'></td>
			<td><input type='text' size='10' name='firstName'></td>
			<td><input type='text' size='10' name='lastName'></td>
			<td><input type='text' size='1' name='suffix'></td>
			<td><textarea rows='2' cols='25' name='address' id='enterAddress'></textarea></td>
			<td><input type='text' size='1' name='state' id='enterState'></td>
			<td><select name='nscaClass'>
				<option value='H'>H</option>
				<option value='E' selected='selected'>E</option>
				<option value='D'>D</option>
				<option value='C'>C</option>
				<option value='B'>B</option>
				<option value='A'>A</option>
				<option value='AA'>AA</option>
				<option value='M'>M</option>
			</select></td>
			<td><select name='nscaConcurrent'>
				<option value='' selected='selected'></option>
				<option value='SJ'>SJ</option>
				<option value='JR'>JR</option>
				<option value='VT'>VT</option>
				<option value='SV'>SV</option>
				<option value='SSV'>SSV</option>
			</select></td>
			<td><input type='checkbox' name='nscaConcurrentLady'></td>
			<td><input type='checkbox' name='hoaOption'></td>
			<td><input type='checkbox' name='hicOption'></td>
			<td><input type='checkbox' name='lewisOption'></td>
			<td><input type='submit'></td>
		</form></tr>
	</table>
	
	<?php

	echo $eventType . ' Event Shooters - ';
	$query =	'SELECT COUNT(*)
				AS numberOfShooters
				FROM eventshooter
				WHERE shooteventid =' . $eventId;
	$result = dbquery($query);
	$row = mysqli_fetch_assoc($result);
	echo $row['numberOfShooters'] . ' Shooters </br>';
	$query =	'SELECT *
				FROM shooter
				JOIN eventshooter
				ON eventshooter.shooterId  = shooter.id
				WHERE eventshooter.shootEventId =' . $eventId . 
				' ORDER BY shooter.lastName ASC';
	$result = dbquery($query);
	echo '<table class=\'altLines\' border=\'1\'><thead><td>NSCA ID</td><td></td><td></td><td></td><td></td><td>Score</td><td>HOA</td><td>HIC</td><td>Lewis</td><td></td><td></td></thead>';
	while ($row = mysqli_fetch_array($result)){
		echo '<tr>';
		echo '<td class=\'nscaId\'>' . $row['nscaId'] . '</td>';
		echo '<td class=\'firstName\'>' . $row['firstName'] . '</td>';
		echo '<td class=\'lastName\'>' . $row['lastName'] . ' ' .  $row['suffix'] . '</td>';
		echo '<td class=\'class\'>' . $row['class'] . '</td>';
		echo '<td class=\'concurrent\'>';
		echo $row['concurrent'];
		if (!empty($row['concurrent']) && $row['nscaConcurrentLady'] == 1){
			echo ' & LY';
		}else if (empty($row['concurrent']) && $row['nscaConcurrentLady'] == 1){
			echo 'LY';
		}
		echo'</td>';
		//get score
		$query2 = 	'SELECT SUM(targetsBroken)
					AS totalScore
					FROM shootereventstationscore
					WHERE eventShooterId=' . $row['id'];
		$result2 = dbquery($query2);
		$row2 = mysqli_fetch_assoc($result2);
		echo '<td class=\'score\'>' . $row2['totalScore'] . '</td>';
		echo '<td class=\'hoaOption\'><input type=\'checkbox\' ';
		if($row['hoaOption'] == 1){
			echo 'checked=\'checked\' ';
		}
		echo 'disabled=\'disabled\'></td>';
		
		echo '<td class=\'hicOption\'><input type=\'checkbox\' ';
		if($row['hicOption'] == 1){
			echo 'checked=\'checked\' ';
		}
		echo 'disabled=\'disabled\'></td>';
		
		echo '<td class=\'lewisOption\'><input type=\'checkbox\' ';
		if($row['lewisOption'] == 1){
			echo 'checked=\'checked\' ';
		}
		echo 'disabled=\'disabled\'></td>';
		
		echo '<td><button disabled=\'disabled\'>Edit</button></td>';
		echo 	'<td>
					<form method=\'get\' action=\'scoreEditor.php?\' >
						<input type=\'hidden\' name=\'eventId\' value=\'' . $eventId . '\'>
						<input type=\'hidden\' name=\'eventShooterId\' value=\'' . $row['id'] . '\'>
						<input type=\'submit\' value=\'Edit Scores\' >
					</form>
				</td>';
	}
	echo '</table>';
	
	
	?>

	<!--<div class="eventShooterTable"></div>-->

	<br/>
	<br/>
</body>

<?php

	include 'footer.php';

?>


<script type="text/javascript">

$(document).ready(function(){
	
	function getState(){
		var address = $('#enterAddress').val();
		var splitAddress = address.split(' ');
		var state = splitAddress[splitAddress.length -2];
		$('#enterState').val(state);
	};

	$('#enterAddress').change(function(){
		getState();
	});

});

</script>

</html>