<?php
ini_set('session.gc_maxlifetime', 864000);
session_start();

$db = false;
date_default_timezone_set('America/New_York');

/* THIS MUST BE FIXED SOME DAY!!!! */
$festivalStartDate = mktime(0,0,0, 7, 28, 2016);
$festivalNumberOfDays = 11;

function POSTvalue($field,$default='')
    {
    if (isset($_POST[$field]))
        {
        if (get_magic_quotes_gpc()) return stripslashes($_POST[$field]);
        else return $_POST[$field];
        }
    else
        return $default;
    }

function GETvalue($field,$default='')
    {
    if (isset($_GET[$field]))
        {
        if (get_magic_quotes_gpc()) return stripslashes($_GET[$field]);
        else return $_GET[$field];
        }
    else
        return $default;
    }

function connectDB()
    {
    require 'config/config.php';
    global $db;
    if ($db !== false)
        return;
    $db = new mysqli($dbhost, $dbusername, $dbpassword, $dbdatabase);
    if ($db->connect_errno)
        die('Database error: ' . $db->connect_error);
    $stmt = dbPrepare("set names 'utf8'");
    if (!$stmt->execute())
        die($stmt->error);
    $stmt->close();
    }

function dbPrepare($sql)
    {
    global $db;
    $stmt = $db->prepare($sql);
    if (!$stmt)
        die('Database error: ' . $db->error);
    return $stmt;
    }

function bindPreparedResults($stmt)
    {
    $data = array();
    $params = array();
    $meta = $stmt->result_metadata();
    while ($field = $meta->fetch_field())
        $params[] = &$data[$field->name];
    call_user_func_array(array($stmt,'bind_result'),$params);
    return $data;
    }

function log_message($m,$proposalid=0,$is_sql=0)
    {
    global $db;
    if (!$db)
        {
        if ($is_sql)
            $f = fopen('sqllog.txt', 'a');
        else
            $f = fopen('log.txt', 'a');
        if ($f)
            {
            if (array_key_exists('username',$_SESSION)) $n = sprintf('%s (%s)',$_SESSION['username'],$_SERVER['REMOTE_ADDR']);
            else $n = $_SERVER['REMOTE_ADDR'];
            fwrite($f, strftime('%D %T') . ' ' . $n . ': ' . $m . "\n");
            fclose($f);
            }
        }
    else
        {
        $stmt = dbPrepare("insert into log (`userid`, `ip`, `proposal`, `is_sql`, `message`) values (?,?,?,?,?)");
        if (array_key_exists('username',$_SESSION))
            $userid = $_SESSION['userid'];
        else
            $userid = 0;
        $stmt->bind_param('isiis',$userid,$_SERVER['REMOTE_ADDR'],$proposalid,$is_sql,$m);
        if (!$stmt->execute())
            die($stmt->error);
        $stmt->close();
        }
    }

function newEntityID($tablename)
    {
    global $db;
    $stmt = dbPrepare('insert into `entity` (`tablename`) values (?)');
    $stmt->bind_param('s',$tablename);
    if (!$stmt->execute())
        die($stmt->error);
    $stmt->close();
    return $db->insert_id;
    }


$userPrivs = false;

function loggedIn()
    {
    return ((array_key_exists('userid',$_SESSION)) && ($_SESSION['userid'] > 0));
    }

function requireLogin()
    {
    if (!loggedIn())
        {
        $_SESSION['LOGIN_RETURN_PAGE'] = currentURL();
        header('Location: .');
        die();
        }
    }

function requirePrivilege($priv,$reason='')
    {
    if (hasPrivilege($priv))
        return;
    if (is_array($priv))
        log_message("lacks $priv[0] (or other) privilege - $reason - url " . currentURL() . "; referer $_SERVER[HTTP_REFERER]");
    else
        log_message("lacks $priv privilege - $reason - url " . currentURL() . "; referer $_SERVER[HTTP_REFERER]");
    $_SESSION['LOGIN_RETURN_PAGE'] = currentURL();
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


function currentURL()
    {
    if ($_SERVER['HTTPS'] == 'on')
        $pageURL = 'https://';
    else
        $pageURL = 'http://';
    $pageURL .= $_SERVER['SERVER_NAME'];
    if ($_SERVER['SERVER_PORT'] != '80')
        $pageURL .= ':' . $_SERVER['SERVER_PORT'];
    $pageURL .= $_SERVER['REQUEST_URI'];
    return $pageURL;
    }


function bifPageheader($title,$headerExtras='')
{
if ($title != '')
    $titleprefix = $title . ' |';
else
    $titleprefix = '';
echo <<<ENDSTRING
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<link type="text/css" rel="stylesheet" href="style.css" />
<title>$titleprefix Buffalo Infringement Festival</title>
ENDSTRING;
echo $headerExtras;
echo <<<ENDSTRING
</head>
<body>
<div class="menubar">
 <table class="menubar">
 <tr>
 <td>Buffalo Infringement database</td>
 <td> : </td>
 <td><a href="." title="" class="active">Proposals</a></td>
 <td> | </td>
 <td><a href="/contact.php" title="" class="active">Contact</a></td>
 <tr>
 </table>
</div>
<h1 style="background-color: #eec; border-radius: 2em">
$title
</h1>
ENDSTRING;
}


function bifPagefooter()
{
echo <<<ENDSTRING
</body>
</html>
ENDSTRING;
}
?>
