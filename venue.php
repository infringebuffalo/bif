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

$row = dbQueryByID('select name,shortname,info from venue where id=?',$id);
bifPageheader('venue: ' . $row['name']);

$data = unserialize($row['info']);

echo "<table>\n";
echo "<tr><th>Name</th><td>$row[name]</td></tr>\n";
echo "<tr><th>Short name</th><td>$row[shortname]</td></tr>\n";
foreach ($data as $k => $v)
    echo "<tr><th>$k</th><td>$v</td></tr>\n";
echo "</table>\n";

bifPagefooter();
?>
