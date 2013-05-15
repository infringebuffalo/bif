<?php
require_once 'init.php';
connectDB();
requirePrivilege('scheduler');
require_once 'util.php';
require '../bif.php';

if (!isset($_GET['id']))
    die('no user id given');
else
    $user_id = $_GET['id'];

$info = dbQueryByID('select name,email,phone,snailmail from user where id=?',$user_id);
bifPageheader('user: ' . $info['name']);

if (hasPrivilege('scheduler'))
    {
    echo "<table>\n";
    echo "<tr><th>Name</th><td>$info[name]</td></tr>\n";
    echo "<tr><th>E-mail</th><td>$info[email]</td></tr>\n";
    echo "<tr><th>Phone</th><td>$info[phone]</td></tr>\n";
    echo "<tr><th>Address</th><td>" . multiline($info['snailmail']) . "</td></tr>\n";
    echo "</table>\n";
    }

$info = dbQueryByID('select user.name,card.role,card.email,card.phone,card.snailmail from card join user on card.userid=user.id where user.id=?',$user_id);
if ($info)
    {
    echo "<h2>Public contact info</h2>\n";
    echo "<table>\n";
    echo "<tr><th>Name</th><td>$info[name]</td></tr>\n";
    echo "<tr><th>Role</th><td>$info[role]</td></tr>\n";
    echo "<tr><th>E-mail</th><td>$info[email]</td></tr>\n";
    if ($info['phone'] != '')
        echo "<tr><th>Phone</th><td>$info[phone]</td></tr>\n";
    if ($info['snailmail'] != '')
        echo "<tr><th>Address</th><td>" . multiline($info['snailmail']) . "</td></tr>\n";
    echo "</table>\n";
    }

bifPagefooter();
?>
