<?php

include ('config.php');
include ('init.php');
include ('include.php');

?>



<!DOCTYPE html>
<html>
<head>

	<title>Club Editor</title>
	<?php
		include 'header.php'; 
	?>
	
</head>

<body>
	<h1>Club Editor</h1>
	
	<div class="clubTable"></div>

</body>

<?php

	include 'footer.php';

?>


<script type="text/javascript">

$(function() {

	var eventsTable = new EditableTable({
		db: '<?= $mysql_database_name ?>',
		dbTable: 'club',
		columnHeaders: ['id','nscaClubId','clubName'],
		uneditableColumns: ['id'],
		element: $('.clubTable'),
		displayFunction: {
			id: function(id) {
				return $('<a href="shootEditor.php?clubId='+id+'">'+id+'</a>');
			}
		}
	});
	eventsTable.loadTable(0,100);
	
	$('#all').click(function(){
		$('.erow').trigger('click');
	});

});

</script>

</html>