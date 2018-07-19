<?php

 /*
 * Pre-define database credentials to be used in a connection
 */
$db_server = 'localhost';
$db_user = 'username';
$db_password = 'password';
$db_database = 'db_name';

// Establish a connection to the database
$db = new mysqli($db_server, $db_user, $db_password, $db_database) or die($db->connect_error);

// Predefine common variables like the hospital name and number.
$hos_name = 'HOSP';
$hos_number = 'asddads';

// Define the location for saving the exported files
$save_path = 'C:/Users/user/Downloads/Documents/';

?>