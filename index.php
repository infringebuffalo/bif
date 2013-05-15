<?php
require_once 'init.php';
require_once 'util.php';
require '../bif.php';
connectDB();

bifPageheader('proposals database');

if (loggedIn())
    {
    include 'homepage.php';
    }
else
    {
    include 'loginForm.php';
    }

bifPagefooter();
?>
