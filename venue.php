<?php
require_once 'init.php';
connectDB();
requireLogin();
require_once 'util.php';
require '../bif.php';

if (!isset($_GET['id']))
    die('no venue id given');
else
    $id = $_GET['id'];

$info = dbQueryByID('select name,shortname,info from venue where id=?',$id);
bifPageheader('venue: ' . $info['name']);

$data = unserialize($info['info']);

echo "<table>\n";
echo "<tr><th>Name</th><td>$info[name]</td></tr>\n";
echo "<tr><th>Short name</th><td>$info[shortname]</td></tr>\n";
foreach ($data as $k => $v)
    echo "<tr><th>$k</th><td>$v</td></tr>\n";
echo "</table>\n";

bifPagefooter();
?>
