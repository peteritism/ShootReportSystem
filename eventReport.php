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
		tr:nth-child(even) {
			background: #DDD;
		}
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

	$scoreReportData = array();

	function makeTable ($eventId,$searchBy,$searchParameter/*,$headersArray*/){
		//this is shit
		drawTable(
			mergeshooterArrayAwards(
				giveAwards(
					getShooters($eventId, $searchBy, $searchParameter),
				$searchBy,
				$searchParameter),
			$searchParameter),
		$searchBy,
		$searchParameter);

	} //end makeTable
	
	function getShooters($eventId, $searchBy, $searchParameter){
		//number of shooters by searchBy
		
		if ($searchBy == 'class' || $searchBy == 'concurrent'){
			$whereClause = 'WHERE eventshooter.' . $searchBy . '=\'' . $searchParameter . '\'';
		}else if ($searchBy == 'concurrentLady'){
			$whereClause = 'WHERE shooter.nscaConcurrentLady =\'' . $searchParameter . '\'';

		}
		

		$query = 	'SELECT COUNT(*)
					AS numberOfShooters
					FROM shooter
					JOIN eventshooter
					ON eventshooter.shooterId  = shooter.id ' .
					$whereClause . '
					AND eventshooter.shootEventId =' . $eventId;
		$result = dbquery($query);
		$row = mysqli_fetch_assoc($result);
		$shooterCount = $row['numberOfShooters'];
		
		//get shooters by searchBy
		$query =	'SELECT *
					FROM shooter
					JOIN eventshooter
					ON eventshooter.shooterId  = shooter.id ' .
					$whereClause . '
					AND eventshooter.shootEventId =' . $eventId;
		$result = dbquery($query);
		$shooterArray = array();
		$i = 0;
		while ($row = mysqli_fetch_array($result)){
			//ust add score to array instead of creating new array?
			$shooterArray[$i]['firstName'] = $row['firstName'];
			$shooterArray[$i]['lastName'] = $row['lastName'] . ' ' . $row['suffix'];
			$shooterArray[$i]['nscaId'] = $row['nscaId']; 		//merged into nsca report
			$shooterArray[$i]['state'] = $row['state'];		//merged into nsca report
			$shooterArray[$i]['shooterId'] = $row['shooterId'];//merged into nsca report
			//get scores
			$query2 = 	'SELECT SUM(targetsBroken)
						AS totalScore
						FROM shootereventstationscore
						WHERE eventShooterId=' . $row['id'];
			$result2 = dbquery($query2);
			$row2 = mysqli_fetch_assoc($result2);
			$shooterArray[$i]['score'] = $row2['totalScore'];
			$i++;
		}
		$getShootersReturn = array($shooterArray,$shooterCount);
		return $getShootersReturn;
	} //end getShooters
	
	function giveAwards($getShooterReturn,$searchBy,$searchParameter){
		//might be possible to do this with a strategic db query
		//sort by scores DESC
		$shooterArray = $getShooterReturn[0];
		$shooterCount = $getShooterReturn[1];

		if ($shooterCount > 0){
			foreach ($shooterArray as $shooter){
				$tmp[] = $shooter['score'];
			}
			array_multisort($tmp, SORT_DESC, $shooterArray);
			//put one of each score in array in descending order
			$scoreList = array();
			$last = 10000;
			foreach ($shooterArray as $shooter){
				$current = $shooter['score'];
				if ($current < $last){
					$scoreList[] = $current;
					$last = $current;
				}
			}
		}
		if($searchBy == 'class'){
			//determine punches/award based on amount of shooters
			if ($shooterCount >= 0 && $shooterCount <= 2){
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
				if ($shooterArray[$j]['score'] == $scoreList[$i]){
					$shooterArray[$j]['awardClass'] = $searchParameter . strval($i+1);
					$shooterArray[$j]['punches'] = $punches[$i];
					$j++;
				}else {
					$i++;
				}
			}
			while ($i >= sizeof($punches) && $j < $shooterCount){
				$shooterArray[$j]['awardClass'] = $shooterArray[$j]['punches'] = '-';
				$j++;
			};
		}else if($searchBy == 'concurrent' || $searchBy == 'concurrentLady'){
			//this will be more complicated when I implement points system, but for now this will do.
			//add shooter count conditions here for concurrent points
			$concurrentPoints = array(4,3,2,1);
			$i = 0; //location in punches and scoreList
			$j = 0; //location in shooter table
			while ($i < sizeof($concurrentPoints)){
				if (isset($shooterArray[$j]['score']) && $shooterArray[$j]['score'] == $scoreList[$i]){
					$shooterArray[$j]['awardConcurrent'] = $searchParameter . strval($i+1);
					$shooterArray[$j]['concurrentPoints'] = $concurrentPoints[$i];
					$j++;
				}else {
					$i++;
				}
			}
			while ($i >= sizeof($concurrentPoints) && $j < $shooterCount){
				$shooterArray[$j]['awardConcurrent'] = $shooterArray[$j]['concurrentPoints'] = '-';
				$j++;
			};
			

		}//else if($searchBy == 'concurrentLady'){
			//append award instead of settign award
		
		//}
		
		return array($shooterArray,$shooterCount); 
	} //end giveAwards
	
	function mergeshooterArrayAwards($shooterArrayAndShooterCount){
		// $mergeshooterArrayAwards = array($mergedData, $shooterArray)
		//return $mergeshooterArrayAwardsReturn;
		return $shooterArrayAndShooterCount;
	}//end mergeshooterArrayAwards


	function drawTable($shooterArrayAndShooterCount, $searchBy, $searchParameter){
		
		$shooterArray = $shooterArrayAndShooterCount[0];
		$shooterCount = $shooterArrayAndShooterCount[1];
		//dump shooterArray into scoreReport array 
		//global $scoreReportData;
		//$scoreReportData = array_merge($scoreReportData, $shooterArray);

		//draw table
		if ($shooterCount > 0){
			if ($searchBy == 'M'){
				$searchBy = 'Master';
			}
			$shooterCountString = $shooterCount;
			if ($shooterCountString == 1){
				$shooterCountString .= ' Shooter';
			}else{
				$shooterCountString .= ' Shooters';
			}
			if ($searchBy == 'class'){
				echo '<table border=\'1\'><thead><td colspan=\'3\'>' . $searchParameter . ' Class - ' . $shooterCountString . '</td><td>Award</td><td>Punches</td></thead>';
				for ($k = 0; $k < $shooterCount ; $k++){
					echo '<tr>';
					echo '<td class=\'firstName\'>' . $shooterArray[$k]['firstName'] . '</td>';
					echo '<td class=\'lastName\'>' . $shooterArray[$k]['lastName'] . '</td>';
					echo '<td class=\'score\'>' . $shooterArray[$k]['score'] . '</td>';
					echo '<td class=\'awardClass\'>' . $shooterArray[$k]['awardClass'] . '</td>';
					echo '<td class=\'punches\'>' . $shooterArray[$k]['punches'] . '</td>';
					echo '</tr>';
				}
				echo '</table>';
			}else if ($searchBy == 'concurrent' || $searchBy == 'concurrentLady' ){
				if ($searchBy == 'concurrentLady'){
					$searchParameter = 'LY';
				}
				echo '<table border=\'1\'><thead><td colspan=\'3\'>' . $searchParameter . ' Concurrent - ' . $shooterCountString . '</td><td>Award</td></thead>';

				for ($k = 0; $k < $shooterCount ; $k++){
					echo '<tr>';
					echo '<td class=\'firstName\'>' . $shooterArray[$k]['firstName'] . '</td>';
					echo '<td class=\'lastName\'>' . $shooterArray[$k]['lastName'] . '</td>';
					echo '<td class=\'score\'>' . $shooterArray[$k]['score'] . '</td>';
					echo '<td class=\'awardClass\'>' . $shooterArray[$k]['awardConcurrent'] . '</td>';
					echo '</tr>';
				}

			}
		}
		//return $shooterArray;
	} //end drawTable

	//end function makeClassTableh
	
	/*makeTable($eventId,'class','M');
	makeTable($eventId,'class','AA');
	makeTable($eventId,'class','A');
	makeTable($eventId,'class','B');
	makeTable($eventId,'class','C');
	makeTable($eventId,'class','D');
	makeTable($eventId,'class','E');
	
	makeTable($eventId,'concurrent','SJ');
	makeTable($eventId,'concurrent','JR');
	makeTable($eventId,'concurrent','VT');
	makeTable($eventId,'concurrent','SV');
	makeTable($eventId,'concurrent','SSV');
	makeTable($eventId,'concurrentLady','1');
	//makeTable($eventId,'concurrent',''); //open concurrency - to calculate All-X points
	
	*/
	
	//HOA
	//get shooters with hoa
	//find highest score
	//calculate percentage winnings
	//calculate money winnings
	
	//HIC
	//get shooters with hic per class
	//same as above
	
	//lewis
	//get shooters with lewis
	//get shooterCount with lewis
	//get lewis groups
	//order shooters by score ascending **important
	
	
	//
	//Lewis Calculation
	//
	//these queries should be moved to getShooters
	
	
	$query = '	SELECT lewisGroups
				FROM shootevent
				WHERE id=' . $eventId;
	$result = dbquery($query);
	$row = mysqli_fetch_assoc($result);
	$lewisGroups = $row['lewisGroups'];
	

	if(!isset($lewisGroups) || $lewisGroups == 0){
		echo 'Lewis Groups not set';
	}
	else{
		$searchBy = 'lewisOption';
		$searchParameter = 1;
		$whereClause = 'WHERE eventshooter.' . $searchBy . '=\'' . $searchParameter . '\'';

		$query = 	'SELECT COUNT(*)
					AS numberOfShooters
					FROM shooter
					JOIN eventshooter
					ON eventshooter.shooterId  = shooter.id ' .
					$whereClause . '
					AND eventshooter.shootEventId =' . $eventId;
		$result = dbquery($query);
		$row = mysqli_fetch_assoc($result);
		$shooterCount = $row['numberOfShooters'];

		//get shooters by searchBy
		$query =	'SELECT *
					FROM shooter
					JOIN eventshooter
					ON eventshooter.shooterId  = shooter.id ' .
					$whereClause . '
					AND eventshooter.shootEventId =' . $eventId;
		$result = dbquery($query);
		$i = 0;
		while ($row = mysqli_fetch_array($result)){
			$shooterArray[$i]['firstName'] = $row['firstName'];
			$shooterArray[$i]['lastName'] = $row['lastName'] . ' ' . $row['suffix'];
			//get scores
			$query2 = 	'SELECT SUM(targetsBroken)
						AS totalScore
						FROM shootereventstationscore
						WHERE eventShooterId=' . $row['id'];
			$result2 = dbquery($query2);
			$row2 = mysqli_fetch_assoc($result2);
			$shooterArray[$i]['score'] = $row2['totalScore'];
			$i++;
		}

		foreach ($shooterArray as $val){
			$tmp[] = $val['score'];
		}
		array_multisort($tmp, SORT_ASC, $shooterArray);


		$shooterCountModular = $shooterCount % $lewisGroups;  //left over shooters if broken into even groups
		$groupShooterCount = ( $shooterCount - $shooterCountModular ) / $lewisGroups;  //Lewis group size if evenly divisible
		$groupCounts = array();  //size of each Lewis group from lowest score to highest
		$x = 1; //$x - group number

		while ($x <= $lewisGroups){
			if ($shooterCountModular > 0){
				$groupCounts[$x] = $groupShooterCount + 1;
				$shooterCountModular -= 1;
			}else {
				$groupCounts[$x] = $groupShooterCount;
			}
			$x++;
		}  //end while

		//assign groupings
		$x = 0;
		$y = $lewisGroups;
		//look at each value in the  $lewisGroupShooterCount array
		foreach ($groupCounts as $shootersLeftInGroup){
			//set shooters' group
			while ($shootersLeftInGroup > 0 ){
				$shooterArray[$x]['lewisGroup'] = $y;
				$shootersLeftInGroup -= 1;
				$x += 1;
			}
			$y -= 1;
		}

		//groupings for 
		$originalGroup = $lastGroup = $shooterArray[0]['lewisGroup'];
		$lastScore = $currentGroup = -1; //set to impossible
		$scoresBelowLine = $scoresAboveLine = 0;
		foreach ($shooterArray as $shooter){

			$currentScore = $shooter['score'];
			$currentGroup = $shooter['lewisGroup'];
			
			//echo $lastScore;
			
			if ($currentScore == $lastScore){
				if ($scoresBelowLine == 0 && $scoresAboveLine == 0){
					$originalGroup = $lastGroup;
				}
				if ($currentGroup == $originalGroup){
					$scoresBelowLine += 1;
				}else{
					$scoresAboveLine += 1;
					$highestGroup = $currentGroup;
				}
			}else { //if scores are not the same
					if ($scoresBelowLine == 0 && $scoresBelowLine == 0){
						//capture case, no ties before this, skip to next score
					}else if ($scoresBelowLine >= $scoresAboveLine){
						$lewisTies[$lastScore] = $originalGroup;
					//	echo ' v';
					}else {
						$lewisTies[lastScore] = $lastGroup;
					//	echo ' ^';
						$scoresAboveLine = $scoresBelowLine = 0;
					}
					$scoresAboveLine = $scoresBelowLine = 0;
			}
			$lastScore = $currentScore;
			$lastGroup = $currentGroup;

			//echo ' --- ' . $currentScore . ' - ' . $currentGroup . ' - ' . $originalGroup . ' - ' . $lastGroup . ' - ' . $scoresBelowLine . ' - ' . $scoresAboveLine;
			//echo '</br>';
			
		}  //end outer foreach


		//changing groups
		foreach ($shooterArray as &$shooter){
			foreach ($lewisTies as $score => $newGroup){
				if ($shooter['score'] == $score ){
					$shooter['lewisGroup'] = $newGroup;
					break;
				}
			}
		} //end changing groups
		
		unset($shooter);

		$query =	'SELECT lewisCost
					FROM shootevent
					WHERE id =' . $eventId;
		$result = dbquery($query);
		$row = mysqli_fetch_assoc($result);
		$lewisCost = $row['lewisCost'];
		$totalLewisMoney = $lewisCost * $shooterCount;
		$groupLewisMoney = $totalLewisMoney / $lewisGroups;
		$x = 1;

		while ($x <= $lewisGroups){

			$highestScore = 0;
			$awardSplit = 0;
			foreach ($shooterArray as $shooter){
				//the last score meeting the if statement should always be highest else there's a problem
				if($shooter['lewisGroup'] == $x){
					$highestScore = $shooter['score'];
				}
			}
			foreach ($shooterArray as $shooter){
				if ($shooter['score'] == $highestScore){
					$awardSplit++;
				}
			}

			//convert lewis group number to letter (NSCA practice) 
			$lewisClassLetter = chr($x + 64);

			foreach ($shooterArray as &$shooter){
				if($shooter['lewisGroup'] == $x){
					if ($shooter['score'] == $highestScore){
						$shooter['lewisPercentage'] = round((1 / $awardSplit), 3, PHP_ROUND_HALF_UP) * 100;
						$shooter['lewisMoney'] = round(((1 / $awardSplit) * $groupLewisMoney),2,PHP_ROUND_HALF_UP);
					}else {
						$shooter['lewisPercentage'] = $shooter['lewisMoney'] = '0';
					}
				}
			}
			
			unset($shooter);

			echo '<table border="1">';
				echo '<thead><td colspan="5">Lewis Class ' . $lewisClassLetter . '</thead>';
				echo '<thead>';
					echo '<td colspan="2"></td>'; //firstName lastName
					echo '<td>Score</td>';
					echo '<td>Win %</td>';
					echo '<td class="private">Money</td>';
				echo '</thead>';
			$shooterArrayDesc = array_reverse($shooterArray);
			foreach ($shooterArrayDesc as $shooter){
				if($shooter['lewisGroup'] == $x){
					echo '<tr>';
						echo '<td class="firstName">' . $shooter['firstName'] . '</td>';
						echo '<td class="lastName">' . $shooter['lastName'] . '</td>';
						echo '<td>' . $shooter['score'] . '</td>';
						echo '<td>' . $shooter['lewisPercentage'] . '%</td>';
						echo '<td class="private">$' . $shooter['lewisMoney'] . '</td>';
					echo '</tr>';
				}
			}
			echo '</table>';
			$x++;
		}//end while
	}
	
	//
	//end Lewis Calculation
	//
	
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