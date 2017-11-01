<?php
	session_start();
	
	if(!isset($_SESSION['success'])){
		header('Location: ../index.php');
		exit();
	} else {
		unset($_SESSION['success']);
	}
?>

<!DOCTYPE HTML>
<html lang = "pl">
<head>
	<meta charset = "utf-8" />
	<meta http-equiv = "X-UA-Compatible" content = "IE=edge,chrome=1" />
</head>

<body>

	Rejestracja udana<br/><br/>
	<a href = "../index.php">Zaloguj sie</a>
	
</body>
</html>