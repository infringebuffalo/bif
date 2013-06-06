<?php

function multiline($s)
    {
    return str_replace("\n", "<br>\n", htmlspecialchars(stripslashes($s)));
    }

// Turn a possibly incomplete URL into a valid one for an <a href=...
// This is a quick hack, which should work for the data we have, but is
// not generally correct
function completeURL($s)
    {
    if ((substr($s,0,7) == 'http://') || (substr($s,0,8) == 'https://'))
        return $s;
    else
        return 'http://' . $s;
    }
function linkedURL($s)
    {
    return '<a href="' . completeURL($s) . '"><em>' . $s . '</em></a>';
    }


function getUserID($username)
    {
    $stmt = dbPrepare('select `id` from `user` where `email`=?');
    $stmt->bind_param('s',$username);
    if (!$stmt->execute())
        die($stmt->error);
    $stmt->bind_result($id);
    if (!$stmt->fetch())
        $id = 0;
    $stmt->close();
    return $id;
    }

/* This will just return the ID of the last festival in the database */
function getFestivalID()
    {
    $stmt = dbPrepare('select `id` from `festival` order by `id` DESC limit 1');
    if (!$stmt->execute())
        die($stmt->error);
    $stmt->bind_result($id);
    if (!$stmt->fetch())
        $id = 0;
    $stmt->close();
    return $id;
    }

function dbQueryByID($query,$id)
    {
    $stmt = dbPrepare($query);
    $stmt->bind_param('i',$id);
    if (!$stmt->execute())
        die($stmt->error);
    $data = array();
    $params = array();
    $meta = $stmt->result_metadata();
    while ($field = $meta->fetch_field())
        $params[] = &$data[$field->name];
    call_user_func_array(array($stmt,'bind_result'),$params);
    if (!$stmt->fetch())
        $data = NULL;
    $stmt->close();
    return $data;
    }

function dbQueryByString($query,$str)
    {
    $stmt = dbPrepare($query);
    $stmt->bind_param('s',$str);
    if (!$stmt->execute())
        die($stmt->error);
    $data = array();
    $params = array();
    $meta = $stmt->result_metadata();
    while ($field = $meta->fetch_field())
        $params[] = &$data[$field->name];
    call_user_func_array(array($stmt,'bind_result'),$params);
    if (!$stmt->fetch())
        $data = NULL;
    $stmt->close();
    return $data;
    }

function loggedMail($addr, $subject, $body, $header)
    {
    if (mail($addr, $subject, $body, $header))
        {
        log_message("sent mail to $addr");
        return true;
        }
    else
        {
        log_message("ERROR: mail to $addr failed");
        return false;
        }
    }

function getBatch($name,$festival,$create=false,$desc='')
    {
    $stmt = dbPrepare('select id from batch where name=?');
    $stmt->bind_param('s',$name);
    $stmt->execute();
    $stmt->bind_result($id);
    if ($stmt->fetch())
        {
        $stmt->close();
        return $id;
        }
    else
        {
        $stmt->close();
        if ($create)
            return createBatch($name,$festival,$desc);
        else
            return 0;
        }
    }

function createBatch($name,$festival,$desc)
    {
    $id = newEntityID('batch');
    $stmt = dbPrepare('insert into batch (id,name,festival,description) values (?,?,?,?)');
    $stmt->bind_param('isis',$id,$name,$festival,$desc);
    $stmt->execute();
    return $id;
    }

function savePreferences()
    {
    if (isset($_SESSION['preferences']))
        $prefs = $_SESSION['preferences'];
    else
        $prefs = array();
    $prefs_ser = serialize($prefs);
    $stmt = dbPrepare('update user set preferences=? where id=?');
    $stmt->bind_param('si',$prefs_ser,$_SESSION['userid']);
    if (!$stmt->execute())
        die($stmt->error);
    $stmt->close();
    log_message('saved preferences');
    }

?>
