<?php

  session_start(); 

  // Hardcoded password. Neither hashed nor associated to a user --> not really safe. 
  // But comes with a little bit of protection ;-)
  $password = "Geheim!";

  // page to redirect to after successful login... 
  // in this case static - always index.php. Might make sense to adapt this, 
  // so that we always redirect to the page that has originally been called... 
  $redirectPage = "index.php";

  // Variable that is used to store and display any error messages....
  // will not have any impact if remains empty
  $errorText = "";

  // check if login button was pressed... 
  if (isset($_POST['btn-login'])){
    // password check... 
    if (!empty($_POST['passwd'] && $_POST['passwd'] == $password)){      

        $_SESSION['authenticated'] = true; 
        header('Location: ' . $redirectPage); 
        exit; 
    }
    else {
        $errorText = "Password incorrect. Evil.";
    }
  }

  // Logout if user uses this page and has not entered a password... 
  unset ($_SESSION['authenticated']); 



 ?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

    <!-- My own stylesheet -->
    <link rel="stylesheet" href="../script/style.css">


    <title>Hello, world!</title>
  </head>
  <body>
    <!-- header image to make things a little more appealing -->
    <header>
      <h1>LOGIN</h1>
    </header>
    <main role="main">
        <?php
            // added an error: if variabe $errorText is set, show error in
            // formtted message box (bootstrap classes used)
            if($errorText != ""){
            echo "<h3 class='alert alert-danger message-box'>" . $errorText . "</h3>";
            }
        ?>

        <h1>Please Login!</h1>
        <form method="POST">
        <input type="password" name="passwd">
        <button type="submit" class="btn btn-primary" value="1" name="btn-login">Login</button>
        </form>
    

    </main>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    <!-- Link to custom Javascript that deals with forms ... -->
    <script src="script/script.js"></script>

  </body>
</html>
