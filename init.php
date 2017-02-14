<?php
ini_set('session.gc_maxlifetime', 864000);
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8'); 
mb_language('uni');
mb_regex_encoding('UTF-8');
session_start();

$STARTTIME = microtime(TRUE);

$db = false;
date_default_timezone_set('America/New_York');

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

function REQUESTvalue($field,$default='')
    {
    if (isset($_REQUEST[$field]))
        {
        if (get_magic_quotes_gpc()) return stripslashes($_REQUEST[$field]);
        else return $_REQUEST[$field];
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
    $stmt = $db->prepare("set names 'utf8'");
    if (!$stmt)
        die('Database error: ' . $db->error);
    if (!$stmt->execute())
        die('Database error: ' . $stmt->error);
    $stmt->close();
    }

function dbPrepare($sql)
    {
    global $db;
    $stmt = $db->prepare($sql);
    if (!$stmt)
        errorAndQuit('Database error: ' . $db->error,true);
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
        $stmt = $db->prepare("insert into log (`userid`, `ip`, `proposal`, `is_sql`, `message`) values (?,?,?,?,?)");
        if (!$stmt)
            die('Database error: ' . $db->error);
        if (array_key_exists('username',$_SESSION))
            $userid = $_SESSION['userid'];
        else
            $userid = 0;
        $stmt->bind_param('isiis',$userid,$_SERVER['REMOTE_ADDR'],$proposalid,$is_sql,$m);
        if (!$stmt->execute())
            die('Database error: ' . $stmt->error);
        $stmt->close();
        }
    }

function newEntityID($tablename)
    {
    global $db;
    $stmt = dbPrepare('insert into `entity` (`tablename`) values (?)');
    $stmt->bind_param('s',$tablename);
    if (!$stmt->execute())
        errorAndQuit("Database error: " . $stmt->error,true);
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
        log_message("lacks " . implode("/",$priv) . " privilege - $reason - url " . currentURL() . "; referer $_SERVER[HTTP_REFERER]");
    else
        log_message("lacks $priv privilege - $reason - url " . currentURL() . "; referer $_SERVER[HTTP_REFERER]");
    $_SESSION['LOGIN_RETURN_PAGE'] = currentURL();
    header('Location: .');
    die();
    }

function hasPrivilege($priv)
    {
    global $userPrivs, $db;
    if (!loggedIn())
        return false;
    if ($db == false)
        connectDB();
    if ($userPrivs === false)
        {
        $stmt = dbPrepare('select privs_json from user where id=?');
        $stmt->bind_param('i',$_SESSION['userid']);
        if (!$stmt->execute())
            die('Database error: ' . $stmt->error);
        $stmt->bind_result($privs_json);
        $stmt->fetch();
        $stmt->close();
        $userPrivs = json_decode($privs_json,true);
        }
    if (privsArrayIncludes($userPrivs, $priv))
        return true;
    return false;
    }

function privsArrayIncludes($userPrivs, $priv, $festival=-1)
    {
    if (!is_array($userPrivs))
        return false;
    if ($festival == -1)
        $festival = getFestivalID();
    if (!is_array($priv))
        $priv = array($priv);
    foreach ($priv as $p)
        {
        if ((array_key_exists(0,$userPrivs)) && (in_array($p,$userPrivs[0])))
            return true;
        else if ((array_key_exists($festival,$userPrivs)) && (in_array($p,$userPrivs[$festival])))
            return true;
        }
    return false;
    }

/* This will just return the ID of the last festival in the database */
function getFestivalID()
    {
    static $id=-1;
    if ($id == -1)
        {
        $stmt = dbPrepare('select `id` from `festival` order by `id` DESC limit 1');
        if (!$stmt->execute())
            die('Database error: ' . $stmt->error);
        $stmt->bind_result($id);
        if (!$stmt->fetch())
            $id = 0;
        $stmt->close();
        }
    return $id;
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
echo "\n" . $headerExtras;
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
if ((array_key_exists('adminmessage',$_SESSION)) && ($_SESSION['adminmessage'] != ''))
    {
    echo '<div style="background:#ffff80; padding:0.5em; width:790px;">' . $_SESSION['adminmessage'] . '</div>';
    unset($_SESSION['adminmessage']);
    }
}


function bifPagefooter($showTiming=false)
{
if ($showTiming)
    {
    global $STARTTIME;
    $ENDTIME = microtime(TRUE);
    $t = $ENDTIME - $STARTTIME;
    echo "<p style='font-size:75%'>page took $t seconds</p>";
    }
echo <<<ENDSTRING
</body>
</html>
ENDSTRING;
}
?>
