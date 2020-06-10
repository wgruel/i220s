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

` echo $_GET['phrase1'] ` writes the content that has been transmitted to the page and that is stored in the GET-variables 'phrase1' field to the screen. 


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

We will create a new file to read all the phrases: phrasesList.php.

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

In case we want to add new phrases, we can just change the way PHP stores data to the file. Right now, it overwrites the file, whenever we submit the form. By adding the parameter FILE_APPEND to the function call in phrases_add.php, new content will be added at the end of the file $filename.

```
    file_put_contents($filename, $text, FILE_APPEND);
```

Check it out...

If you open the phrase_list.php file, you will notice that the whole content is written in one line. This is probably not what we want. In order to create multiple lines, we first add a line end to each of the new phrases we enter. This is done by adding `"\n"` to the string we want to put in the file. In file.txt, we already see the difference - but not in the HTML-file. There are multiple ways to fix this. The easiest is to just put a `<pre>` tag around the text. It looks not so beautiful.  

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

## Put DB configuration into config file

We put all the database configuration in the config file and replace the stuff at the beginning of phrases_list. Put this code into config.php:

```
  // DB configuration
  $db_host = "localhost";
  $db_user = "root";
  $db_password = "";
  $db_name = "i2_20s_phrases";
  $link = mysqli_connect($db_host, $db_user, $db_password, $db_name);  

```
This allows us to use the database connection in different files. If we want to change anything, we only have to do this in one location.

In phrase_add.php, we now replace the file_put_contents part with a database specific part. We want to send a query to the DB that saves data in the database.


