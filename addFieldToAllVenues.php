<?php
/*
require_once 'init.php';
connectDB();
requirePrivilege('admin');
require_once 'util.php';
require_once 'apiFunctions.php';

bifPageheader('modifying all venues');
?>

<ul>
<?php
$venues = array();
$festival = GETvalue('festival',getFestivalID());
$stmt = dbPrepare('select `id`, `name` from `venue` where `festival`=? and deleted=0 order by name');
$stmt->bind_param('i',$festival);
$stmt->execute();
$stmt->bind_result($id,$name);
while ($stmt->fetch()) 
    {
    $venues[] = $id;
    }
$stmt->close();
foreach ($venues as $id)
    {
    echo "<li><a href='venue.php?id=$id'>$id</a></li>\n";
    addVenueInfoField($id,'Description for web');
    }
?>
</ul>

<?php
bifPagefooter();
*/
?>
