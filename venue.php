<?php
require_once 'init.php';
connectDB();
requireLogin();
//requirePrivilege(array('scheduler','organizer'));
require_once 'util.php';
require_once 'scheduler.php';
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
function showScheduler(name)
    {
    $('.scheduleForm').hide()
    $(name).show()
    }

$(document).ready(function() {
    $('.edit_info').hide();
 });
</script>
<link rel="stylesheet" href="style.css" type="text/css" />
ENDSTRING;


$venueinfo = dbQueryByID('select name,shortname,info,deleted from venue where id=?',$id);
bifPageheader('venue: ' . $venueinfo['name'],$header);

function calEntry($day)
    {
    global $info;
    return '<input type="checkbox" name="' . dayToDate($day) . '" value="1"/>';
    }

function calEntry2($day)
    {
    global $info;
    return '<input type="checkbox" name="' . dayToDate($day) . '" value="1" checked/>';
    }

if (hasPrivilege('scheduler'))
    {
    echo <<<ENDSTRING
<div class="schedulebox">
Scheduling:<br/>
<a href="" id="scheduleEventAnchor" onclick="showScheduler('#scheduleEventForm'); return false">performance</a>&nbsp;
<a href="" id="scheduleInstallationAnchor" onclick="showScheduler('#scheduleInstallationForm'); return false">installation</a>&nbsp;
ENDSTRING;
    echo scheduleEventForm('venue.php?id=' . $id, 'calEntry', 0, $id);
    echo scheduleInstallationForm('venue.php?id=' . $id, 'calEntry2', 0, $id);
    echo "</div>\n";
    }


$dayshows = array();
$dayinst = array();
for ($i=0; $i < $festivalNumberOfDays; $i++)
    {
    $dayshows[dayToDate($i)] = array();
    $dayinst[dayToDate($i)] = array();
    }
getDatabase();
foreach ($venueList[$id]->listings as $l)
    {
    if ($l->installation)
        $dayinst[$l->date][] = sortingKey($l->proposal->title) . listingRow($l->id,false,false,false,true,true,'','<td>'.stripslashes($l->venuenote).'</td>');
    else
        $dayshows[$l->date][] = sortingKey($l->starttime) . listingRow($l->id,false,true,false,true,true,'','<td>'.stripslashes($l->venuenote).'</td>');
    }
echo "<div class=\"schedulebox\">\nSchedule:\n<table>\n";
echo "<thead><tr><th>day</th><th>performances</th><th>installations</th></tr></thead>\n";
echo "<tbody>\n";
for ($i=0; $i < $festivalNumberOfDays; $i++)
    {
    $date = dayToDate($i);
    echo '<tr><td>' . dateToString($date,true) . '</td>';
    sort ($dayshows[$date]);
    echo '<td><table>';
    foreach ($dayshows[$date] as $row)
        echo $row;
    echo '</table></td>';
    sort ($dayinst[$date]);
    echo '<td><table>';
    $odd = true;
    foreach ($dayinst[$date] as $row)
        {
        if ($odd) echo '<tr class="oddrow">';
        else echo '<tr>';
        $odd = ! $odd;
        echo $row . '</tr>';
        }
    echo '</table></td>';
    echo '</tr>';
    }
echo "</tbody>\n</table>\n</div>\n";

if ($venueinfo['deleted'])
    echo "<span><form method='POST' action='api.php'><input type='hidden' name='command' value='undeleteVenue' /><input type='hidden' name='id' value='$id' /><input type='submit' value='undelete venue' /></form></span>";
else
    echo "<span><form method='POST' action='api.php'><input type='hidden' name='command' value='deleteVenue' /><input type='hidden' name='id' value='$id' /><input type='submit' value='delete venue' /></form></span>";

$info = unserialize($venueinfo['info']);

echo "<table>\n";

echo "<tr id='edit_fieldName' class='edit_info'><th>Name</th><td><form method='POST' action='api.php'><input type='hidden' name='command' value='changeVenueName' /><input type='hidden' name='venue' value='$id' /><textarea name='newinfo' cols='80'>$venueinfo[name]</textarea><input type='submit' name='submit' value='save'><button onclick='hideEditor(\"fieldName\"); return false;'>don't edit</button></td></form></tr>\n";
echo "<tr id='show_fieldName' class='show_info' onclick='showEditor(\"fieldName\");'><th>Name</th><td>$venueinfo[name]</td></tr>\n";

echo "<tr id='edit_fieldShortname' class='edit_info'><th>Short name</th><td><form method='POST' action='api.php'><input type='hidden' name='command' value='changeVenueShortname' /><input type='hidden' name='venue' value='$id' /><textarea name='newinfo' cols='80'>$venueinfo[shortname]</textarea><input type='submit' name='submit' value='save'><button onclick='hideEditor(\"fieldShortname\"); return false;'>don't edit</button></td></form></tr>\n";
echo "<tr id='show_fieldShortname' class='show_info' onclick='showEditor(\"fieldShortname\");'><th>Short name</th><td>$venueinfo[shortname]</td></tr>\n";

foreach ($info as $fieldnum=>$v)
    {
    echo "<tr id='edit_field$fieldnum' class='edit_info'><th>$v[0]</th><td><form method='POST' action='api.php'><input type='hidden' name='command' value='changeVenueInfo' /><input type='hidden' name='venue' value='$id' /><input type='hidden' name='fieldnum' value='$fieldnum' /><textarea name='newinfo' cols='80'>$v[1]</textarea><input type='submit' name='submit' value='save'><button onclick='hideEditor(\"field$fieldnum\"); return false;'>don't edit</button></td></form></tr>\n";
    echo "<tr id='show_field$fieldnum' class='show_info' onclick='showEditor(\"field$fieldnum\");'><th>$v[0]</th><td>" . multiline($v[1]) . "</td></tr>\n";
    }

if (hasPrivilege('scheduler'))
    {
    echo "<tr id='edit_fieldNew' class='edit_info'><th>[add field]</th><td><form method='POST' action='api.php'><input type='hidden' name='command' value='addVenueInfoField' /><input type='hidden' name='venue' value='$id' /><input type='text' name='fieldname'><input type='submit' name='submit' value='add'><button onclick='hideEditor(\"fieldNew\"); return false;'>don't add</button></td></form></tr>\n";
    echo "<tr id='show_fieldNew' class='show_info' onclick='showEditor(\"fieldNew\");'><th style='background:#ff8'>[add field]</th><td>&nbsp;</td></tr>\n";
    }

echo "</table>\n";

bifPagefooter();
?>
