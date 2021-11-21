<?php

DEFINE('ROOT_PATH', realpath(dirname(__FILE__)));
DEFINE('BASE_URL', 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));

//TODO: Change to Local host on deployment.
//DEFINE('DB_HOST', 'localhost');
/*IP Address to access the databases remotely*/
DEFINE('DB_HOST', 'localhost');
DEFINE('DB_USER', 'admin');
DEFINE('DB_PASSWORD', '123456');
DEFINE('DB_NAME', 'blogsite');
global $dbc;

//Create DB connection
$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if(!$dbc){
    die('Connection failed to database: '.mysqli_connect_error());
}
?>
