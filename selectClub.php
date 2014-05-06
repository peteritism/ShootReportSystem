<?php

include ('config.php');
include ('init.php');
include ('include.php');

?>

<!DOCTYPE html>
<html>
<head>

	<title>Station Editor</title>

	<?php
		include 'header.php'; 
	?>

</head>

<body>

	<h1>Select Club</h1>
	<h2>Click club name to go to Registered Shoots</h2>
	<?php
	
		$query = 	'SELECT *
					FROM club';
		$result = dbquery($query);
		while($row = mysqli_fetch_array($result)){
			echo $row['nscaClubId'] . ' <a href=\'selectShoot.php?clubId=' . $row['id'] . '\'>' . $row['clubName'] . '</a>'; 
			echo '<br/>';
		}
	
	?>

</body>

<?php

	include 'footer.php';

?>


<script type="text/javascript">

</html>