<?php
    session_start();

    if(isset($_POST['login'])){
      $_OK = true;
      
      $login = $_POST['login'];
      $email = $_POST['email'];
      $password = $_POST['password'];
      $cpassword = $_POST['cpassword'];

      if(strlen($login) < 3 || strlen($login) > 20){
        $_OK = false;
        $_SESSION['error_login'] = "Login musi posiadać od 3 do 20 znaków.";
      } 

      if(!ctype_alnum($login)){
        $_OK = false;
        $_SESSION['error_login'] = "Login nie możę posiadać znaków specjalnych oraz narodowych.";
      }

      $emailb = filter_var($email, FILTER_SANITIZE_EMAIL);

      if(!(filter_var($emailb, FILTER_VALIDATE_EMAIL)) || ($email != $emailb)){
        $_OK = false;
        $_SESSION['error_email'] = "Wprowadz poprawny adres email.";
      }

      if((strlen($password) < 8) || (strlen($password) > 20)){
        $_OK = false;
        $_SESSION['error_password'] = "Hasło musi posiadać od 8 do 20 znaków.";
      }

      if($password != $cpassword){
        $_OK = false;
        $_SESSION['error_password'] = "Hasła nie są takie same.";
      }

      $password_hash = password_hash($password, PASSWORD_DEFAULT);

      $secret_key = "6LfiwzYUAAAAAEYlqGw2NHSgKhmSHTt7DagLKgdE";

      $check = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret_key.'&response='.$_POST['g-recaptcha-response']);

      $answer = json_decode($check);

      if(!($answer -> success)){
        $_OK = false;
        $_SESSION['error_bot'] = "Zweryfikuj się";
      }

      require_once "connect.php";
      mysqli_report(MYSQLI_REPORT_STRICT);

      try{
        $connect = new mysqli($host, $db_user, $db_pass, $db_name);
        if($connect -> connect_errno != 0)
          throw new Exception(mysqli_connect_errno());
        else{
          $result = $connect -> query("SELECT id FROM users WHERE Email='$email'");

          if(!$result)
            throw new Exception($connect -> error);

          $howManyEmail = $result -> num_row;
          if($howManyEmail > 0){
            $_OK = false;
            $_SESSION['error_email'] = "Konto o podanym adresie email już istnieje.";
          }

          $result = $connect -> query("SELECT id FROM users WHERE Login='$login'");
          
          if(!$result)
            throw new Exception($connect -> error);
          
          $howManyLogin = $result -> num_row;
          if($howManyLogin > 0){
            $_OK = false;
            $_SESSION['error_login'] = "Konto o podanym loginie już istnieje.";
          }

          if($_OK){
            if($connect -> query("INSERT INTO users VALUES (NULL, '$login', '$email', '$password_hash')")){
              $_SESSION['success'] = true;
              header('Location: welcome.php');
            } else {
              throw new Exception($connect -> error);
            }
          }

          $connect -> close();
        }
      }
      catch(Exception $error){
        echo '<span style="color:red;">Błąd serwera, proszę spróbować za jakiś czas.</span>';
        //echo '<br/> Error: '.$error;
      }
    }

?>

<!doctype html>
<html lang="pl">
  <head>
    <title>WebApp</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="author" content="Damian Henisz, Michał Żakowski, Adam Wójcik">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
          <link href="css/full.css" rel="stylesheet" type="text/css">
          <link href="css/signing.css" rel="stylesheet" type="text/css">
          
    <style>
      .error{
         color:red;
         margin-top: 10px;
         margin-bottom: 10px;
       }
    </style>

    <script src='https://www.google.com/recaptcha/api.js'></script>

  </head>

  <body>
  <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
      
    <nav class="navbar" style="padding-bottom: 150px;"> 
      	<!--padding na  200px lepiej wyglada -->
        <p id="Logo" class="navbar-brand"> <b> WEBAPP </b> </p>
          <p class="navbar-right" style="padding-top: 20px;" >
            <a href="../index.php">
            <button id="Rejestracja" type="button" class="btn btn-default btn-sm" > Home
              <i class="fa fa-home fa-fw"></i>
            </button> </a>  
            
            </p>
    </nav>
      

    <div class="row" >
      <div class="container padding=2">
    <div class="row vertical-offset-100">
        <div class="col-md-4 col-md-offset-4"></div>
        <div class="col-md-4 col-md-offset-4">
    		<div class="panel panel-default" style="border: 0.5px; border-color: white; border-style: solid; padding: 20px; background-color: #1f1f1f;" >

			  	<div class="panel-heading">
                    <center> <h3 class="panel-title" > Stwórz konto </h3></center>
			 	</div>
			  	<div class="panel-body" >
			    	<form method = "post" accept-charset="UTF-8" role="form">
                    <fieldset>
			    	  	<div class="form-group">
                  <input class="form-control" placeholder="E-mail" name="email" type="text">
                  
                  <?php
                    if(isset($_SESSION['error_email'])){
                      echo '<div class = "error">'.$_SESSION['error_email'].'</div>';
                      unset($_SESSION['error_email']);
                    }
                  ?>

				</div>
				
              <div class="form-group">
                 <input class="form-control" placeholder="Login" name="login" type="text">

                 <?php
                  if(isset($_SESSION['error_login'])){
                    echo '<div class = "error">'.$_SESSION['error_login'].'</div>';
                     unset($_SESSION['error_login']);
                  }
                ?>

            	</div>
               
			    <div class="form-group">
                	<input class="form-control" placeholder="Hasło" name="password" type="password">
                
                	<?php
                  	if(isset($_SESSION['error_password'])){
                    	echo '<div class = "error">'.$_SESSION['error_password'].'</div>';
                    	unset($_SESSION['error_password']);
                  	}
                	?>
				</div>

              <div class="form-group">
                <input class="form-control" placeholder="Powtórz hasło" name="cpassword" type="password">
              </div>

              <div class="g-recaptcha" data-sitekey="6LfiwzYUAAAAAKb2Q9lTkEJjczvoYMqpfnifVmw5"></div>
              
                  <?php
                    if(isset($_SESSION['error_bot'])){
                      echo '<div class = "error">'.$_SESSION['error_bot'].'</div>';
                      unset($_SESSION['error_bot']);
                    }
				  ?>
				  
				</br>
				
			    <input class="btn btn-lg btn-success btn-block" type="submit" value="Zarejestruj się" style="background-color: #cc0033; cursor:pointer;">
			    </fieldset>
			    </form>
			
			</div>
		</div>
        <div class="col-md-4 col-md-offset-4"></div>
	</div>
    
</div>
  


  
  </body>
</html>
