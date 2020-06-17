<?php

  include('protect.php');
  include('../config.php');

  // Variable that is used to store and display any error messages....
  // will not have any impact if remains empty
  $errorText = "";

  // Initialize empty statements array. Will be used to
  // store the statements... E
  $statements = array();


  if (isset($_GET['delete-id'])){
    $stmt = "DELETE FROM `phrases` WHERE `phrases`.`id` = " . $_GET['delete-id']; 
    $link->query($stmt); 
  }


  // we handle the database in the header, so things are less cluttered...
  // we can do this in a way that is more structured - but not at this point in time .
  $stmt = "SELECT * FROM `phrases` ORDER BY id DESC";
  $result = $link->query($stmt);

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
      <h1>ADMIN INTERFACE</h1>
    </header>
    <main role="main">
      <?php
        // added an error: if variabe $errorText is set, show error in
        // formtted message box (bootstrap classes used)
        if($errorText != ""){
          echo "<h3 class='alert alert-warning message-box'>" . $errorText . "</h3>";
        }
      ?>

      <h1>Please Check These Phrases! </h1>
      <table class="table-striped table">
          <th>ID</th>
          <th>Phrase</th>
          <th>Name</th>
          <th></th>
          <th></th>
          <?php
          // use $reslut variable from PHP head of file...
          if ($result->num_rows > 0){
              // get data from database line by line.
              // each line will be stored in $row
              // access to $row elements via index (0 for first element, 2 for third element)
              while ($row = mysqli_fetch_row($result)){
                echo "<tr>\n";
                echo "<td>" . $row[0] . "</td>\n";
                echo "<td>" . $row[1] . "</td>\n";
                echo "<td>" . $row[2] . "</td>\n";
                echo "<td><a href='?delete-id=" . $row[0] . "  '>Delete </a></td>";
                echo "<td><a href='phrase_edit.php?edit-id=" . $row[0] . "  '>Edit </a></td>";
                echo "</tr>";
              }
          }
          else {
              echo "<tr><td colspan='2'>No data found</td></tr>";
          }
          ?>
      </table>

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
