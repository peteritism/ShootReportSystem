<?php

include ('config.php');
include ('init.php');
include ('include.php');

$shootId = $_GET['shootId'];

?>

<!DOCTYPE html>
<html>
<head>

	<title>Event Selector</title>

	<?php
		include 'header.php'; 
	?>

</head>

<body>

	<h1>Select Event</h1>
	<h2>Click Event to add/edit Shooters</h2>
	<?php
	
		$query = 	'SELECT *
					FROM shootevent
					WHERE shootId =' . $shootId;
		$result = dbquery($query);
		echo '<table><tr>';
		while($row = mysqli_fetch_array($result)){
			echo '<td>' . $row['eventType'] . '</td>';
			echo '<td> - ' . $row['targets'] . ' Targets</td>';
			echo '<td><form method=\'get\' action=\'eventShooterEditor.php\' ><input type=\'hidden\' name=\'eventId\' value=\'' . $row['id'] . '\'><input type=\'submit\' value=\'Edit Shooter/Scores\'></form></td>';
			echo '<td><form method=\'get\' action=\'eventReport.php\' ><input type=\'hidden\' name=\'eventId\' value=\'' . $row['id'] . '\'><input type=\'submit\' value=\'Public Report\'></form></td>';
			echo '<td><form method=\'get\' action=\'eventReport.php?\' ><input type=\'hidden\' name=\'eventId\' value=\'' . $row['id'] . '\'><input type=\'hidden\' name=\'private\' value=\'private\'><input type=\'submit\' value=\'Private Report\'></form></td>';
		}
		echo '</tr></table>';
	
	?>

</body>

<?php

	include 'footer.php';

?>


<script type="text/javascript">

</html>