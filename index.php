<?php
  include('config.php');

  // Variable that is used to store and display any error messages....
  // will not have any impact if remains empty
  $errorText = "";

  // Initialize empty statements array. Will be used to
  // store the statements... 
  $statements = array();

  // if button was pressed (user wants to save something... )
  if(isset($_GET['btn-save'])){
    // make sure user has entered all phrase-information correctly
    if($_GET['phrase_01'] != "" && $_GET['phrase_02'] != "" && $_GET['phrase_03'] != "" && !empty($_GET['nameField'])){
      // put together the message that is to be savet. For now, we save name and phrases in one statement
      $text = $_GET['nameField'] . ": " . $_GET['phrase_01'] . " " . $_GET['phrase_02'] . " " . $_GET['phrase_03'] . "\n";
      // write info to a file
      file_put_contents($filename, $text, FILE_APPEND);
    }
    // user has not provided all required information... (check performed on server ...)
    else {
      $errorText = "Please chose one option from each of the dropdowns and enter your name!";
    }
  }


  // connect to database
  // $link is the connection to the database and will be used to access database
  $link = mysqli_connect("localhost", "root", "", "i2_20s_phrases");
  // If an error occurs, we want to show that. Looks ugly, but works...
  echo mysqli_error($link);

  // we handle the database in the header, so things are less cluttered...
  // we can do this in a way that is more structured - but not at this point in time .
  $stmt = "SELECT * FROM `phrases`";
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
    <link rel="stylesheet" href="script/style.css">


    <title>Hello, world!</title>
  </head>
  <body>
    <!-- header image to make things a little more appealing -->
    <header>
      <h1>What do YOU say YES to?</h1>
    </header>
    <main role="main">
      <?php
        // added an error: if variabe $errorText is set, show error in
        // formtted message box (bootstrap classes used)
        if($errorText != ""){
          echo "<h3 class='alert alert-warning message-box'>" . $errorText . "</h3>";
        }
      ?>
      <h1>I say YES! to ... </h1>
      <!--
          the class "needs-validation" indicates that we want to provide
          feedback after form validation

          the attribute "novalidate" indicates that the form won't
          be validated on submit - the Javascript in script.js intercepts
          the submit button and provides feedback...
      -->
      <form method="get" class="needs-validation" novalidate>
        <div class="row">
         <div class="col-md-4">
                <select name="phrase_01" class="custom-select">
                  <!--
                    First item in option list is
                    - disabled: cannot be chosen by user
                    - selected: selected at first, so user know that she has to select something...
                  -->
                  <option value="" selected disabled>Open the select menu</option>
                  <option value="Sportliche">Sportliche</option>
                  <option value="Bekloppte">Bekloppte</option>
                  <option value="Sensationelle">Sensationelle</option>
                  <option value="Tolle">Tolle</option>
                  <option value="Seltene">Seltene</option>
                </select>
          </div>
          <div class="col-md-4">
                <select name="phrase_02" class="custom-select">
                  <option value="" selected disabled>Open the select menu</option>
                  <option value="Ketten">Ketten</option>
                  <option value="Bleistifte">Bleistifte</option>
                  <option value="Muelltonnen">Muelltonnen</option>
                  <option value="Gitarren">Gitarren</option>
                  <option value="Schnurrbaerte">Schnurrbaerte</option>
                </select>
          </div>
          <div class="col-md-4">
                <select name="phrase_03" class="custom-select">
                  <option value="" selected disabled>Open the select menu</option>
                  <option value="Verdoppelung">Verdoppelung</option>
                  <option value="System">System</option>
                  <option value="Gefahr">Gefahr</option>
                  <option value="Wohlklang">Wohlklang</option>
                  <option value="Versicherung">Versicherung</option>
                </select>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label for="nameField" class="control-label">Name</label>
              <input type="text" class="form-control" id="nameField" name="nameField" placeholder="‘Max Mustermann‘ - Wirklich?!" required>
              <!--
                the next two divs are only shown after form validation.
                if the validation had no erros the first div is shown (resp. is not shown if form is submitted instantly)
                if errors occur, the second div is shown
                in order to make this work, we need a little Javascript that I have put to an external JS file
              -->
              <div class="valid-feedback">Genau. Super Name!</div>
              <div class="invalid-feedback">Das kannst Du besser. Schreib hier doch was rein.</div>
            </div>
          </div>
        </div>

        <button type="submit" class="btn btn-primary" value="1" name="btn-save">Say Yes!</button>
      </form>

      <hr>

      <h1>Others say YES! to... </h1>
      <table class="table-striped table">
          <th>ID</th>
          <th>Phrase</th>
          <th>Name</th>
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
