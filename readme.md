# Creating a phrase generator
### Wolfgang Gruel

The following file describes how to create a phrase generator. It describes creating the service step-by-step. 
If you want to build on the state that we discussed during our class, you can download the code on Github: https://github.com/wgruel/i220s

Make sure, you put the files into a subfolder of your document root (see "Setup system" section). There will an .sql files in that folder - this contains the current database. You can open that file with a normal texteditor (e.g., ATOM or Sublime) and copy the text to the SQL-Tab in PhpMyAdmin AFTER having selected a database (see Setup database section).

### Disclaimer
We want to create PHP-Files that access our database, so we can view, change and delete phrases and users from the browser. In order to learn the concepts, we do this in a simplistic way without considering modern architecture patterns...

If you have any questions about PHP functions, the documentation on www.php.net is a very helpful resource. 

## Setup system

Install Xampp on your machine (https://www.apachefriends.org/de/download.html).
The directory that the webserver works with is called "htdocs" (= document root). It is a subdirectory of your Xampp-Installation folder (on Mac "/Applications/XAMPP/htdocs", on Windows ususally something like "C:\xampp\htdocs")

## Basic steps to achieve the goal (Overview)

We will perform the following steps
- Step 1: Send data to server via form (using <form>-tag + <submit>-buttons, if submitted via GET, we can see the transferred data in the URL)
- Step 2: Recieve data on server via PHP (using $_REQUEST, $_GET, or $_POST arrays) and process it
- Step 3: Store data on server (via file-system first, then using a database)
- Step 4: Dynamicalls create a response with PHP that might contain data that has just been stored

## Create the input interface

The first thing, we want to do, is to create an input site.

We will need a headline:

```
<h1>I say YES! to ...</h1>
```

To submit that information, we need a form. As a first step, we will just send the information to the page itself (no action will be set) and will use GET as a method to transmit the data.

```
<form method="get">
 <!-- form content goes here -->
</form>
```
Without form-tag, there is no submission of data. The form-data can be sent via GET (data visible in URL) or via POST (data not visible in URL). 

As input, we will use two select-fields - every phrase-element will be put into an option-tag. Each of the selects needs to get a unique name (e.g. "phrase_01").

```
    <select class="custom-select" name="phrase_01">
        <option selected>Open this select menu</option>
        <option value="learning">learning</option>
        <option value="exploring">exploring</option>
        <option value="finding">finding</option>
        <option value="enjoying">enjoying</option>
    </select>
```

If we want to use special characters as values, we need to URL-encode them (e.g., replace spaces with %20).

Then, we will need a button in order to submit all that stuff:
```
    <button type="submit" class="btn btn-default" name="btn-save" value="1">Say YES!</button>
```

If we load the page in our browser now (in localhost context), we select several options, now. If we press the button, the address-line of our browser changes - we submit the entered data to the server. Nice.


## Processing the input

We want to process this input now. Therefore, we put some PHP to the top of the page. This PHP is supposed to check if the button was pressed. To do that, we process the information that was delivered via the $_GET array (the form data should be stored here...).

```
<?php
  if(isset($_GET['btn-save'])){
    // here, we will put the save-operations...
    // but we can just output some stuff that was sent to our page...
    echo $_GET['phrase1'];
  }
?>
```

` isset() ` checks if a variable is set or not. 

` echo $_GET['phrase_01'] ` writes the content that has been transmitted to the page and that is stored in the GET-variables 'phrase_01' field to the screen. 


## Save the input to a file
We can simply store all the information to a file now.
We define a variable called $filename and a variable called $text - this variable is supposed to contain all the text and contains of the two elements that are delivered via the $_GET parameter.  
```
    $filename = "file.txt";
    $text = $_GET['phrase_01'] . " " . $_GET['phrase_02'];
    file_put_contents($filename, $text);

```

` file_put_contents($filename, $text) ` stores data that is stored in variable $text in a file called $filename. This file is located in the same folder as the php-file.

If you submit the form now, you should be able to see a new file that contains the information that you just selected. Check if the file was written and open it with a text editor. It should contain all the submitted information, now.

In case, you encounter problems, you might want to check the permissions (Right-click the folder and check if the webserver has write-access...).

Unfortunately, the information that we want to write is URL-encoded (contains strange %20s). We want to store the information in a different style, so we have to perform an URL-decode operation:

```
$text = urldecode($text);
```

## Reading the phrases from a file

We add the following code to the top of the file in order to read the contents of the file:

```
<?php
  $filename = "file.txt";
  $text = file_get_contents($filename);
?>
```

This piece of code assumes that the file called "file.txt" exists. If this is not the case, we'll get an error. 

In the HTML-Part, we just echo the content of $text in order to put the file content to the right place:

<?php echo $text ?>

## Adding more phrases.

In case we want to add new phrases, we can just change the way PHP stores data to the file. Right now, it overwrites the file, whenever we submit the form. By adding the parameter FILE_APPEND to the function call in index.php, new content will be added at the end of the file $filename.

```
    file_put_contents($filename, $text, FILE_APPEND);
```

Check it out...

If you open the file, you will notice that the whole content is written in one line. This is probably not what we want. In order to create multiple lines, we first add a line end to each of the new phrases we enter. This is done by adding `"\n"` to the string we want to put in the file. In file.txt, we already see the difference - but not in the HTML-file. There are multiple ways to fix this. The easiest is to just put a `<pre>` tag around the text. It looks not so beautiful.  

## Reading the content line by line

What we want to do, is to read the content line by line. In phrase_list.php, we replace `file_put_contents()` with `file()`.

In the head, we put:

```
  $statements = file($filename, FILE_IGNORE_NEW_LINES);
```

` file() ` returns the contents of the file $filename as an array. Each line is stored in a separate array-element ` ($statement[0] => "Line 1", $statement[1] => "Line 2") `. 

In the body, we need to loop through the statements-array, now:
```
    <?php
    foreach ($statements as $stmt){
        echo "<p>". $stmt . "</p>";
    }
    ?>
```

By using the foreach statement we loop through the $statements array. On each iteration, the value of the current element of $statement is assigned to $stmt and the internal array pointer is advanced by one (so on the next iteration, you'll be looking at the next element). In the loop, we can access the current array element by using the $stmt variable.

## Create your first config file

We want to put the name of the file that we use to store the information into a centralized file. What we will do, is create a new file called `config.php`. We will use that file in every other file in order to store centralized information. For now, we only put the following information into that file:

```
<?php
  // name of the file that we store data to
  // we want to use this information in different files 
  $filename = "file.txt";
?>

```

We replace the name that determines the filename in our other files and import the config-file:

```
include('config.php');
```

By putting that line to the very top, we make sure the contents is read (and thus, $filename is defined) before we execute any other code. 


## Using a database instead of a file

But what, if we would love to only show one phrase? What if we would like to edit a phrase? Or add some personal text? We could do this with the help of our text file. But we can also use a database. This makes many things easier... 

We use MariaDB to store our data. We can access this DB with the help of PHP. We'll 'send SQL-statements from PHP to our database'. In that way, we can create, modify and delete tables, and also insert, update, delete and read data from these tables. 

## Setup database

Open PhpMyAdmin on your machine (http://localhost/phpmyadmin/). In the sidebar on the lefthand-side, click "New". Create a database. Give it some name (e.g. "phrases" ...

Now, create a table that stores all the phrases. To begin, we only want to store the phrases and an email-address. Each phrase also gets an ID that helps us to identify the right phrase later. Put this code in the SQL-Textarea of PHPMyadmin (Tab "SQL" on mainpage of the database).

```
CREATE TABLE `phrases` (
  `id` int(11) NOT NULL,
  `phrase` text NOT NULL,
  `recipient` varchar(50) NOT NULL
);
ALTER TABLE `phrases`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `phrases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

```

This SQL-script creates a new table called 'phrases' with the fields id, phrase, and recpipent. It also modifies the table (ALTER TABLE) and defines an automatically incrementing primary key. Primary keys are used to identify an entry in our table. 

## Put in some sample phrases

You can either go to "Insert" and enter some sample phrases, or you can use the following statement:

```
INSERT INTO `phrases` (`id`, `phrase`, `recipient`) 
VALUES	(NULL, 'exploring something new about my world', ''), 
		(NULL, 'finding to be brave from time to time', 'email@example.com');
```
We now have a very basic database that we can work with...

## Accessing the phrases

We will now use the database instead of reading from the textfile.

We will remove the text-file specific code and replace with some database specific code. Before doing anything else, we want to connect to the database.
Replace this code
```
    // replace this
    $statements = file($filename, FILE_IGNORE_NEW_LINES);
```
with this one:
```
  $link = mysqli_connect("localhost", "root", "", "i2_20s__phrases");

```

`localhost` is the DB-host, `root` is the DB-User, the Password is empty in that case (`""`). The database is called `i2_20s_phrases`.

---
NOTE: 

If you have issues when trying to connect or with any database queries, you can display the MySQL error via the following code:

```
echo mysqli_error($link);

``` 

---


Now, we want to read the phrases from the database. To read them, we use the statement `SELECT * FROM phrases`.

Replace the following code:
```
    foreach ($statements as $stmt){
        echo "<p>". $stmt . "</p>";
    }
```

... with this snippet:
```
  $stmt = "SELECT * FROM `phrases`";
  $result = $link->query($stmt);

  if ($result->num_rows > 0){
    while ($row = mysqli_fetch_row($result)){
        echo $row[0];
        echo $row[1];
    }				
  }
  else {
    // nothing found :-(
  }

```
The SQL-Statement will be executed with the help of the $link-object's query method: We tell this object (a mysqli-object) that we want to execute the statment that is stored in the $stmt variable. We then check, if we get any results. If not, we'll do nothing. If we receive results, we output column 0 and 1 ($row[0] - don't forget: counting starts at 0). $row is an array that contains all the result-data that is stored in one table row, in this case (the star in the SQL-statement indicates that we want to have all columns), we get id, phrase, recipient.

What we want to do now, is to just display our data in a table. So, we can quickly build a table around our loop and apply the bootstrap classes table and table-striped in order to make the table not too ugly...

```
    <table class="table-striped table">
        <th>ID</th>
        <th>Phrase</th>
        <?php
        $link = mysqli_connect("localhost", "root", "", "i2_20s_phrases");
        $stmt = "SELECT * FROM `phrases`";
        $result = $link->query($stmt);

        if ($result->num_rows > 0){
            while ($row = mysqli_fetch_row($result)){
            echo "<tr>\n";
            echo "<td>" . $row[0] . "</td>\n";
            echo "<td>" . $row[1] . "</td>\n";
            echo "</tr>";
            }
        }
        else {
            echo "<tr><td colspan='2'>No data found</td></tr>";
        }
        ?>
    </table>

```
I use the "echo" command to output some HTML-text. The colspan-attribute indicates that we want to have a table-cell that merges three cells....

## Putting data into the database

Of course, we don't want to enter data via PHPMyAdmin but we want the user to enter the data - as we already saw this, we will just replace writing to a file with writing to a database ...

```
      // put together the message that is to be saved. 
      $text = $_GET['phrase_01'] . " " . $_GET['phrase_02'] . " " . $_GET['phrase_03'] . "\n";
      $name = $_GET['nameField'];
      // write info to a database
      // create sql-statements
      $stmt = "INSERT INTO `phrases` (`id`, `phrase`, `name`) VALUES (NULL, '" . $text . "', '" . $name . "');";
      // execute statement
      $result = $link->query($stmt);

```


## Put DB configuration into config file

We put all the database configuration in the config file and replace the stuff at the beginning of our file. Put this code into config.php:

```
  // DB configuration
  $db_host = "localhost";
  $db_user = "root";
  $db_password = "";
  $db_name = "i2_20s_phrases";
  $link = mysqli_connect($db_host, $db_user, $db_password, $db_name);  

```
This allows us to use the database connection in different files. If we want to change anything, we only have to do this in one location.

In our PHP-File, we now replace the file_put_contents part with a database specific part. We want to send a query to the DB that saves data in the database.

```
      // put together the message that is to be saved. 
      $text = $_GET['phrase_01'] . " " . $_GET['phrase_02'] . " " . $_GET['phrase_03'] . "\n";
      $name = $_GET['nameField'];
      // write info to a database
      // create sql-statements
      $stmt = "INSERT INTO `phrases` (`id`, `phrase`, `name`) VALUES (NULL, '" . $text . "', '" . $name . "');";
      // execute statement
      $result = $link->query($stmt);


```

Git: 849e38f

<!--
## Mailing the phrase to someone

We might want to mail that phrase to somebody. What we want to do is:
- add a textfield to phrase_add.php
- use mail-function after we have stored the contents...  

This only works on a webserver - and is of course very bad style... (no error handling, open to everybody, ... not recommended to use it like that...)


```
      $mailSuccess = mail(urldecode($_GET['email']), "Our Message", $text);

```

Or with a little more details...

```
    if (isset($_GET['email'])){
      $to      = urldecode($_GET['email']);
      $subject = 'I say YES! to...';
      $message = $text;
      $headers = 'From: internet2@hdmy.de' . "\r\n" .
          'Reply-To: internet2@hdmy.de' . "\r\n" .
          'X-Mailer: PHP/' . phpversion();

      $mailSuccess = mail($to, $subject, $message, $headers);      

      /*
      // if you want to do some rudimentary error handling...   
      if (!$mailSuccess){
        echo "mail not sent";
      }
      else {
        echo "mail sent to: " . $to;
      }
      */
    }

```


We can also switch the mailing functionality on and off. We do that by adding a variable to the config file.

```
// switch that to true if you want to do the mailing stuff...
$mailfun = false;
```

We then have to check in our application if mailfun is true or false:
```
    if ($mailfun == true){
      // email related stuff...
      if (isset($_GET['email'])){
          // ....
      }
    }
```

The e-mail field should also just be displayed in case $mailfun is true:
```
        <?php
        if ($mailfun == true){
        ?>
        <div class="form-group">
            <label for="email">Send this message to:</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com">
        </div>    
        <?php    
        }
        ?>
```
-->
-


# Being hacked...

So far, we have build a pretty straight forward solution that makes it possible to enter a phrase and to display it. Try to call the index.php file with the following parameters:

```
index.php?btn-save=1&phrase_01=SomeReallyBadWordsLikeF****&phrase_02=MoreBadwords&phrase_03=SuperBadWords&nameField=Some%20Bad%20Name

(in my case that is http://localhost/i220s/index.php?btn-save=1&phrase_01=SomeReallyBadWordsLikeF****&phrase_02=MoreBadwords&phrase_03=SuperBadWords&nameField=Some%20Bad%20Name)

```
Now check the phrases_read.php

# What to do?
Of course, we want to avoid that somebody publishes bad words... We can do many things: 
- Change the form submission from GET to POST (better, but still hackable)
- Only allow certain words (Create array with bad words and compare input with these words)
- Create an approval process
- Delete inappropriate content manually

The easiest way is to manually delete inapproriate content. This can of course be achieved by simply accessing the database. If you don't have access to the database or don't want do give access to the database to your moderators/editors, a small admin interface might work ... 


# Creating an admin interface
To create an administration interface, we create a new folder called "admin". We put all the admin files into that folder. We can even password protect this folder. There is a multitude of options to do that: 
- A method that comes with your appache-server is called .htaccess. More information can be found here: http://www.htaccesstools.com/articles/password-protection/
- You can implement a user management 
- You can hardcode a password into your code

```

if (!isset($_GET['password']) || $_GET['password'] != "GEHEIM"){
	die("Passwort incorrect");
}

```

We can also use the Session-Variable in order to make sure that our users are authenticated. A session is a way to store information (in variables) that can be used across multiple pages during one browser sessin (i.e. as long as the browser is not closed). A session is started with the `session_start()` function. `$_SESSION` is an array that is globally available.

On every site that we want to protect, we include the file `protect.php` at the very top (to make sure that all functionality is only accessible when user is logged in. Header redirects have to be executed before any output is created). For `index.php` that means: 

```
<?php

  include('protect.php');
  include('../config.php');
  /// ... rest of the code follows here
```

`protect.php` containts a very small snippet of code: 

```

<?php
  // session_start() creates a session
  session_start(); 
  // check if user is authenticated by checking $_SESSION
  if (empty($_SESSION['authenticated'])){
    // redirect to login page
    header('Location: login.php');
    // exit script
    exit; 
  }

?>

```

On a special login site (`login.php`), we ask the suser to enter his password: 

```

        <h1>Please Login!</h1>
        <form method="POST">
        <input type="password" name="passwd">
        <button type="submit" class="btn btn-primary" value="1" name="btn-login">Login</button>
        </form>
```

After having submitted the password, we check if the password is correct. We put this piece of code to the top of `login.php`: 

```
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

```



# The admin dashboard: List all phrases 
Our first admin dashboard only just of the list of phrases that we already know. We simply copy "phrase_read.php" to the admin folder. We rename it to "index.php". 

If we run our script ("admin/index.php"), we will get some errors that look like: 
```
Warning: include(config.php): failed to open stream: No such file or directory in 
/Applications/XAMPP/xamppfiles/htdocs/i220s/admin/index.php on line 2

```
In order to fix that, we will have to adapt the include statements. Instead of `include('config.php');`, we include the config file in the superordinate folder `include('../config.php');`

We should now see the list of phrases again. 

# Adding a delete button
We want to create a delete button for each entry in  our database. That is why we add one more column in our while loop that loops through the sentences in our index.php: 
```
while ($row = mysqli_fetch_row($result)){
	echo "<tr>";
	echo "<td>" . $row[0] . "</td>";
	echo "<td>" . $row[1] . "</td>";
	echo "<td>" . $row[2] . "</td>";
	echo "<td><a href=''>delete</a></td>";
	echo "</tr>";
}
```

To actually delete a phrase, we will have to create an SQL-statement and execute it. The SQL-Statement will look like: `DELETE FROM ``phrases` WHERE ``ID`` = 12;`. The ID should be set dynamically (we want to delete the entry with the respective ID from the database). That means, we will have to pass it to the statement in some way.

We adapt the link of the table cell as follows: 

```
	echo "<td><a href='?delete-id=" . $row[0] . "'>delete</a></td>";

```

After reloading the page, we can click this link - it will reload the current page and submit a GET-parameter called delete-id. We can now use that delete-id. 

At the top of the index.php, we check if the delete-id parameter is set. If it is set, we create the SQL-statement and simply erase the row from our database. We put the check after the DB-connection but before the SELECT statement: 

```
  // connect to database
  $link = mysqli_connect("localhost", "root", "", "i2_20s_phrases");

  if (isset($_GET['delete-id'])){
    $db_query = "DELETE FROM `phrases` WHERE `ID` = " . $_GET['delete-id'] ;
    $delete_result = $link->query($db_query);     
    // The following line shows how many rows were delted... 
    // Can be used for error handling... 
    // echo $link->affected_rows; 
  } 

  // query database
  $result = $link->query('SELECT * FROM phrases');

```

If we click on the delete links in our list, we should be able to delete that line now... As we query the database (SELECT * FROM phrases) after having deleted the row, we will see the list without the line that we just deleted. 

# Editing data
We might not only delete data. Editing is also an option to deal with inappropriate data. E.g., we can manually replace all inappropriate words with the string "MOOO". 

Similar to adding the delete-link, we add a link to an edit page:
```
	echo "<td><a href='phrase_edit.php?edit-id=" . $row[0] . "'>edit</a></td>";

```

We have to create a page called "phrase_edit.php" now. We can use the phrase_add.php as a template (copy it to the admin folder and change the includes as desribed before). It probably makes sense to delete the PHP part at the top of the page first. 

We also replace the content in our form: We don't want to have a select menu, but a textbox. We want to make sure that the edit-id that we received from the previous page will be used to update our data. We use a hidden input field and store the value of $_GET['edit-id'] in that field.

```
            <form>
                <input type="hidden" name="edit-id" value="<?php echo $_GET['edit-id']?>" >
                <input type="text" name="phrase" value=""></input>
                <button type="submit" name="btn-save" value="1">Update</button>                
            </form>
```

After we submit the form, we want to put the value of the text field into the database. As we did in the phrase_add.php, we check, if the button was pressed. If so, we create a database-statement. This time, we need the UPDATE statement. Update has the following Syntax 

```
UPDATE table_name
SET column1=value, column2=value2,...
WHERE some_column=some_value 

```

If we put this statement into the context ouf our script, it looks like: 


```
  include('../config.php');
  // initialize variable $update_result
  $update_result = 0;
  $link = mysqli_connect("localhost", "root", "", "i2_20s_phrases");
  if (isset($_GET['btn-save'])){
    $db_query = "UPDATE `phrases` SET `text` = '" . $_GET['phrase'] . "' WHERE `ID` = " . $_GET['edit-id'] ;
     // echo $db_query; 
     $update_result = $link->query($db_query);     
  }
  
```

The script knows which dataset to update by using the edit-id that we submitted via the hidden form field.

So far, the update is a little inconvenient as we don't know the text the user entered before. In order to change that, we want to read the record with the ID edit-id and show its text in the text-field. To get that text, we query the database again and store the phrase in the variable $text: 

```
  $db_query = "SELECT * FROM `phrases` WHERE `ID` = " . $_GET['edit-id'] ;
  $result = $link->query($db_query); 
  $row = mysqli_fetch_row($result); 
  $text = $row[1];

```

We only have to set the value of the form's text field to the value of $text, now. 

```
<input type="text" name="phrase" value="<?php echo $text ?>"></input>
				
```

If our update was successful, we might want to show this to the user. We put a litte alert-div on our page: 

```
        <?php if ($update_result == 1){ ?>
          <div class="alert alert-primary" role="alert">
            Update Success! 
            <a href="index.php">Back to dashboard</a>
          </div>
        <?php } ?>
```

That's basically it. We created a phrase-generator application that can create, read, update and delete records in a database. Nice!

Git: 8f53aef

## Use Google Maps API

### Extend Phrases 

Go to PHP MyAdmin and create three fields for the phrases-table called 'address', 'lat' and 'lng'. The last two parameters will contain latitude and longitude. This data is required to display the location on the map. We get it from the Google API later.

```
ALTER TABLE `phrases` ADD `address` TEXT NOT NULL, ADD `lat` VARCHAR(15) NOT NULL, ADD `lng` VARCHAR(15) NOT NULL ;
```

Usually, we would use number-fields with lat and lng - but to make things easier, we use varchar, so we don't have to deal with spaces etc.

Now, we need some fields to enter / update the address. Latitude and longitude will be computed later, so we don't need an input field.

In index.php, we add a textfield to the form: 
```
            <div class="form-group">
              <label for="address">Address:</label>
              <input type="text" class="form-control" id="address" name="address">
            </div>   
```

We have to adapt the variable mapping and the insert statements. In index.php, this looks like: 
```
    $address = $_GET['address'];
    $lat = 0.0; 
    $lng = 0.0;

    // create SQL statement
    $sql_query = "INSERT INTO `phrases`(`username`, `password`, `email`, `address`, `lat`, `lng`) ";
    $sql_query .= "VALUES('". $username ."','" . $password . "','" . $email . "' ,'" . $address . "' ,'" . $lat . "' ,'" . $lng . "')";

```

We also change the `phrase_edit.php` in the admin folder: 

```
    $phrase = $_GET['phrase'];
    $address = $_GET['address'];
    // set lat and lng to 0 - will be updated later...
    $lat = 0.0;
    $lng = 0.0;
 
    // we create an update statement...
    $stmt = "UPDATE `phrases` SET 
        `phrases`.`phrase` = '" . $phrase . "',      
        `address` = '" . $address . "',
        `lat` = '" . $lat . "',
        `lng` = '" . $lng . "'  
        WHERE id = " . $_GET['edit-id']; 


```

We also add an address-field to the form. If an adress has been entered, we display it (`$row[3]`)

```
        <div class="form-group">
          <label for="address">Address:</label>
          <input type="text" class="form-control" id="address" name="address" value="<?php echo $row[3]?>">
        </div>
```

Git: abefd2d

## Google-Maps
What we want to do next is to display a map on our main page. We will have to do a couple of things: 
- get a Google Maps API Key in order to actually use the Google APIs
- Translate the address entered by the users into lattitude and longitude
- Embedd a map on the page
- Create Javascript with the help of PHP in order to show all phrases on the map... 

### Get an API-Key
To use the Google APIs, you need an API-key. The key can be obtained by pressing "get a key" at the top of this page:  https://developers.google.com/maps/documentation/javascript/. 


We copy the API key and store it in our config.php file (`$api_key=YOUR_API_KEY`).


### Geocode address that has been entered by the user

We will compute lat and lng with the help of the Google Maps API. Therefore, we want to create an API call that looks like: `https://maps.googleapis.com/maps/api/geocode/json?address=Nobelstr.+10%2C+Stuttgart&key=YOUR_API_KEY`. 

We have to do a little bit of cleaning of the address as no spaces are allowed in an URL. To do that, we use `urlencode($address)`. That means, after initlalizing lat and lng, we enter the following code: 
```
  $maps_url = 'https://' .
            'maps.googleapis.com/' .
            'maps/api/geocode/json' .
            '?address=' . urlencode($address) . 
			&key=' . $api_key;
```
To get the results, we use the function `file_get_contents`. The result is a JSON-Object that we want to decode it into an array to deal with it in an easy way in PHP. 
```
      $maps_url = 'https://' .
            'maps.googleapis.com/' .
            'maps/api/geocode/json' .
            '?address=' . urlencode($address) . 
            '&key=' . $api_key;
      $maps_json = file_get_contents($maps_url);
      $maps_array = json_decode($maps_json, true);

```
$maps_array is an array that we can walk through. We get the lat and lng by accessing the first element of results element. This has an element called geometry with an object called location. Location has data about lat and lon. 
```
$lat = $maps_array['results'][0]['geometry']['location']['lat'];
$lng = $maps_array['results'][0]['geometry']['location']['lng'];

```
Using the same update statement than before, we don't have to do anything special to store lat and lng in our database...  

Git: 12a15bc

### Embedd a map
There are several options to display a map. For more details check https://developers.google.com/maps/web/. We use the Javascript method. 

To embed the map, we first create a div called "map":
```
  <div id ="map" style="width: 100%; height: 400px">
  </div>
```
Then, we enter some Javascript to the bottom of the page:
```
<script>
function initMap() {
  var loc = {lat: 48.7412561, lng: 9.1008994};
  var map = new google.maps.Map(document.getElementById('map'), {
	zoom: 14,
	center: loc
  });
  var marker = new google.maps.Marker({
	position: loc,
	map: map
  });
}
</script>
<script async defer
src="https://maps.googleapis.com/maps/api/js?key=[YOUR_API_KEY]&callback=initMap">
</script>
```
This will show a map with a high zoom-level in the middle of the ocean. We have to change the lat and lng attributes of the loc-object. Just play with lat and lng and you will find out how this works... 

### Show location of all phrases on the map... 
We want display all the phrases and their locations on the map. This can be done in several ways that require creating Javascript-Output with the help of PHP. We can just use PHP to write Javascript code. And that's what we do. The required information about the location is stored database... 

In order to keep things easy to understand, we loop the database results in the header and save all information in a `$phrases` Array. After querying the database for all phrases (`"SELECT * FROM phrases"`), we store all information in an array that consists of arrays with the respective rows from the database:

```
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
      $phrase['lat'] = $row[4];       
      $phrase['lng'] = $row[5];       
      // add phrase to phrases array
      array_push($phrases, $phrase); 
    }
  }

```

We adapt the area that has been used to display the table rows accordingly:

```
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

```

Back to the part where we read data from the database into the lat/lng fields of the `$phrase` variable. There might be cases, where lattitude and longitude were not stored (e.g. because the user has not provided the information or because the location could not be found). We want to make sure the variable has a defined content, so we check if the rows are empty - if so, we set the content to 0.0. 

```
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
```

Then, we can create Javascript using PHP. We just ouptut some Javascript with the help of PHP. 
We create a Javascript array that contains the phrase, the latitude and the longitude. We add this array to the locations array. We have to do this in the HTML part of the file and before we initialize the map.
Optional: If we have stored new lines in the DB this might lead to an error. That is why replace all occurances of \n with an empty string (str_replace). Then, that looks like:

```
      echo "<script>";
      echo "var locations = new Array();\n";
      foreach ($phrases as $phrase){
        echo "locations.push(['" . str_replace("\n", "", $phrase['text']) . "', " . $phrase['lat'] . " , " . $phrase['lng'] . "]);\n";
      }
      echo "</script>"; 
    ?>

```

You can check if the script works: Displaying the source of the page should now create some Javascript like: 

```
	var locations = new Array();
	locations.push(['Sportliche Bleistifte Versicherung', 48.7415131 , 9.1010301]);
	locations.push(['Sensationelle Bleistifte Gefahr', 42.3586078 , -71.0590157]);

```

These locations can be used by our Maps Javascript. We want to create markers on the map that show the different locations and phrases. The Google Maps API provides this functionality. Calling `new google.maps.Marker()` creates a new marker. The function expects a parameter for the position, and for the map that the marker should be put on:  

```
	marker = new google.maps.Marker({
	  position: new google.maps.LatLng(lat, long),
	  map: map
	});        

```

We want to do this for all locations that are stored in the locations-array. 

```
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
          }
        }

```

If we want to be a little more crazy, we can also create an "infowindow" that is shown if a user clicks on a marker. To do that, we add an eventlistner to the marker (needs to be in the for-loop!): 

```
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

```

If we want to put the map into the header of the file, we might want to remove the padding around the header: `<header style="padding: 0px">`. We might also want to get rid of the message "Use ... to scroll and zoom", so we add one parameter to the instantiation of the map: `gestureHandling: 'greedy' `
