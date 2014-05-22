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
	

?>

<!DOCTYPE html>
<html>
<head>

	<title>Event Report</title>
	<?php
		include 'header.php'; 
	?>
	<style>
		td{
			padding: 0px 3px;
			text-align:center;
		}
		td.firstName{
			text-align:right;
		}
		td.lastName{
			text-align:left;
		}
		tr:nth-child(even) {background: #DDD}
	</style>
</head>

<body>
	<h1>Event Report</h1>
	<?php
	//put if event exists statement here
	
		/*echo '<h2> Editing Shooters in the ';
		
		$query =	'SELECT eventType
					FROM shootevent
					WHERE id='. $eventId;
		$result = dbquery($query);
		$row = mysqli_fetch_assoc($result);
		$eventType = $row['eventType'];
		echo $eventType;

		echo ' Event of the ';
		*/
		$query = 	'SELECT shootevent.eventType, registeredshoot.nscaShootId, registeredshoot.shootName, club.clubName
					FROM registeredshoot
					JOIN shootevent
					ON registeredshoot.id=shootevent.shootId
					JOIN club
					ON registeredshoot.clubId=club.id
					WHERE shootevent.id = ' . $eventId;
		$result = dbquery($query);
		$row = mysqli_fetch_array($result);
		$clubName = $row['clubName'];
		$shootName = $row['shootName'];
		$eventType = $row['eventType'];
		echo $clubName . '</br>';
		echo $shootName;
		if($row['nscaShootId']){
			echo ' - ' . $row['nscaShootId'] . '</br>';
		}else{
			echo '</br>';
		}
		echo $eventType . ' Event </br>';
		
	?>


	
	<?php
	
	//total event Shooters
	$query =	'SELECT COUNT(*)
				AS numberOfShooters
				FROM eventshooter
				WHERE shooteventid =' . $eventId;
	$result = dbquery($query);
	$row = mysqli_fetch_assoc($result);
	$totalShooters = $row['numberOfShooters'] . ' Shooters </br>';
	
	$query =	'SELECT *
				FROM shooter
				JOIN eventshooter
				ON eventshooter.shooterId  = shooter.id
				WHERE eventshooter.shootEventId =' . $eventId . 
				' ORDER BY shooter.lastName ASC';
	$result = dbquery($query);
	//table all BY LASTNAME ASC
	echo '<table border=\'1\'><thead><td>NSCA ID</td><td></td><td></td><td></td><td>Score</td></thead>';
	while ($row = mysqli_fetch_array($result)){
		echo '<tr>';
		echo '<td class=\'nscaId\'>' . $row['nscaId'] . '</td>';
		echo '<td class=\'firstName\'>' . $row['firstName'] . '</td>';
		echo '<td class=\'lastName\'>' . $row['lastName'] . ' ' .  $row['suffix'] . '</td>';
		echo '<td class=\'class\'>' . $row['class'] . '</td>';
		echo'</td>';
		//get score
		$query2 = 	'SELECT SUM(targetsBroken)
					AS totalScore
					FROM shootereventstationscore
					WHERE eventShooterId=' . $row['id'];
		$result2 = dbquery($query2);
		$row2 = mysqli_fetch_assoc($result2);
		echo '<td class=\'score\'>' . $row2['totalScore'] . '</td>';
	}
	echo '</table>';
	
	function makeClassTable ($eventId,$class){
		//number of shooter in class
		$query = 	'SELECT COUNT(*)
					AS numberOfShooters
					FROM shooter
					JOIN eventshooter
					ON eventshooter.shooterId  = shooter.id
					WHERE eventshooter.class=\'' . $class . '\'
					AND eventshooter.shootEventId =' . $eventId;
		$result = dbquery($query);
		$row = mysqli_fetch_assoc($result);
		$shooterCount = $row['numberOfShooters'];
		
		//get shooters in class
		$query =	'SELECT *
					FROM shooter
					JOIN eventshooter
					ON eventshooter.shooterId  = shooter.id
					WHERE eventshooter.class=\'' . $class . '\'
					AND eventshooter.shootEventId =' . $eventId;
		$result = dbquery($query);
		$tableData = array();
		$i = 0;
		while ($row = mysqli_fetch_array($result)){
			$tableData[$i]['firstName'] = $row['firstName'];
			$tableData[$i]['lastName'] = $row['lastName'] . ' ' . $row['suffix'];
			$tableData[$i]['nscaId'] = $row['nscaId']; 		//merged into nsca report
			$tableData[$i]['state'] = $row['state'];		//merged into nsca report
			$tableData[$i]['shooterId'] = $row['shooterId'];//merged into nsca report
			//get scores
			$query2 = 	'SELECT SUM(targetsBroken)
						AS totalScore
						FROM shootereventstationscore
						WHERE eventShooterId=' . $row['id'];
			$result2 = dbquery($query2);
			$row2 = mysqli_fetch_assoc($result2);
			$tableData[$i]['score'] = $row2['totalScore'];
			$i++;
		}
		//sort by scores DESC
		foreach ($tableData as $val){
			$tmp[] = $val['score'];
		}
		array_multisort($tmp, SORT_DESC, $tableData);
		//put top six scores in an array
		$scoreList = array();
		$last = 101;	//higher than any possible score
		foreach ($tableData as $val){
			//need six for shoots with more than 45 shooters per class
			$current = $val['score'];
			if ($current < $last){
				$scoreList[] = $current;
				$last = $current;
			}
		}
		//determine punches/award based on amount of shooters
		if ($shooterCount > 0 && $shooterCount <= 2){
			$punches = array();
		}
		if ($shooterCount >= 3 && $shooterCount <= 9){
			$punches = array(1);
		}
		if ($shooterCount >= 10 && $shooterCount <= 14){
			$punches = array(2,1);
		}
		if ($shooterCount >= 15 && $shooterCount <= 29){
			$punches = array(4,2,1);
		}
		if ($shooterCount >= 30 && $shooterCount <= 44){
			$punches = array(4,4,2,1);
		}
		if ($shooterCount >= 45){
			$punches = array(4,4,4,3,2,1);
		}
		$i = 0; //location in punches and scoreList
		$j = 0; //location in shooter table
		while ($i < sizeof($punches)){
			if ($tableData[$j]['score'] == $scoreList[$i]){
				$tableData[$j]['awardClass'] = $class . strval($i+1);
				$tableData[$j]['punches'] = $punches[$i];
				$j++;
			}else {
				$i++;
			}
		}
		while ($i >= sizeof($punches) && $j < $shooterCount){
			$tableData[$j]['awardClass'] = $tableData[$j]['punches'] = '-';
			$j++;
		};
		
		//draw table
		if ($class == 'M'){
			$class = 'Master';
		}
		$shooterCountString = $shooterCount;
		if ($shooterCountString == 1){
			$shooterCountString .= ' Shooter';
		}else{
			$shooterCountString .= ' Shooters';
		}
		echo '<table border=\'1\'><thead><td colspan=\'3\'>' . $class . ' Class - ' . $shooterCountString . '</td><td>Award</td><td>Punches</td></thead>';
		for ($k = 0; $k < $shooterCount ; $k++){
			echo '<tr>';
			echo '<td class=\'firstName\'>' . $tableData[$k]['firstName'] . '</td>';
			echo '<td class=\'lastName\'>' . $tableData[$k]['lastName'] . '</td>';
			echo '<td class=\'score\'>' . $tableData[$k]['score'] . '</td>';
			echo '<td class=\'awardClass\'>' . $tableData[$k]['awardClass'] . '</td>';
			echo '<td class=\'punches\'>' . $tableData[$k]['punches'] . '</td>';
			echo '</tr>';
		}
		echo '</table>';
		return $tableData;
	}//end function makeClassTable
	
	makeClassTable($eventId,'M');
	makeClassTable($eventId,'AA');
	makeClassTable($eventId,'A');
	makeClassTable($eventId,'B');
	makeClassTable($eventId,'C');
	makeClassTable($eventId,'D');
	makeClassTable($eventId,'E');
	
	?>
	
</body>

<?php

	include 'footer.php';

?>


<script type="text/javascript">

$(document).ready(function(){
	
});

</script>

</html>