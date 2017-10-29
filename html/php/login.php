<?php

	session_start();
	
	if((!isset($_POST['login'])) || (!isset($_POST['password']))){
		header('Location: ../index.php');
		exit();
	}

	require_once "connect.php";

	$connection = @new mysqli($host, $db_user, $db_pass, $db_name);

	if($connection -> connect_errno != FALSE){
		echo "Can't connect to a database. Error number:".$connection -> connect_errno;
	} else {
		
		$login = $_POST['login'];
		$password = $_POST['password'];
		$remember = $_POST['remember'];
		
		$login = htmlentities($login, ENT_QUOTES, "UTF-8");
		$password = htmlentities($password, ENT_QUOTES, "UTF-8");
		 //nazwa kolumny w db
		
		if($result = @$connection -> query(sprintf("SELECT * FROM uzytkownicy WHERE ( user = '%s' OR email = '%s' ) AND pass = '%s'",
			mysql_real_escape_string($connection, $login),
			mysql_real_escape_string($connection, $login),
			mysql_real_escape_string($connection, $password)))){
				
			$howMany = $result -> num_rows;
			
			if($howMany > 0){
				$_SESSION['isLogin'] = TRUE;
				
				$line = $result -> fetch_assoc();
				$_SESSION['id'] = $line['id'];
				$_SESSION['user'] = $line['user']; //nazwa kolumny w db
				
				unset($_SESSION['loginError']);
				$result -> free_result();
				
				header('Location: account.php');
				
			} else {
				
				$_SESSION['loginError'] = '<span style = "color:red"> Nieprawidłowy login lub hasło! </span>';
				
				header('Location: ../index.php');
			}
		}
		
		$connection -> close();
	}


?>