<?php
require_once 'init.php';
connectDB();
log_message($_SESSION['username'] . ' logged out');
session_destroy();
session_start();
header('Location: index.php');
?>
