<?php
require_once 'init.php';
connectDB();
requirePrivilege(array('scheduler','organizer'));
require_once 'util.php';
require_once 'scheduler.php';

bifPageheader('venue locations');
?>

<table>
<tr><th width='10%'>name</th><th>lat/lon</th><th width='20%'>address</th><th>new lat/lon</th></tr>
<?php
function getInfo($info,$field)
    {
    foreach ($info as $i)
        if (is_array($i) && array_key_exists(0,$i) && (strcasecmp($i[0],$field)==0))
            return $i[1];
    return '';
    }


$festival = GETvalue('festival',getFestivalID());
$stmt = dbPrepare('select `id`, `name`, info from `venue` where `festival`=? and deleted=0 order by name');
$stmt->bind_param('i',$festival);
$stmt->execute();
$stmt->bind_result($id,$name,$info_ser);
while ($stmt->fetch()) 
    {
    $info = unserialize($info_ser);
    if ($name == '')
        $name = '!!NEEDS A NAME!!';
    echo "<tr>\n";
    echo "<td><a href='venue.php?id=$id'>$name</a></td>\n";
    $lat = getInfo($info,'latitude');
    $lon = getInfo($info,'longitude');
    $addr = getInfo($info,'address');
    echo "<td>$lat, $lon</td>\n";
    echo "<td><a target='_blank' href='https://www.google.com/maps/place/" . urlencode($addr) . "/'>$addr</a></td>\n";
    echo "<td>";
    echo beginApiCallHtml('setLatLon', array('venue'=>$id));
    echo "<input type='text' size='20' name='latlon'>";
    echo endApiCallHtml('submit');
    echo "</td>\n";
    echo "</tr>\n";
    }
$stmt->close();
?>
</table>

<?php
bifPagefooter();
?>
