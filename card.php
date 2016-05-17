<?php
require_once 'init.php';
require_once 'util.php';
connectDB();

if (!isset($_GET['id']))
    errorAndQuit('no card id given');
else
    $id = $_GET['id'];

$row = dbQueryByID('select user.name,card.role,card.email,card.phone,card.snailmail from card join user on card.userid=user.id where card.id=?',$id);
bifPageheader('card: ' . $row['name']);

echo "<table>\n";
echo "<tr><th>Name</th><td>$row[name]</td></tr>\n";
echo "<tr><th>Role</th><td>$row[role]</td></tr>\n";
echo "<tr><th>E-mail</th><td>$row[email]</td></tr>\n";
if ($row['phone'] != '')
    echo "<tr><th>Phone</th><td>$row[phone]</td></tr>\n";
if ($row['snailmail'] != '')
    echo "<tr><th>Address</th><td>" . multiline($row['snailmail']) . "</td></tr>\n";
echo "</table>\n";

bifPagefooter();
?>
