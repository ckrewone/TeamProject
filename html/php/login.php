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
				$_SESSION['loginError'] = '<span style = "color:red"> Wrong login or password! </span>';
				header('Location: ../index.php');
			}
				
		} else {
			$_SESSION['loginError'] = '<span style = "color:red"> Wrong login or password! </span>';
			header('Location: ../index.php');
		}
	}else{
		$_SESSION['loginError'] = '<span style = "color:red"> We have temporary server problems. Sorry. </span>';
		header('Location: ../index.php');									
	}
		
	$connection -> close();
}
?>
