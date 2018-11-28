<?php
include('includes/fonctions.inc.php');
include('includes/postgres.conf.inc.php');
session_start();
?>

<!DOCTYPE html>
<html lang = "fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" type="text/css" href="style/style.css"/>
	<title> BDD </title>
</head>

<banner>
	<?php
	echo sessionInformation();
	?>
</banner>

<body>
	<a href="pages/account_choice.php?action=signIn"> S'insrire </a> <br/>
	<a href="pages/account_choice.php?action=logIn"> Se connecter </a> <br/>
	<a href="pages/account_log_out.php"> Se déconnecter </a> <br/>
	<a href="pages/events_list.php"> Liste des évènements </a> <br/>
	<a href="pages/events_creation.php"> Créer un évènement </a> <br/>
</body>
</html>
