<?php
require_once 'db2/init.php';
connectDB();
require_once 'db2/scheduler.php';
getDatabase();

if ((array_key_exists('id',$_GET)) && ($_GET['id']) && (is_numeric($_GET['id'])))
    $id=$_GET['id'];
else
    die();

$row = dbQueryByID('select name,info from venue where id=?',$id);
$info = unserialize($row['info']);
$title = $row['name'];

$address = getInfo($info,'address');
$maphtml = getInfo($info,'maphtml');
$website = getInfo($info,'website');
$viewingtimes = getInfo($info,'viewingtimes');
$s = '';
if ($maphtml != '')
    $s .= '<div class="rfloat">' . $maphtml . '</div>';
if ($address != '')
    $s .= '<p><b>Address:</b><br/>' . str_replace("\n",'<br/>',$address) . '</p>';
if ($website != '')
    $s .= '<p><b>Website:</b> ' . linkedURL($website) . '</p>';
if ($viewingtimes != '')
    $s .= '<p><b>Hours:</b> ' . $viewingtimes . '</p>';

global $venueList;
$v = $venueList[$id];

require 'instDates.php';

$dayshows = array();
$instlist = array();
for ($i=0; $i < 11; $i++)
    $dayshows[dayToDate($i)] = array();
$numshows = 0;
foreach ($v->listings as $l)
    {
    if ($l->installation)
        {
        if (!isset($instlist[$l->proposal->id]))
            $instlist[$l->proposal->id] = new instDates($l->proposal);
        $instlist[$l->proposal->id]->dates[] = $l->date;
        $instlist[$l->proposal->id]->cancelled = $l->cancelled;
        }
    else
        {
        $dayshows[$l->date][] = sortingKey($l->starttime) . calendarRow($l->id);
        $numshows++;
        }
    }

$s .= '<br clear="all" />';
$s .= '<div class="schedulebox">';

if ($numshows > 0)
    {
    $s .= '<b>Performances:</b><table class="bif" rules="rows">';
    $s .= '<tbody>';
    for ($i=0; $i < 11; $i++)
        {
        $date = dayToDate($i);
        if (count($dayshows[$date]) > 0)
            {
            $s .= '<tr><td>' . dateToString($date,true) . '</td>';
            $s .= '<td><table class="colorized">';
            sort ($dayshows[$date]);
            foreach ($dayshows[$date] as $row)
                $s .= $row;
            $s .= '</table></td>';
            $s .= '</tr>';
            }
        }
    $s .= '</tbody></table>';
    }

if (count($instlist) > 0)
    {
    $s .= '<p><b>Installations:</b><ul>';
    $rows = array();
    foreach ($instlist as $i)
        {
        $r = sortingKey($i->proposal->title) . '<li>';
        if ($i->cancelled)
            $r .= '<span style="text-decoration: line-through">';
        $r .= '<a href="show.php?id=' . $i->proposal->id . '">' . $i->proposal->title . '</a>';
        $o = $i->output();
        if ($o != 'Jul 25 - Aug 4')
            $r .= ' (' . $o . ')';
        if ($i->cancelled)
            $r .= '</span> (cancelled)';
        $r .= '</li>';
        $rows[] = $r;
        }
    sort($rows);
    $s .= implode("\n",$rows);
    $s .= '</ul>';
    }

$s .= '</div>';

require 'bif.php';
bifPageheader($title);
echo $s;
bifPagefooter();

function calendarRow($id)
    {
    global $listingList;
    global $proposalList;
    $l = $listingList[$id];
    if ($l->cancelled) $tdtags = ' class="cancelled"';
    else $tdtags = '';
    $s = '<tr>';
    $s .= '<td' . $tdtags . '>';
    if ($l->installation) $s .= 'installation';
    else $s .= timeToString($l->starttime) . '&nbsp;-&nbsp;' . timeToString($l->endtime);
    $s .= '</td>';

    $p = $l->proposal;
    $s .= sprintf('<td%s><a href="show.php?id=%d">%s</a>',$tdtags,$p->id,$p->title);
    if ($p->isgroupshow)
        {
        foreach ($p->performers as $perf)
            {
            $s .= '<br/>';
            if ($perf->cancelled)
                $s .= '<span class="cancelled" style="text-decoration: line-through">';
            if ($p->grouplistmode == 0)
                $s .= $perf->showorder;
            else if ($p->grouplistmode == 1)
                $s .= timeToString($perf->time);
            else
                $s .= $perf->showorder . ' (' . timeToString($perf->time) . ')';
            $s .= ' <a href="show.php?id=' . $perf->performerid . '">' . $perf->performer->title . '</a>';
            if ($perf->cancelled)
                $s .= '</span> (cancelled)';
            }
        }
    $s .= '</td>';

    if ($l->venuenote != '') $s .= '<td>' . $l->venuenote . '</td>';
    $s .= '</td>';
    if ($l->cancelled)
        $s .= '<td>(cancelled)</td>';
    $s .= "</tr>\n";
    return $s;
    }

function getInfo($info,$field)
    {
    foreach ($info as $i)
        if (is_array($i) && array_key_exists(0,$i) && ($i[0] == $field))
            return $i[1];
    return '';
    }

?>
