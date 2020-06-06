<?php
defined("ABSPATH") or die("");
/* 
Use this script to test a database connection on your server.
When the installer tries to connect to the database it does so with a very simple PHP function mysqli_connect. If you believe all the parameters your
entering in the installer are correct, you can validate them by adding this file in the same directory as the installer the follow these steps:

1. Fill out the parameters below to match your enviroment
2. Open this file in a web browser http://yoursite.com/db-test.php
3. A status message will return if the values are correct
*/
	
$link = mysqli_connect("127.0.0.1", "my_user", "my_password", "my_db");

if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}

echo "Success: A proper connection to MySQL was made! The my_db database is great." . PHP_EOL;
echo "Host information: " . mysqli_get_host_info($link) . PHP_EOL;

mysqli_close($link);
?>
