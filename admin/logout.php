<?php
session_start();
session_destroy();

// Redirect to login page from admin folder
header("Location: ../login.php");
exit();
?>