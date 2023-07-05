<?php

// Database connection details
$host = "localhost"; // address of the database server
$username = "root"; //  username to access the database
$password = "";// password to access the database
$database = "cms"; // name of the  database

// Connect to the database using mysqli_connect function
$connect = mysqli_connect($host, $username, $password, $database);

// Set the character set of the database connection to UTF8
mysqli_set_charset($connect, 'UTF8');


