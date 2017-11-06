<?php
	session_start();
	
	if(!isset($_SESSION['isLogin'])){
		header('Location: ../index.php');
		exit();
	}
?>

<!DOCTYPE HTML>
<html lang = "pl">
<head>
	<meta charset = "utf-8" />
	<meta http-equiv = "X-UA-Compatible" content = "IE=edge,chrome=1" />
</head>

<body>

	<?php
		echo "<p> Witaj ".$_SESSION['user'].'![<a href = "logout.php">Wyloguj</a>]</p>';
	?>
	
</body>
</html>
