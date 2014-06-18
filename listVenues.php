<?php
require_once 'init.php';
connectDB();
requirePrivilege(array('scheduler','organizer'));
require_once 'util.php';

$header = <<<ENDSTRING
<script src="jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
 });
</script>
ENDSTRING;

bifPageheader('all venues',$header);
?>

<table>
<tr><th>name</th><th>venue sheet</th><th>venue sign</th></tr>
<?php
$festival = GETvalue('festival',getFestivalID());
$stmt = dbPrepare('select `id`, `name` from `venue` where `festival`=? and deleted=0 order by name');
$stmt->bind_param('i',$festival);
$stmt->execute();
$stmt->bind_result($id,$name);
while ($stmt->fetch()) 
    {
    if ($name == '')
        $name = '!!NEEDS A NAME!!';
    echo "<tr>\n";
    echo "<td><a href='venue.php?id=$id'>$name</a></td>\n";
    echo "<td><a href='venuesheet.php?id=$id'>sheet</a></td>\n";
    echo "<td><a href='venuesign.php?id=$id'>sign</a></td>\n";
    echo "</tr>\n";
    }
$stmt->close();
?>
</table>

<h2>Deleted venues</h2>
<table>
<?php
$stmt = dbPrepare('select `id`, `name` from `venue` where `festival`=? and deleted=1 order by name');
$stmt->bind_param('i',getFestivalID());
$stmt->execute();
$stmt->bind_result($id,$name);
while ($stmt->fetch()) 
    {
    if ($name == '')
        $name = '!!NEEDS A NAME!!';
    echo "<tr><td><a href='venue.php?id=$id'>$name</a></td></tr>\n";
    }
$stmt->close();
?>
</table>

<?php
bifPagefooter();
?>
