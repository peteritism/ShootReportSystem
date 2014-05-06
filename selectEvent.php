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
		while($row = mysqli_fetch_array($result)){
			echo '<a href=\'eventShooterEditor.php?eventId=' . $row['id'] . '\'>' . $row['eventType'] . '</a>' . ' - ' . $row['targets'] . ' Targets'; 
			echo '<br/>';
		}
	
	?>

</body>

<?php

	include 'footer.php';

?>


<script type="text/javascript">

</html>