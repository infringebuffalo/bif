<?php
require_once 'init.php';
connectDB();
requirePrivilege(array('scheduler','organizer'));
require_once 'util.php';
require '../bif.php';

if (!isset($_GET['id']))
    die('no venue id given');
else
    $id = $_GET['id'];

$header = <<<ENDSTRING
<script src="jquery-1.9.1.min.js" type="text/javascript"></script>
<script type="text/javascript">
function showEditor(name)
    {
    $('#show_' + name).hide();
    $('#edit_' + name).show();
    }
function hideEditor(name)
    {
    $('#show_' + name).show();
    $('#edit_' + name).hide();
    }

$(document).ready(function() {
    $('.edit_info').hide();
 });
</script>
<link rel="stylesheet" href="style.css" type="text/css" />
ENDSTRING;


$row = dbQueryByID('select name,shortname,info,deleted from venue where id=?',$id);
bifPageheader('venue: ' . $row['name'],$header);

if ($row['deleted'])
    echo "<span><form method='POST' action='api.php'><input type='hidden' name='command' value='undeleteVenue' /><input type='hidden' name='id' value='$id' /><input type='submit' value='undelete venue' /></form></span>";
else
    echo "<span><form method='POST' action='api.php'><input type='hidden' name='command' value='deleteVenue' /><input type='hidden' name='id' value='$id' /><input type='submit' value='delete venue' /></form></span>";

$info = unserialize($row['info']);

echo "<table>\n";
echo "<tr><th>Name</th><td>$row[name]</td></tr>\n";
echo "<tr><th>Short name</th><td>$row[shortname]</td></tr>\n";
foreach ($info as $fieldnum=>$v)
    {
    echo "<tr id='edit_field$fieldnum' class='edit_info'><th>$v[0]</th><td><form method='POST' action='api.php'><input type='hidden' name='command' value='changeVenueInfo' /><input type='hidden' name='venue' value='$id' /><input type='hidden' name='fieldnum' value='$fieldnum' /><textarea name='newinfo' cols='80'>$v[1]</textarea><input type='submit' name='submit' value='update'><button onclick='hideEditor(\"field$fieldnum\"); return false;'>cancel</button></td></form></tr>\n";
    echo "<tr id='show_field$fieldnum' class='show_info' onclick='showEditor(\"field$fieldnum\");'><th>$v[0]</th><td>" . multiline($v[1]) . "</td></tr>\n";
    }
echo "</table>\n";

bifPagefooter();
?>
