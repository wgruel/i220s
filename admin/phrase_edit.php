<?php
  include('protect.php');
  include('../config.php');
  include('../functions.php');

  // Variable that is used to store and display any error messages....
  // will not have any impact if remains empty
  $errorText = "";

  // initalizie variable $update_result
  $update_result = 0; 
  
  // if button has been pressed, ...
  if (isset($_GET['btn-save'])){
    
    $phrase = $_GET['phrase'];
    $address = $_GET['address'];
 
    $location = geocode($address, $api_key); 

    // we create an update statement...
    $stmt = "UPDATE `phrases` SET 
        `phrases`.`phrase` = '" . $phrase . "',      
        `address` = '" . $address . "',
        `lat` = '" . $location[0] . "',
        `lng` = '" . $location[1] . "'  
        WHERE id = " . $_GET['edit-id']; 

    // ... and execute it... 
    $update_result = $link->query($stmt); 
    if ($update_result > 0) {
      $errorText = "Updated " . $update_result . " Datasets. <a href='index.php'>Back to index</a>"; 
    }
  }

  // Get the phrase with edit-id from database in order to later populate the input-field....
  $stmt = "SELECT * FROM `phrases` WHERE id = " . $_GET['edit-id'];
  $result = $link->query($stmt);
  $row = mysqli_fetch_row($result); 
  $text = $row[1];

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
      <h1>EDIT PHRASE</h1>
    </header>
    <main role="main">
      <?php
        // added an error: if variabe $errorText is set, show error in
        // formtted message box (bootstrap classes used)
        if($errorText != ""){
          echo "<h3 class='alert alert-warning message-box'>" . $errorText . "</h3>";
        }
      ?>

      <h1>Please Edit This Phrase! </h1>
      <form>
        <div class="form-group">
            <input type="hidden" name="edit-id" value="<?php echo $_GET['edit-id'] ?>">
            <input type="text" name="phrase" class="form-control input-lg" value="<?php echo $text ?>">
        </div>
        <div class="form-group">
              <label for="address">Address:</label>
              <input type="text" class="form-control" id="address" name="address" value="<?php echo $row[3]?>">
        </div>
        <button type="submit" class="btn btn-primary" value="1" name="btn-save">Save</button>

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
