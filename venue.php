<?php
require_once 'init.php';
connectDB();
requireLogin();
requirePrivilege(array('scheduler','organizer'));
require_once 'util.php';
require_once 'scheduler.php';


function venueToolsDiv($id,$venueinfo)
    {
    $html = '';
    if (hasPrivilege('scheduler'))
        {
        $html .= "<div style='float:right; width:20%'>\n";
        if ($venueinfo['deleted'])
            $html .= "<span><form method='POST' action='api.php'><input type='hidden' name='command' value='undeleteVenue' /><input type='hidden' name='id' value='$id' /><input type='submit' value='undelete venue' /></form></span>";
        else
            $html .= "<span><form method='POST' action='api.php'><input type='hidden' name='command' value='deleteVenue' /><input type='hidden' name='id' value='$id' /><input type='submit' value='delete venue' /></form></span>";
        if ($venueinfo['festival'] != getFestivalID())
            $html .=  "<span><form method='POST' action='api.php'><input type='hidden' name='command' value='copyVenue' /><input type='hidden' name='id' value='$id' /><input type='submit' value='copy venue' /></form></span>";
        $html .= "<br><br>\n";
        $notes = getNotes($id);
        foreach ($notes as $n)
            {
            $html .= noteDiv($n,$id);
            }
        $html .= "<form method='POST' action='api.php'><input type='hidden' name='command' value='addNote' /><input type='hidden' name='entity' value='$id' /><textarea name='note' rows='2' cols='30'></textarea><br><input type='submit' name='submit' value='add note'/></form>\n";
        $html .= "</div>\n";
        }
    return $html;
    }


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

function venueSchedulingDiv($id,$venueinfo)
    {
    $html = '';
    if (hasPrivilege('scheduler'))
        {
        $html .= <<<ENDSTRING
<div class="schedulebox">
Scheduling:<br/>
<a href="" id="scheduleEventAnchor" onclick="showScheduler('#scheduleEventForm'); return false">performance</a>&nbsp;
<a href="" id="scheduleInstallationAnchor" onclick="showScheduler('#scheduleInstallationForm'); return false">installation</a>&nbsp;
ENDSTRING;
        $html .= scheduleEventForm('venue.php?id=' . $id, 'calEntry', 0, $id);
        $html .= scheduleInstallationForm('venue.php?id=' . $id, 'calEntry2', 0, $id);
        $html .= "</div>\n";
        }
    return $html;
    }


function venueScheduleDiv($id,$venueinfo)
    {
    global $venueList;
    $html = '';
    $dayshows = array();
    $dayinst = array();
    for ($i=0; $i < festivalNumberOfDays(); $i++)
        {
        $dayshows[dayToDate($i)] = array();
        $dayinst[dayToDate($i)] = array();
        }
    getDatabase($venueinfo['festival']);
    foreach ($venueList[$id]->listings as $l)
        {
        if ($l->installation)
            $dayinst[$l->date][] = sortingKey($l->proposal->title) . listingRow($l->id,false,false,false,true,true,'','<td>'.stripslashes($l->venuenote).'</td>');
        else
            $dayshows[$l->date][] = sortingKey(sortableTime($l->starttime)) . listingRow($l->id,false,true,false,true,true,'','<td>'.stripslashes($l->venuenote).'</td>');
        }
    $html .= "<div class=\"schedulebox\">\nSchedule:\n<table>\n";
    $html .= "<thead><tr><th>day</th><th>performances</th><th>installations</th></tr></thead>\n";
    $html .= "<tbody>\n";
    for ($i=0; $i < festivalNumberOfDays(); $i++)
        {
        $date = dayToDate($i);
        $html .= '<tr><td>' . dateToString($date,true) . '</td>';
        sort ($dayshows[$date]);
        $html .= '<td><table>';
        foreach ($dayshows[$date] as $row)
            $html .= $row;
        $html .= '</table></td>';
        sort ($dayinst[$date]);
        $html .= '<td><table>';
        $odd = true;
        foreach ($dayinst[$date] as $row)
            {
            if ($odd) $html .= '<tr class="oddrow">';
            else $html .= '<tr>';
            $odd = ! $odd;
            $html .= $row . '</tr>';
            }
        $html .= '</table></td>';
        $html .= '</tr>';
        }
    $html .= "</tbody>\n</table>\n</div>\n";
    return $html;
    }


function venueInfoDiv($id,$venueinfo)
    {
    $html = '';
    $info = json_decode($venueinfo['info_json'],true);

    $html .= "<div><table>\n";

    $html .= "<tr id='edit_fieldName' class='edit_info'><th>Name</th><td><form method='POST' action='api.php'><input type='hidden' name='command' value='changeVenueName' /><input type='hidden' name='venue' value='$id' /><textarea name='newinfo' cols='80'>" . htmlspecialchars($venueinfo[name],ENT_COMPAT | ENT_HTML5, "UTF-8") . "</textarea><input type='submit' name='submit' value='save'><button onclick='hideEditor(\"fieldName\"); return false;'>don't edit</button></td></form></tr>\n";
    $html .= "<tr id='show_fieldName' class='show_info'> <th>Name <span class='fieldEditLink' onclick='showEditor(\"fieldName\");'>[edit]</span></th> <td>" . htmlspecialchars($venueinfo['name'],ENT_COMPAT | ENT_HTML5, "UTF-8") . "</td></tr>\n";
    $html .= "<tr id='edit_fieldShortname' class='edit_info'><th>Short name</th><td><form method='POST' action='api.php'><input type='hidden' name='command' value='changeVenueShortname' /><input type='hidden' name='venue' value='$id' /><textarea name='newinfo' cols='80'>" . htmlspecialchars($venueinfo[shortname],ENT_COMPAT | ENT_HTML5, "UTF-8") . "</textarea><input type='submit' name='submit' value='save'><button onclick='hideEditor(\"fieldShortname\"); return false;'>don't edit</button></td></form></tr>\n";
    $html .= "<tr id='show_fieldShortname' class='show_info'> <th>Short name <span class='fieldEditLink' onclick='showEditor(\"fieldShortname\");'>[edit]</span></th> <td>" . htmlspecialchars($venueinfo['shortname'],ENT_COMPAT | ENT_HTML5, "UTF-8") . "</td></tr>\n";

    foreach ($info as $fieldnum=>$v)
        {
        $html .= "<tr id='edit_field$fieldnum' class='edit_info'><th>" . htmlspecialchars($v[0],ENT_COMPAT | ENT_HTML5, "UTF-8") . "</th>\n";
        $html .= "<td style='white-space:nowrap'>" . beginApiCallHtml('changeVenueInfo', array('venue'=>"$id", 'fieldnum'=>"$fieldnum"));
        $html .= "<textarea id='input_field$fieldnum' name='newinfo' cols='80'>" . htmlspecialchars($v[1],ENT_COMPAT | ENT_HTML5, "UTF-8") . "</textarea>\n<input type='submit' name='submit' value='save'><button onclick='hideEditor(\"field$fieldnum\"); return false;'>don't edit</button></form>";
        $html .= beginApiCallHtml('deleteVenueInfoField', array('venue'=>"$id", 'fieldnum'=>"$fieldnum")) . "<input type='submit' name='submit' value='delete field'></form>\n";
        $html .= "</td></tr>\n";
        $html .= "<tr id='show_field$fieldnum' class='show_info'><th>" . htmlspecialchars($v[0],ENT_COMPAT | ENT_HTML5, "UTF-8") . " <span class='fieldEditLink' onclick='showEditor(\"field$fieldnum\");'>[edit]</span></th><td>" . multiline($v[1]) . "</td></tr>\n";
        }

    if (hasPrivilege('scheduler'))
        {
        $html .= "<tr id='edit_fieldNew' class='edit_info'><th>[add field]</th><td><form method='POST' action='api.php'><input type='hidden' name='command' value='addVenueInfoField' /><input type='hidden' name='venue' value='$id' />Name:<input type='text' size='30' name='fieldname'> Value:<input type='text' name='fieldvalue'><input type='submit' name='submit' value='add'><button onclick='hideEditor(\"fieldNew\"); return false;'>don't add</button></td></form></tr>\n";
        $html .= "<tr id='show_fieldNew' class='show_info' onclick='showEditor(\"fieldNew\");'><th style='background:#ff8'>[add field]</th><td>&nbsp;</td></tr>\n";
        }

    $html .= "</table></div>\n";

    return $html;
    }

function venueMapDiv($id,$venueinfo)
    {
    $info = json_decode($venueinfo['info_json'],true);
    $maphtml = getInfo($info,'maphtml');
    $html = "<div class='rfloat'>\n";
    if (trim($maphtml) != '')
        $html .= $maphtml;
    else
        {
        $lat = getInfo($info,'latitude');
        $lon = getInfo($info,'longitude');
        if (($lat != '') && ($lon != ''))
            {
            $lat = (float) $lat;
            $lon = (float) $lon;
            $left = $lon - 0.0015;
            $right = $lon + 0.0015;
            $bottom = $lat - 0.0016;
            $top = $lat + 0.0016;
            $biglat = $lat;
            $biglon = $lon;
            $html .= '<iframe width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://www.openstreetmap.org/export/embed.html?bbox=' . $left . ',' . $bottom . ',' . $right . ',' . $top . '&amp;layer=mapnik&amp;marker=' . $lat . ',' . $lon . '" style="border: 1px solid black"></iframe><br /><small><a href="http://www.openstreetmap.org/?lat=' . $biglat . '&amp;lon=' . $biglon . '&amp;zoom=17&amp;layers=M&amp;mlat=' . $lat . '&amp;mlon=' . $lon . '">View Larger Map</a></small>';
            }
        }
    $html .= "</div>\n";
    return $html;
    }


if (!isset($_GET['id']))
    errorAndQuit('no venue id given');
else
    $id = $_GET['id'];

$header = <<<ENDSTRING
<script src="jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
function showEditor(name)
    {
    $('#show_' + name).hide();
    $('#edit_' + name).show();
    }
function hideEditor(name)
    {
    shownode = $('#show_'+name);
    editnode = $('#edit_'+name);
    inputnode = $('#input_'+name);
    shownode.show();
    origtext = shownode.find("td").html();
    inputnode.val(origtext);
    editnode.hide();
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
ENDSTRING;

$venueinfo = dbQueryByID('select venue.name,shortname,info_json,festival,deleted,festival.name as festivalname from venue join festival on venue.festival=festival.id where venue.id=?',$id);

$toolsdiv = venueToolsDiv($id,$venueinfo);
$schedulingdiv = venueSchedulingDiv($id,$venueinfo);
$schedulediv = venueScheduleDiv($id,$venueinfo);
$infodiv = venueInfoDiv($id,$venueinfo);
$mapdiv = venueMapDiv($id,$venueinfo);

bifPageheader('venue: ' . $venueinfo['name'],$header);
echo "<p>Venue for $venueinfo[festivalname]</p>\n";
echo $mapdiv;
echo $schedulingdiv;
echo $schedulediv;
echo $toolsdiv;
echo $infodiv;

bifPagefooter();
?>
