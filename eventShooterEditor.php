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
		$query = 	'SELECT id
					FROM shooter
					WHERE nscaId=' . $_POST['nscaId'];
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
			<td><input type='text' size='5' name='nscaId'></td>
			<td><input type='text' size='10' name='firstName'></td>
			<td><input type='text' size='10' name='lastName'></td>
			<td><input type='text' size='1' name='suffix'></td>
			<td><textarea rows='2' cols='25' name='address'></textarea></td>
			<td><input type='text' size='1' name='state'></td>
			<td><select name='nscaClass'>
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

	echo 'Event Shooters <br/>';
	$query =	'SELECT *
				FROM eventshooter
				JOIN shooter
				ON eventshooter.shooterId  = shooter.id
				WHERE eventshooter.shootEventId =' . $eventId . 
				' ORDER BY shooter.lastName ASC';
	$result = dbquery($query);
	echo '<table border=\'1\'><thead><td>NSCA ID</td><td></td><td></td><td></td><td></td><td>Score</td><td>HOA</td><td>HIC</td><td>Lewis</td><td></td><td></td></thead>';
	while ($row = mysqli_fetch_array($result)){
		echo '<tr>';
		echo '<td class=\'nscaId\'>' . $row['nscaId'] . '</td>';
		echo '<td class=\'firstName\'>' . $row['firstName'] . '</td>';
		echo '<td class=\'lasttName\'>' . $row['lastName'] . '</td>';
		echo '<td class=\'class\'>' . $row['class'] . '</td>';
		echo '<td class=\'concurrent\'>' . $row['concurrent'] . '</td>';
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


$(function() {
	var eventsTable = new EditableTable({
		db: '<?= $mysql_database_name ?>',
		dbTable: 'eventshooter',
		columnHeaders: ['ID','ShootEvent ID','Shooter ID','HOA','HIC','Lewis','class','concurrent'],
		uneditableColumns: ['id','shootEventId','shooterId'],
		element: $('.eventShooterTable'),

	});
	eventsTable.loadTable(0,100,'shootEventId = <?= $eventId ?>');
	$('#all').click(function(){
		$('.erow').trigger('click');
	});

});

</script>

</html>