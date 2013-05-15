<?php
require_once 'init.php';
require_once 'util.php';
require '../bif.php';
connectDB();

if (!isset($_GET['id']))
    die('no card id given');
else
    $id = $_GET['id'];

$info = dbQueryByID('select user.name,card.role,card.email,card.phone,card.snailmail from card join user on card.userid=user.id where card.id=?',$id);
bifPageheader('card: ' . $info['name']);

echo "<table>\n";
echo "<tr><th>Name</th><td>$info[name]</td></tr>\n";
echo "<tr><th>Role</th><td>$info[role]</td></tr>\n";
echo "<tr><th>E-mail</th><td>$info[email]</td></tr>\n";
if ($info['phone'] != '')
    echo "<tr><th>Phone</th><td>$info[phone]</td></tr>\n";
if ($info['snailmail'] != '')
    echo "<tr><th>Address</th><td>" . multiline($info['snailmail']) . "</td></tr>\n";
echo "</table>\n";

bifPagefooter();
?>
