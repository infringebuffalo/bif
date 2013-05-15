<?php
require_once 'init.php';

if (loggedIn())
    {
    session_destroy();
    session_start();
    }

connectDB();

function getAdminPrefs()
    {
/*
    global $db, $dbPrefix;
    $stmt = dbPrepare("select listtags,claimedshowsonly from adminPrefs where admin=?");
    if (!$stmt) die("Database error: " . $db->error);
    $stmt->bind_param('i',$_SESSION['userid']);
    $stmt->execute();
    $stmt->bind_result($tagslist,$claimedshowsonly);
    $stmt->fetch();
    $_SESSION['prefs_claimedshowsonly'] = $claimedshowsonly;
    $stmt->close();
    if ((isset($tagslist)) && ($tagslist != ''))
        $_SESSION['prefs_tags'] = unserialize($tagslist);
    else
        {
        $stmt = dbPrepare("select tag from {$dbPrefix}_tags");
        $stmt->execute();
        $stmt->bind_result($t);
        $tags = array();
        while ($stmt->fetch())
            $tags[] = $t;
        $stmt->close();
        $_SESSION['prefs_tags'] = $tags;
        }
*/
    }

$username = POSTvalue('username');
$postedpassword = md5(POSTvalue('password'));

$stmt = dbPrepare('select id,password,newpassword,privs from user where email=?');
$stmt->bind_param('s',$username);
$stmt->execute();
$stmt->bind_result($userid,$password,$newpassword,$privs);
if ($stmt->fetch())
    {
    $stmt->close();
    if (($postedpassword == $password) || (($newpassword != '') && ($postedpassword == $newpassword)))
        {
        $_SESSION['userid'] = $userid;
        $_SESSION['username'] = htmlentities($username);
        $_SESSION['privs'] = $privs;
        if ($privs != '')
            getAdminPrefs();
        log_message($username . ' logged in');
        }
    else
        {
        $_SESSION['loginError'] = 'Login failed';
        log_message($username . ' login failed: wrong password');
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
