<?php
require_once 'util.php';

$proposalList = array();
$batchList = array();
$listingList = array();
$venueList = array();
$groupPerformerList = array();
$programinfoList = array();

class proposalInfo
    {
    function __construct($id,$title,$festival,$grouplistmode,$isgroupshow,$deleted)
        {
        $this->id = $id;
        $this->title = $title;
        $this->festival = $festival;
        $this->deleted = $deleted;
        $this->grouplistmode = $grouplistmode;
        $this->isgroupshow = $isgroupshow;
        if ($isgroupshow)
            $this->performers = array();
        $this->listings = array();
        $this->groupshows = array();
        }
    }

class batchInfo
    {
    function __construct($id,$name)
        {
        $this->id = $id;
        $this->name = $name;
        $this->proposals = array();
        }
    }

class venueInfo
    {
    function __construct($id,$name)
        {
        $this->id = $id;
        $this->name = $name;
        $this->listings = array();
        }
    }

class listingInfo
    {
    function __construct($id,$proposalid,$venueid,$venuenote,$date,$starttime,$endtime,$installation,$cancelled,$note)
        {
        global $proposalList;
        global $venueList;
        $this->id = $id;
        $this->proposalid = $proposalid;
        $this->proposal = $proposalList[$proposalid];
        $this->venueid = $venueid;
        $this->venue = $venueList[$venueid];
        $this->venuenote = $venuenote;
        $this->date = $date;
        $this->starttime = $starttime;
        $this->endtime = $endtime;
        $this->installation = $installation;
        $this->cancelled = $cancelled;
        $this->note = $note;
        $this->proposal->listings[] = $this;
        $this->venue->listings[] = $this;
        if ($this->proposal->isgroupshow)
            {
            foreach ($this->proposal->performers as $performer)
                $performer->performer->listings[] = $this;
            }
        }
    }

class groupPerformerInfo
    {
    function __construct($id,$groupeventid,$performerid,$showorder,$time,$note,$cancelled)
        {
        global $proposalList;
        $this->id = $id;
        $this->groupeventid = $groupeventid;
        $this->groupevent = $proposalList[$groupeventid];
        $this->performerid = $performerid;
        $this->performer = $proposalList[$performerid];
        $this->showorder = $showorder;
        $this->time = $time;
        $this->note = $note;
        $this->cancelled = $cancelled;
        $this->groupevent->performers[] = $this;
        $this->performer->groupshows[] = $this;
        }
    }

class programInfo
    {
    function __construct($id,$title,$info)
        {
        $this->id = $id;
        $this->title = stripslashes($title);
        $this->brochure_description = '';
        $this->type = '';
        $this->website = '';
        $this->organization = '';
        if (is_array($info))
          foreach ($info as $i)
            if (is_array($i) && array_key_exists(0,$i))
                {
                if ($i[0] == 'Description for brochure')
                    $this->brochure_description = stripslashes($i[1]);
                else if (($i[0] == 'Short_Description') && ($this->brochure_description == ''))
                    $this->brochure_description = stripslashes($i[1]);
                else if ($i[0] == 'Website')
                    $this->website = $i[1];
                else if (($i[0] == 'Primary_Website') && ($this->website == ''))
                    $this->website = $i[1];
                else if (($i[0] == 'Type') || ($i[0] == 'Proposal_Type'))
                    $this->type = $i[1];
                else if ($i[0] == 'Organization')
                    $this->organization = $i[1];
                }
        }
    function text()
        {
        $s = '';
        if ($this->organization != '') $s .= '<em>Presented by ' . $this->organization . '</em><br/>';
        if ($this->website != '') $s .= '<em>' . $this->website . '</em><br/>';
        if ($this->brochure_description != '') $s .= $this->brochure_description . '<br/>';
        return $s;
        }
    }


function getDatabase($festival=0)
    {
    global $proposalList;
    global $venueList;
    global $listingList;
    global $groupPerformerList;

    if ($festival == 0)
        $festival = getFestivalID();

    $proposalList = array();
    $stmt = dbPrepare("select proposal.id,title,isgroupshow,deleted from proposal where festival=? order by title");
    $stmt->bind_param('i',$festival);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id,$title,$isgroupshow,$deleted);
    $grouplistmode = 1;
    while ($stmt->fetch())
        {
        $proposalList[$id] = new proposalInfo($id,stripslashes($title),$festival,$grouplistmode,$isgroupshow,$deleted);
        }
    $stmt->free_result();
    $stmt->close();

    $batchList = array();
    $stmt = dbPrepare("select id,name from batch where festival=?");
    $stmt->bind_param('i',$festival);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id,$name);
    while ($stmt->fetch())
        {
        $batchList[$id] = new batchInfo($id,stripslashes($name));
        }
    $stmt->free_result();
    $stmt->close();
    foreach ($batchList as $batch)
        {
        $stmt = dbPrepare("select proposal_id from proposalBatch where batch_id=?");
        $stmt->bind_param('i',$batch->id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($proposal_id);
        while ($stmt->fetch())
            {
            $batch->proposals[] = $proposalList[$proposal_id];
            }
        $stmt->free_result();
        $stmt->close();
        }

    $venueList = array();
    $stmt = dbPrepare("select id,name from venue where festival=?");
    $stmt->bind_param('i',$festival);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id,$name);
    while ($stmt->fetch())
        {
        $venueList[$id] = new venueInfo($id,stripslashes($name));
        }
    $stmt->free_result();
    $stmt->close();

    $groupPerformerList = array();
    $stmt = dbPrepare("select groupPerformer.id,groupevent,performer,showorder,time,note,cancelled from groupPerformer join proposal on groupPerformer.groupevent=proposal.id where proposal.festival=? order by groupevent,showorder,time");
    $stmt->bind_param('i',$festival);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id,$groupevent,$performer,$showorder,$time,$note,$cancelled);
    while ($stmt->fetch())
        $groupPerformerList[$id] = new groupPerformerInfo($id,$groupevent,$performer,$showorder,$time,stripslashes($note),$cancelled);
    $stmt->free_result();
    $stmt->close();

    $listingList = array();
    $stmt = dbPrepare("select listing.id,proposal,venue,venuenote,date,starttime,endtime,installation,cancelled,note from listing join proposal on listing.proposal=proposal.id where proposal.festival=?");
    $stmt->bind_param('i',$festival);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id,$proposal,$venue,$venuenote,$date,$starttime,$endtime,$installation,$cancelled,$note);
    while ($stmt->fetch())
        {
        $listingList[$id] = new listingInfo($id,$proposal,$venue,stripslashes($venuenote),$date,$starttime,$endtime,$installation,$cancelled,stripslashes($note));
        }
    $stmt->free_result();
    $stmt->close();
    }


function getPrograminfoList($festival=0)
    {
    global $programinfoList;
    $programinfoList = array();
    $stmt = dbPrepare("select id,title,info_json from proposal where deleted=0 and festival=?");
    $festival = getFestivalID();
    $stmt->bind_param('i',$festival);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id,$title,$info_json);
    while ($stmt->fetch())
        {
        $info = json_decode($info_json,true);

        $programinfoList[$id] = new programInfo($id,$title,$info);
        }
    $stmt->free_result();
    $stmt->close();
    }


function generateSmallCalendar($dayfunc)
    {
    global $festivalStartDate, $festivalNumberOfDays;
    $retstr = '<table rules=all>';
    $retstr .= '<tr><th>S<th>M<th>T<th>W<th>T<th>F<th>S</tr>' . "\n";
    $retstr .= '<tr>';
    if (date('w',$festivalStartDate) > 0)
        $retstr .= '<td colspan=' . date('w',$festivalStartDate) . "></td>\n";
    for ($d = 0; $d < $festivalNumberOfDays; $d++)
        {
        if (date('w',dayToTimestamp($d)) == 0)
            $retstr .= '</tr><tr>';
        $retstr .= '<td class="calEntry" data-bifday="' . $d . '">' . $dayfunc('day'.$d) . dayToDateDay($d) . "</td>\n";
        }
    $retstr .= '<td colspan=' . (7-date('w',dayToTimestamp($d))) . '></td></table>';
    $retstr .= '<span class="availabilityInfo">&nbsp;</span>';
    return $retstr;
    }

function timeToString($time)
    {
    if ($time == 1200)
        return 'noon';
    elseif ($time == 2400)
        return 'midnight';
    $h = floor($time/100);
    $m = $time - $h*100;
    if ($h >= 24)
        $h -= 24;
    if ($h >= 12)
        {
        $suffix = 'pm';
        if ($h > 12) $h -= 12;
        }
    else
        {
        $suffix = 'am';
        if ($h == 0) $h = 12;
        }
    if ($m != 0)
        return $h . ':' . str_pad($m,2,'0',STR_PAD_LEFT) . $suffix;
    else
        return $h . $suffix;
    }

function timeRangeToString($t1,$t2)
    {
    $ts1 = timeToString($t1);
    $ts2 = timeToString($t2);
    if (substr($ts1,-2,2) == substr($ts2,-2,2))
        return substr($ts1,0,strlen($ts1)-2) . ' - ' . $ts2;
    else
        return $ts1 . ' - ' . $ts2;
    }
function dateMenu($name, $default='')
    {
    global $festivalNumberOfDays;
    $retstr = '<select name="' . $name . '">';
    for ($d = 0; $d < $festivalNumberOfDays; $d++)
        {
        $date = dayToDate('day'.$d);
        $retstr .= '<option value="' . $date . '"';
        if ($date == $default) $retstr .= ' selected';
        $retstr .= '>' . dateToString($date) . '</option>';
        }
    $retstr .= "</select>\n";
    return $retstr;
    }

function timeMenu($startHour, $endHour, $name, $default='1900')
    {
    if ($default == '') $default='1900';
    $retstr = '<select name="' . $name . '">';
    for ($hour = $startHour; $hour < $endHour; $hour++)
        {
        for ($minute = 0; $minute < 60; $minute += 15)
            {
            $t = sprintf('%02d%02d',$hour,$minute);
            $retstr .= '<option value="' . $t . '"';
            if ($t == $default) $retstr .= ' selected';
            $retstr .= '>' . timeToString($t) . '</option>';
            }
        }
    $t = sprintf('%02d00',$endHour);
    $retstr .= '<option value="' . $t . '"';
    if ($t == $default) $retstr .= ' selected';
    $retstr .= '>' . timeToString($t) . '</option>';
    $retstr .= "</select>\n";
    return $retstr;
    }

function venueMenu($menuname,$selected='')
    {
    $venues = array();
    $stmt = dbPrepare('select id,name,shortname from venue where deleted=0 and festival=? order by shortname');
    $festival = getFestivalID();
    $stmt->bind_param('i',$festival);
    $stmt->execute();
    $stmt->bind_result($id,$name,$shortname);
    while ($stmt->fetch())
        {
        if ($shortname != '')
            $venues[$id] = substr(stripslashes($shortname),0,32);
        else
            $venues[$id] = substr(stripslashes($name),0,32);
        }
    $stmt->close();
    $retstr = "<select name='$menuname'>\n";
    asort($venues);
    foreach ($venues as $vid=>$vname)
        {
        $retstr .= "<option value='$vid'";
        if ($vid == $selected) $retstr .= ' selected';
        $retstr .= ">$vname</option>\n";
        }
    $retstr .= "</select>\n";
    return $retstr;
    }

function batchMenu($name,$includeAllShows=true,$selected=0)
    {
    $retstr = "<select name='$name'>\n";
    if ($includeAllShows)
        $retstr .= "<option value='0'>[all shows]</option>\n";
    $festival = getFestivalID();
    $stmt = dbPrepare('select id,name from batch where festival=? order by name');
    $stmt->bind_param('i',$festival);
    $stmt->execute();
    $stmt->bind_result($id,$name);
    while ($stmt->fetch())
        {
        $retstr .= "<option value='$id'";
        if ($id == $selected)
            $retstr .= " selected";
        $retstr .= ">" . stripslashes($name) . "</option>\n";
        }
    $stmt->close();
    $retstr .= "</select>\n";
    return $retstr;
    }

function categoryMenu($name,$selected=0)
    {
    $retstr = "<select name='$name'>\n";
    $stmt = dbPrepare('select id,name from category order by name');
    $stmt->execute();
    $stmt->bind_result($id,$name);
    while ($stmt->fetch())
        {
        $retstr .= "<option value='$id'";
        if ($id == $selected)
            $retstr .= " selected";
        $retstr .= ">" . stripslashes($name) . "</option>\n";
        }
    $stmt->close();
    $retstr .= "</select>\n";
    return $retstr;
    }

function showMenu($name,$batchid=0,$includeNone=false,$selected=0)
    {
    global $db;
    $retstr = "<select name='" . $name . "'>\n";
    if ($includeNone) $retstr .= '<option value="0">---- none ----</option>' . "\n";
    if ($batchid != 0)
        {
        $stmt = dbPrepare("select id,title from proposal join proposalBatch on proposal.id=proposalBatch.proposal_id where proposalBatch.batch_id=? and deleted=0 order by title");
        $stmt->bind_param('i',$batchid);
        }
    else
        {
        $festival = getFestivalID();
        $stmt = dbPrepare("select id,title from proposal where deleted=0 and festival=? order by title");
        $stmt->bind_param('i',$festival);
        }
    $stmt->execute();
    $stmt->bind_result($id,$title);
    while ($stmt->fetch())
        {
        $retstr .= "<option value='$id'";
        if ($id == $selected)
            $retstr .= " selected";
        $retstr .= ">" . substr(stripslashes($title),0,32) . "</option>\n";
        }
    $retstr .= "</select>\n";
    return $retstr;
    }

function groupShowMenu($name)
    {
    global $proposalList;
    $festival = getFestivalID();
    $retstr = '<select name="' . $name . '">';
    foreach ($proposalList as $proposal)
        {
        if (($proposal->isgroupshow) && (!$proposal->deleted) && ($proposal->festival == $festival))
            {
            $retstr .= '<option value="' . $proposal->id . '">' . $proposal->title . '</option>';
            }
        }
    $retstr .= '</select>';
    return $retstr;
    }


# converts "day0" etc (or 0, 1, 2 etc) to timestamp for 00:00:00 of that festival day
function dayToTimestamp($day)
    {
    global $festivalStartDate;
    if (strtolower(substr($day,0,3))=='day') $d = substr($day,3,2);
    else $d = $day;
    $day = date('j',$festivalStartDate) + $d;
    $month = date('n',$festivalStartDate);
    $year = date('Y',$festivalStartDate);
    return mktime(0,0,0,$month,$day,$year);
    }

# converts "day0" etc (or 0, 1, 2 etc) to "2011-07-28", etc
function dayToDate($day)
    {
    return date('Y-m-d', dayToTimestamp($day));
    }

# returns just the day part of a date (e.g. converts 0 to 28, from 2011-07-28)
function dayToDateDay($day)
    {
    return date('j', dayToTimestamp($day));
    }

# converts "2011-07-28" to 0, etc
function dateToDaynum($date)
    {
    global $festivalStartDate;
    $d = strtotime($date) - $festivalStartDate;
    return floor($d/(60*60*24));
    }

# converts "2011-07-22" to "Fri, Jul 22", etc
function dateToString($date,$nbsp=false)
    {
    $s = date('D, M j',strtotime($date));
    if ($nbsp) $s = str_replace(' ','&nbsp;',$s);
    return $s;
    }

function addMinutes($t,$a)
    {
    $h = floor($t / 100);
    $m = $t - $h * 100;
    $m += $a;
    while ($m >= 60)
        {
        $h += 1;
        $m -= 60;
        }
    return $h * 100 + $m;
    }

function sortingKey($s)
    {
    $s = strtoupper(html_entity_decode(stripslashes($s)));
    if (substr($s,0,2) == 'A ')
        $s = substr($s,2);
    else if (substr($s,0,3) == 'EL ')
        $s = substr($s,3);
    else if (substr($s,0,4) == 'THE ')
        $s = substr($s,4);
    $s2 = '';
    for ($i=0; $i < strlen($s); $i++)
        if (ctype_alnum($s[$i])) $s2 .= $s[$i];
    return '<!--' . substr($s2,0,16) . '-->';
    }

function scheduleEventForm($returnurl,$calEntryFunc,$proposalid,$venueid)
    {
    $s = '<div class="scheduleForm" id="scheduleEventForm" style="display: none">';
    $s .= '<form method="POST" action="api.php">';
    $s .= '<input type="hidden" name="command" value="scheduleEvent" />';
    $s .= '<input type="hidden" name="returnurl" value="' . $returnurl . '" />';
    if ($proposalid > 0)
        $s .= '<input type="hidden" name="proposal" value="' . $proposalid . '" />';
    if ($venueid > 0)
        $s .= '<input type="hidden" name="venue" value="' . $venueid . '" />';
    $s .= '<input type="hidden" name="installation" value="0" />';
    $s .= '<table>';
    $s .= '<tr>';
    $s .= '<td>';
    $s .= generateSmallCalendar($calEntryFunc);
    $s .= '</td>';
    $s .= '<td>';
    if ($proposalid==0)
        $s .= showMenu('proposal');
    if ($venueid==0)
        $s .= venueMenu('venue');
    $s .= '<br/>Venue detail:<input type="text" name="venuenote" value="" size="20"/></td>';
    $s .= '<td> Start time: ' . timeMenu(6,28,'starttime');
    $s .= '<br/> End time: ' . timeMenu(6,28,'endtime') . '</td>';
    $s .= '<td>Note:<input type="text" name="note" value="" size="10"/></td>';
    $s .= '<td> <input type="submit" value="Add"> </td>';
    $s .= '</tr>';
    $s .= '</table>';
    $s .= '</form>';
    $s .= '</div>';
    return $s;
    }

function scheduleInstallationForm($returnurl,$calEntryFunc,$proposalid,$venueid)
    {
    $s = '<div class="scheduleForm" id="scheduleInstallationForm" style="display: none">';
    $s .= '<form method="POST" action="api.php">';
    $s .= '<input type="hidden" name="command" value="scheduleEvent" />';
    $s .= '<input type="hidden" name="returnurl" value="' . $returnurl . '" />';
    if ($proposalid > 0)
        $s .= '<input type="hidden" name="proposal" value="' . $proposalid . '" />';
    if ($venueid > 0)
        $s .= '<input type="hidden" name="venue" value="' . $venueid . '" />';
    $s .= '<input type="hidden" name="installation" value="1" />';
    $s .= '<table>';
    $s .= '<tr>';
    $s .= '<td>';
    $s .= generateSmallCalendar($calEntryFunc);
    $s .= '</td>';
    $s .= '<td>';
    if ($proposalid==0)
        $s .= showMenu('proposal');
    if ($venueid==0)
        $s .= venueMenu('venue');
    $s .= '<br/>Venue detail:<input type="text" name="venuenote" value="" size="20"/>';
    $s .= '</td>';
    $s .= '<td>Note:<input type="text" name="note" value="" size="10"/></td>';
    $s .= '<td> <input type="submit" value="Add"> </td>';
    $s .= '</tr>';
    $s .= '</table>';
    $s .= '</form>';
    $s .= '</div>';
    return $s;
    }

function googleTime($date,$time)
    {
    $time = addMinutes($time,240); // convert to UTC
    if ($time >= 2400)
        {
        $date = dayToDate(dateToDaynum($date)+1);
        $time -= 2400;
        }
    while (strlen($time) < 4) $time = '0' . $time;
    $s = date('Ymd',strtotime($date)) . 'T' . $time . '00Z';
    return $s;
    }

function listingRow($id,$showdate,$showtime,$showvenue,$showproposal,$showperformers,$rowtags='',$append='')
    {
    global $listingList;
    global $proposalList;
    $l = $listingList[$id];
    if ($l->cancelled) $tdtags = ' class="cancelled"';
    else $tdtags = '';
    $s = '<tr ' . $rowtags . '>';
    if ($showdate)
        $s .= '<td' . $tdtags . '>' . dateToString($l->date) . '</td>';
    if ($showtime)
        {
        $s .= '<td' . $tdtags . '>';
        if ($l->installation) $s .= 'installation';
        else $s .= timeToString($l->starttime) . '&nbsp;-&nbsp;' . timeToString($l->endtime);
        $s .= '</td>';
        }
    if ($showvenue)
        {
        $s .= sprintf('<td%s><a href="venue.php?id=%d">%s</a>',$tdtags,$l->venueid,$l->venue->name);
        if ($l->venuenote != '') $s .= ' (' . $l->venuenote . ')';
        $s .= '</td>';
        }
    if ($showproposal)
        {
        $p = $l->proposal;
        $s .= sprintf('<td%s><a href="proposal.php?id=%d">%s</a>',$tdtags,$p->id,$p->title);
        if (($p->isgroupshow) && ($showperformers))
            {
            foreach ($p->performers as $perf)
                {
                $s .= '<br/>';
                if ($perf->cancelled)
                    $s .= '<span class="cancelled">';
/*
                if ($p->grouplistmode == 0)
                    $s .= $perf->showorder;
                else if ($p->grouplistmode == 1)
                    $s .= timeToString($perf->time);
                else
*/
                    $s .= $perf->showorder . ' (' . timeToString($perf->time) . ')';
                $s .= ' <a href="proposal.php?id=' . $perf->performerid . '">' . $perf->performer->title . '</a>';
                if ($perf->cancelled)
                    $s .= '</span> (cancelled)';
                }
            }
        $s .= '</td>';
        }
    if ($l->cancelled)
        $s .= '<td>(cancelled)</td>';
    if (hasPrivilege(array('organizer','scheduler')))
        {
        $s .= sprintf('<td%s>%s</td>',$tdtags,$l->note);
        }
    $s .= $append;
//$s .= '<td><a href="http://www.google.com/calendar/event?action=TEMPLATE&text=' . urlencode($l->proposal->title) . '&dates=' . googleTime($l->date,$l->starttime) . '/' . googleTime($l->date,$l->endtime) . '&details=' . urlencode('http://infringebuffalo.org/show.php?id='.$l->proposal->id) . '&location=' . urlencode($l->venue->name . ',' . str_replace("\n",',',$l->venue->address)) . '&trp=false' . '">add to google calendar</a></td>';
    $s .= "</tr>\n";
    return $s;
    }

function editableListingRow($id,$showdate,$showtime,$showvenue,$showproposal,$showperformers,$rowtags='',$append='')
    {
    global $listingList;
    global $proposalList;
    $l = $listingList[$id];
    $s = listingRow($id,$showdate,$showtime,$showvenue,$showproposal,$showperformers,$rowtags . ' id="listingrow' . $id . '"',$append . '<td> &nbsp;<a href="" onclick="toggleEdit(\'listingrow' . $id . '\'); return false;">edit</a>&nbsp; </td>');

    $s .= '<tr ' . $rowtags . ' id="listingrow' . $id . 'edit" style="display:none">';
    $s .= '<form method="POST" action="api.php">';
    $s .= '<input type="hidden" name="command" value="changeListing" />';
    $s .= '<input type="hidden" name="listingid" value="' . $id . '" />';
    if ($showdate)
        $s .= '<td>' . dateMenu('date',$l->date) . '</td>';
    else
        $s .= '<input type="hidden" name="date" value="' . $l->date . '" />';
    if ($showtime)
        {
        if ($l->installation)
            $s .= '<input type="hidden" name="installation" value="1" /><td>installation</td>';
        else
            $s .= '<td>' . timeMenu(6,28,'starttime',$l->starttime) . ' - ' . timeMenu(6,28,'endtime',$l->endtime) . '</td>';
        }
    else
        $s .= sprintf('<input type="hidden" name="starttime" value="%s" /><input type="hidden" name="endtime" value="%s" />',$l->starttime,$l->endtime);
    if ($showvenue)
        {
        $s .= '<td>' . venueMenu('venue',$l->venueid);
        $s .= '<input type="text" name="venuenote" value="' . $l->venuenote . '" />';
        $s .= '</td>';
        }
    if ($showproposal)
        {
        $p = $l->proposal;
        $s .= sprintf('<td><a href="proposal.php?id=%d">%s</a>',$p->id,$p->title);
        if (($p->type=='group') && ($showperformers))
            {
            foreach ($p->performers as $perf)
                $s .= '<br/>' . $perf->showorder . ' ' . $perf->performer->title . '</li>';
            }
        $s .= '</td>';
        }
    else
        $s .= '<input type="hidden" name="proposalid" value="' . $l->proposalid . '" />';
    $s .= '<td>Note';
    $s .= '<input type="text" name="note" value="' . $l->note . '" />';
    $s .= '</td>';
    $s .= '<td><input type="submit" value="Save" />';
    $s .= '</form>';
    if ($l->cancelled)
        $s .= '<form method="POST" action="api.php"><input type="hidden" name="command" value="uncancelListing" /><input type="hidden" name="listingid" value="' . $l->id . '" /><input type="submit" value="uncancel" /></form>';
    else
        $s .= '<form method="POST" action="api.php"><input type="hidden" name="command" value="cancelListing" /><input type="hidden" name="listingid" value="' . $l->id . '" /><input type="submit" value="cancel" /></form>';
    $s .= '<form method="POST" action="api.php"><input type="hidden" name="command" value="deleteListing" /><input type="hidden" name="listingid" value="' . $l->id . '" /><input type="submit" value="delete" /></form>';
    $s .= '</td>';

    $s .= $append;
    $s .= '<td><a href="" onclick="toggleEdit(\'listingrow' . $id . '\'); return false;">don\'t edit</a></td>';
    $s .= "</tr>\n";
    return $s;
    }

function getProposalInfo($id,$field)
    {
    $row = dbQueryByID('select info_json from proposal where id=?',$id);
    $info = json_decode($row['info_json'],true);
    foreach ($info as $i)
        if (is_array($i) && array_key_exists(0,$i) && ($i[0] == $field))
            return $i[1];
    return '';
    }

function setProposalInfo($id,$field,$value)
    {
    $row = dbQueryByID('select info_json from proposal where id=?',$id);
    $info = json_decode($row['info_json'],true);
    $found = false;
    foreach ($info as &$i)
        if (is_array($i) && array_key_exists(0,$i) && (strcasecmp($i[0],$field)==0))
            {
            $i[1] = $value;
            $found = true;
            break;
            }
    if (!$found)
        $info[] = array($field,$value);
    $info_json = json_encode($info);
    $stmt = dbPrepare('update proposal set info_json=? where id=?');
    $stmt->bind_param('si',$info_json,$id);
    $stmt->execute();
    $stmt->close();
    }

function addToBatch($proposal,$batch)
    {
    $stmt = dbPrepare('select count(*) from proposalBatch where proposal_id=? and batch_id=?');
    $stmt->bind_param('ii',$proposal,$batch);
    $stmt->execute();
    $count = 0;
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    if ($count > 0)
        return;
    $stmt = dbPrepare('insert into proposalBatch (proposal_id,batch_id) values (?,?)');
    $stmt->bind_param('ii',$proposal,$batch);
    $stmt->execute();
    $stmt->close();
    log_message("Added proposal {ID:$proposal} to batch {ID:$batch}");
    }

function addToCategory($proposal,$category)
    {
    $stmt = dbPrepare('select count(*) from proposalCategory where proposal_id=? and category_id=?');
    $stmt->bind_param('ii',$proposal,$category);
    $stmt->execute();
    $count = 0;
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    if ($count > 0)
        return;
    $stmt = dbPrepare('insert into proposalCategory (proposal_id,category_id) values (?,?)');
    $stmt->bind_param('ii',$proposal,$category);
    $stmt->execute();
    $stmt->close();
    log_message("Added proposal {ID:$proposal} to category {ID:$category}");
    }

function getNotes($entity)
    {
    $stmt = dbPrepare('select note.id,creatorid,note,user.name from note join noteLink on note.id=noteLink.note_id join user on creatorid=user.id where noteLink.entity_id=?');
    $stmt->bind_param('i',$entity);
    $stmt->execute();
    $notes = array();
    $stmt->bind_result($id,$creatorid,$note,$creatorname);
    while ($stmt->fetch())
        {
        $notes[] = array('id'=>$id, 'creatorid'=>$creatorid, 'creatorname'=>$creatorname, 'note'=>$note);
        }
    $stmt->close();
    return $notes;
    }

function noteDiv($note,$entity_id)
    {
    if ($note['creatorid'] == $_SESSION['userid'])
        {
        $div = "<div id='show_note$note[id]' class='show_info note'><span class='noteauthor'>$note[creatorname]:</span> $note[note]<br> <a onclick='showEditor(\"note$note[id]\");'>[edit]</a> <a href='linkNote.php?id=$note[id]'>[link]</a></div>\n";
        $div .= "<div id='edit_note$note[id]' class='edit_info note'>";
        $div .= beginApiCallHtml('changeNote', array('noteid'=>$note['id']), true) . "<textarea name='note' rows='2' cols='30'>$note[note]</textarea><br><input type='submit' name='submit' value='update' style='padding:0' /></form>\n";
        $div .= beginApiCallHtml('unlinkNote', array('noteid'=>$note['id'], 'entityid'=>$entity_id), true) . "<input type='submit' name='submit' value='remove' style='padding:0' />\n</form>\n";
        $div .= "<button style='padding:0' onclick='hideEditor(\"note$note[id]\")'>don't edit</button>\n";
        $div .= "</div>\n";
        }
    else
        {
        $div = "<div style='border: 1px solid'><span style='background:#aaa'>$note[creatorname]:</span> $note[note]</div>\n";
        }
    return $div;
    }


function beginApiCallHtml($command, $parameters=array(), $inline=false, $formname='')
    {
    $html = "<form method='POST' action='api.php'";
    if ($formname != '')
        $html .= " name='$formname'";
    if ($inline)
        $html .= " style='display:inline'";
    $html .= ">\n<input type='hidden' name='command' value='$command' />\n";
    foreach ($parameters as $name=>$value)
        {
        $html .= "<input type='hidden' name='$name' value='$value' />\n";
        }
    return $html;
    }

function endApiCallHtml($submitlabel)
    {
    return "<input type='submit' name='submit' value='$submitlabel' />\n</form>\n";
    }
?>
