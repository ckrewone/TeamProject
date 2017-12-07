<?php
	session_start();
	
	if(!isset($_SESSION['isLogin'])){
		header('Location: ../index.php');
		exit();
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
    <meta name="author" content="Damian Henisz, MichaĹ‚ Ĺ»akowski, Adam WĂłjcik">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
          <link href="../css/full.css" rel="stylesheet" type="text/css">
          <link href="../css/signing.css" rel="stylesheet" type="text/css">

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
            <a href = "logout.php">
            <button id="Wylogowanie" type="button" class="btn btn-danger btn-sm" style="cursor:pointer";> Wyloguj się
              <i class="fa fa-user-circle"> </i>
                </button> 
            </a>
        </p>
    </nav>

    <main role="main">

      <div class="jumbotron" style="background: url(../img/1.png);">
        <div class="container">
	<h1 class="display-3" style="color:rgb(255,255,255);">Hello, user </h1>
          <p style="color:rgb(200,200,200);">This is a template for a simple marketing or informational website. It includes a large callout called a jumbotron and three supporting pieces of content. Use it as a starting point to create something more unique.</p>
          <p><a class="btn btn-danger btn-lg" href="#" role="button">Learn more  <i class="fa fa-info-circle" aria-hidden="true"></i></a></p>
	  <p><a class="btn btn-danger btn-lg" href="#" role="button">Get started  <i class="fa fa-rocket" aria-hidden="true"></i></a></p>
        </div>
      </div>

        <hr>

      </div> <!-- /container -->

    </main>

    <footer class="container">
      <center><p style="color:rgb(200,200,200);">&copy; WebApp 2017</p></center>
    </footer>


  
  </body>
</html>