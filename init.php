<?php
session_start();

require_once 'init-basic.php';

$userPrivs = false;

function loggedIn()
    {
    return ((array_key_exists('userid',$_SESSION)) && ($_SESSION['userid'] > 0));
    }

function requireLogin()
    {
    if (!loggedIn())
        {
        header('Location: .');
        die();
        }
    }

function requirePrivilege($priv)
    {
    if (hasPrivilege($priv))
        return;
    if (is_array($priv))
        log_message('lacks ' . $priv[0] . ' (or other) privilege - ' . $_SERVER['HTTP_REFERER']);
    else
        log_message('lacks ' . $priv . ' privilege - ' . $_SERVER['HTTP_REFERER']);
    header('Location: .');
    die();
    }

function hasPrivilege($priv)
    {
    if (!loggedIn())
        return false;
    if (!isset($db))
        connectDB();
    global $userPrivs,$db;
    if ($userPrivs === false)
        {
        $stmt = dbPrepare('select privs from user where id=?');
        $stmt->bind_param('i',$_SESSION['userid']);
        if (!$stmt->execute())
            die($stmt->error);
        $stmt->bind_result($userPrivs);
        $stmt->fetch();
        $stmt->close();
        }
    if (is_array($priv))
        {
        foreach ($priv as $p)
            if (stripos($userPrivs,'/' . $p . '/') !== false)
                return true;
        }
    else
        {
        if (stripos($userPrivs,'/' . $priv . '/') !== false)
                return true;
        }
    return false;
    }

?>
