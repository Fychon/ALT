<?php
error_reporting(E_ALL);

$db_server = getenv("MYSQL_SERVER");
$db_database = getenv("MYSQL_DATABASE");
$db_username = getenv("MYSQL_USERNAME");
$db_userpass = getenv("MYSQL_USERPASS");

$link = new mysqli($db_server, $db_username, $db_userpass, $db_database);
?>