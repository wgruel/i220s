<?php

  session_start(); 
  // check if user is authenticated by checking $_SESSION
  if (empty($_SESSION['authenticated'])){
    // redirect to login page
    header('Location: login.php');
    // exit script
    exit; 
  }

?>