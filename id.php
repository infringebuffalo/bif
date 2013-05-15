<?php
require_once 'init.php';
connectDB();

if (!isset($_GET['id']))
    die('no id given');
else
    $entity_id = $_GET['id'];
$stmt = dbPrepare('select `tablename` from `entity` where `id`=?');
$stmt->bind_param('i',$entity_id);
$stmt->execute();
$stmt->bind_result($tablename);
$stmt->fetch();
$stmt->close();

header('Location: ' . $tablename . '.php?id=' . $entity_id);
?>
