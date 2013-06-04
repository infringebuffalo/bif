<?php
$db = false;
date_default_timezone_set('America/New_York');

/* THIS MUST BE FIXED LATER */
$festivalStartDate = mktime(0,0,0, 7, 25, 2013);
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

?>
