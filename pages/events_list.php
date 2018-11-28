<?php
	include('../includes/fonctions.inc.php');
	include('../includes/postgres.conf.inc.php');
	session_start();
?>

<!DOCTYPE html>
<html lang = "fr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<link rel="stylesheet" type="text/css" href="../style/style.css"/>
		<title> BDD </title>
	</head>

	<body>

		<?php
		echo list_event();

		for($i=1; $i<=max_id('SELECT event_id FROM event'); $i++) {
			if(isset($_POST['event'.$i])) {
				header('location: events_details.php?event_id=' .$i);
			}
		}

		if(isset($_POST['back'])) {
			header('location: ../index.php');
		}
		?>

	</body>
</html>
