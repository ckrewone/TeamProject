<?php

	session_start();
	
	if((!isset($_POST['login'])) || (!isset($_POST['password']))){
		header('Location: ../index.php');
		exit();
	}

	require_once "connect.php";

	$connection = @new mysqli($host, $db_user, $db_pass, $db_name);

	if($connection -> connect_errno != FALSE){
		echo "Nie można połączyć się z bazą. Numer błędu:".$connection -> connect_errno;
	} 
    else
    {

		$login = $_POST['login'];
		$password = $_POST['password'];
		$remember = $_POST['remember'];
        
		$login = htmlentities($login, ENT_QUOTES, "UTF-8");
		
		if($result = @$connection -> query(sprintf("SELECT * FROM users WHERE Login = '%s' OR Email = '%s'",
			mysqli_real_escape_string($connection, $login),
			mysqli_real_escape_string($connection, $login)))){
				
			$howMany = $result -> num_rows;

			if($howMany > 0){
				$line = $result -> fetch_assoc();

				if(password_verify($password, $line['Password'])){
				
				$_SESSION['isLogin'] = TRUE;
				
				$_SESSION['id'] = $line['id'];
				$_SESSION['user'] = $line['Login'];
				
				unset($_SESSION['loginError']);
				$result -> free_result();
				header('Location: account.php');
			} else {
				$_SESSION['loginError'] = '<span style = "color:red"> Zły login lub hasło! </span>';
				header('Location: ../index.php');
			}
				
		} else {
			$_SESSION['loginError'] = '<span style = "color:red"> Zły login lub hasło! </span>';
			header('Location: ../index.php');
		}
	}else{
		$_SESSION['loginError'] = '<span style = "color:red"> Chwilowe problemy z serwerem. Przepraszamy. </span>';
		header('Location: ../index.php');									
	}
		
	$connection -> close();
}
?>
