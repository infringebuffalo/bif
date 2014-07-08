<?php
require_once 'init.php';
requireLogin();
connectDB();
require_once 'util.php';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-type" content="text/html;charset=utf-8" />
<title>Buffalo Infringement Festival venues</title>
<link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>
<h2>Venues</h2>

<?php
$festival = GETvalue('festival',getFestivalID());
$stmt = dbPrepare('select name,info from venue where deleted=0 and festival=? order by name');
$stmt->bind_param('i',$festival);
$stmt->execute();
$stmt->bind_result($name,$info_ser);
while ($stmt->fetch())
    {
    $info = unserialize($info_ser);
    $address = '';
    $website = '';
    foreach ($info as $i)
        {
        if ($i[0] == 'address')
            $address = $i[1];
        else if ($i[0] == 'website')
            $website = $i[1];
        }
    echo stripslashes($name) . '<br/>';
    echo stripslashes($address) . '<br/>';
    if ($website != '') echo stripslashes($website) . '<br/>';
    echo "<br/><br/>\n";
    }
$stmt->close();
?>

</body>
</html>
