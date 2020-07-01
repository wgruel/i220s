<?php
  include('config.php');
  include('functions.php');

  // Variable that is used to store and display any error messages....
  // will not have any impact if remains empty
  $errorText = "";

  // Initialize empty statements array. Will be used to
  // store the statements... E
  $statements = array();

  // if button was pressed (user wants to save something... )
  if(isset($_GET['btn-save'])){
    // make sure user has entered all phrase-information correctly
    if($_GET['phrase_01'] != "" && $_GET['phrase_02'] != "" && $_GET['phrase_03'] != "" && !empty($_GET['nameField'])){
      // put together the message that is to be saved. 
      $text = $_GET['phrase_01'] . " " . $_GET['phrase_02'] . " " . $_GET['phrase_03'] . "\n";
      $name = $_GET['nameField'];

      // get address .... 
      $address = urldecode($_GET['address']);

      $location = geocode($address, $api_key); 

      // write info to a database
      // create sql-statements
      $stmt = "INSERT INTO `phrases` (`id`, `phrase`, `name`, `address`, `lat`, `lng`) VALUES (NULL, '" . $text . "', '" . $name . "' ,'" . $address . "' ,'" . $location[0] . "' ,'" . $location[1] . "')";
      // execute statement
      $result = $link->query($stmt);
    }
    // user has not provided all required information... (check performed on server ...)
    else {
      $errorText = "Please chose one option from each of the dropdowns and enter your name!";
    }
  }


  // we handle the database in the header, so things are less cluttered...
  // we can do this in a way that is more structured - but not at this point in time .
  $stmt = "SELECT * FROM `phrases`";
  $result = $link->query($stmt);

  // array to store all phrases... 
  $phrases = array(); 
  if ($result->num_rows > 0){
    while ($row = mysqli_fetch_row($result)){    
      $phrase = array(); 
      // fill phrase array with content from database
      // would be nicer to do this in an object, but we haven't 
      // talked about objects, yet. 
      $phrase['id'] = $row[0]; 
      $phrase['text'] = $row[1]; 
      $phrase['name'] = $row[2];
      $phrase['address'] = $row[3];  
      if (!empty ($row[4])) {
        $phrase['lat'] = $row[4]; 
      }
      else {
        $phrase['lat'] = 0.0; 
      }
      if (!empty ($row[5])) {
        $phrase['lng'] = $row[5]; 
      }
      else {
        $phrase['lng'] = 0.0; 
      }    

      // add phrase to phrases array
      array_push($phrases, $phrase); 
    }
  }


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

    <?php 

      echo "<script>";
      echo "var locations = new Array();\n";
      foreach ($phrases as $phrase){
        echo "locations.push(['" . str_replace("\n", "", $phrase['text']) . "', " . $phrase['lat'] . " , " . $phrase['lng'] . "]);\n";
      }
      echo "</script>"; 
    ?>

    <title>Hello, world!</title>
  </head>
  <body>
    <!-- header image to make things a little more appealing -->
    <header style="padding: 0px">
      <div id ="map" style="width: 100%; height: 400px">
      </div>

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
          <div class="col-md-4">
            <div class="form-group">
              <input type="text" class="form-control" id="address" name="address" placeholder="Hauptstrasse 1, 12345 Musterstadt">
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
          <th>Address</th>
          <th>Lat</th>
          <th>Long</th>
          <?php
          // use $reslut variable from PHP head of file...
          if (count($phrases) > 0){
              // get data from database line by line.
              // each line will be stored in $row
              // access to $row elements via index (0 for first element, 2 for third element)
              foreach($phrases as $phrase) {
                echo "<tr>\n";
                echo "<td>" . $phrase['id'] . "</td>\n";
                echo "<td>" . $phrase['text'] . "</td>\n";
                echo "<td>" . $phrase['name'] . "</td>\n";
                echo "<td>" . $phrase['address'] . "</td>\n";
                echo "<td>" . $phrase['lat'] . "</td>\n";
                echo "<td>" . $phrase['lng'] . "</td>\n";
                echo "</tr>";
              }
          }
          else {
              echo "<tr><td colspan='6'>No data found</td></tr>";
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

    <script>
      function initMap() {
        var loc = {lat: 48.7412561, lng: 9.1008994};
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 4,
          center: loc,
          gestureHandling: 'greedy'          
        });

        // create markers
        var marker, i;

        // loop through location-array... 
        for (i = 0; i < locations.length; i++) { 
          // if location is not 0
          if (locations[i][1] != 0){
            // create new marker
            marker = new google.maps.Marker({
              // position is taken from locations array
              position: new google.maps.LatLng(locations[i][1], locations[i][2]),
              // target is the map
              map: map, 
              // we also add a title that is shown if you hover over the marker
              title: locations[i][0]
            });   
            // create local variable infowindow 
            let infowindow = new google.maps.InfoWindow();
            // add eventlistner (on click) to marker
            // if marker is clicked, the anonymous function that 
            // is provided as the third parameter is called 
            // this anonymous function calls the setContent and the open 
            // function of infowindow ... 
            google.maps.event.addListener(marker, 'click', (function(marker, i) {
              return function() {
                infowindow.setContent(locations[i][0]);
                infowindow.open(map, marker);
              }
            })(marker, i));                 
          }
        }

      }
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<?php echo $api_key ?>&callback=initMap">
    </script>

  </body>
</html>
