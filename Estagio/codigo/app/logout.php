<?php
session_start();
session_destroy();
$_SESSION['loggedin'] = false;
header ("Location: index.php");
?>