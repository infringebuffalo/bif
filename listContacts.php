<?php
require_once 'init.php';
connectDB();
requirePrivilege(array('confirmed','scheduler','organizer','admin'));
require_once 'util.php';

bifPageheader('festival contacts');
?>
<table>
<tr><th>role</th><th>name</th><th>e-mail</th></tr>
<?php
$festival = getFestivalID();
$stmt = dbPrepare('select user.id, name, email, role, description from user join contact on user.id=contact.userid where festival=? order by role');
$stmt->bind_param('i',$festival);
$stmt->execute();
$stmt->bind_result($id,$name,$email,$role,$description);
while ($stmt->fetch())
    {
    echo "<tr><td>$role: $description</td><td><a href='user.php?id=$id'>$name</a></td><td>$email</td></tr>\n";
    }
$stmt->close();
/*
$stmt = dbPrepare('select user.id, name, email, role, description from user join contact on user.id=contact.userid where festival=?');
$stmt->bind_param('i',$festival);
$stmt->execute();
$stmt->bind_result($id,$name,$email,$role,$description);
$people = array();
while ($stmt->fetch()) 
    {
    if ($name == '')
        $name = '!!NEEDS A NAME!!';
    if (!array_key_exists($id,$people))
        $people[$id] = array("name"=>$name,"email"=>$email,"role"=>array());
    $people[$id]["role"][] = "$role: $description";
    }
$stmt->close();
asort($people);
foreach ($people as $id=>$data)
    {
    echo "<tr><td><a href='user.php?id=$id'>$data[name]</a></td><td>$data[email]</td><td>";
    echo implode("<br>",$data['role']);
    echo "</td></tr>\n";
    }
*/
?>
</table>
<?php
bifPagefooter();
?>
