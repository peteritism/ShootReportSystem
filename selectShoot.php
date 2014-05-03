<?php

include ('config.php');
include ('init.php');
include ('include.php');

$clubId = $_GET['clubId'];

?>

<!DOCTYPE html>
<html>
<head>

	<title>Shoot Selector</title>

	<?php
		include 'header.php'; 
	?>

</head>

<body>

	<h1>Select Shoot</h1>
	<h2>Click Shoot to see Events</h2>
	<?php
	
		$query = 	'SELECT *
					FROM ' . $mysql_database_name . '.registeredshoot
					WHERE clubId =' . $clubId;
		$result = dbquery($query);
		while($row = mysql_fetch_array($result)){
			echo $row['shootDate'] . ' <a href=\'selectEvent.php?shootId=' . $row['id'] . '\'>' . $row['shootName'] . '</a>'; 
			echo '<br/>';
		}
	
	?>

</body>

<?php

	include 'footer.php';

?>


<script type="text/javascript">

</html>