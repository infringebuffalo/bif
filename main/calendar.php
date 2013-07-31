<?php
require_once 'bif.php';
require_once 'db2/init.php';
connectDB();
require_once 'db2/scheduler.php';
if ((isset($_GET['day'])) && (is_numeric($_GET['day'])))
    {
    $day = $_GET['day']-1;
    if ($day < 0)
        $day = 0;
    if ($day > 10)
        $day = 10;
    $date = dayToDate($day);
    }
else
    die();

getDatabase();

function showType($listing)
    {
    global $programinfoList;
    $types = array('dance'=>'performance','group'=>'performance','groupmedia'=>'media','groupmusic'=>'music','media'=>'media','music'=>'music','poetry'=>'performance','theatre'=>'performance','visualart'=>'visualart');
    return $types[$programinfoList[$listing->proposal->id]->brochure_type];
    }

function date_performances($date)
    {
    global $listingList;
    $list = array();
    foreach ($listingList as $l)
        {
        if ((!$l->installation) && ($l->date == $date))
            {
            $s = sortingKey($l->starttime . $l->endtime . $l->venue->name) . calendarRow($l->id,true);
            $list[] = $s;
            }
        }
    sort($list);
    echo '<table class="colorized" cellpadding=3>' . implode("\n",$list) . '</table>';
    }

function calendarRow($id,$showtime)
    {
    global $listingList;
    global $proposalList;
    $l = $listingList[$id];
    if ($l->cancelled) $tdtags = ' class="cancelled" style="text-decoration: line-through"';
    else $tdtags = '';
    $s = '<tr>';
    if ($showtime)
        {
        $s .= '<td' . $tdtags . '>';
        if ($l->installation) $s .= 'installation';
        else $s .= timeToString($l->starttime) . '&nbsp;-&nbsp;' . timeToString($l->endtime);
        $s .= '</td>';
        }

    $p = $l->proposal;
    $s .= sprintf('<td%s><a href="show.php?id=%d">%s</a>',$tdtags,$p->id,$p->title);
    if ($p->isgroupshow)
        {
        foreach ($p->performers as $perf)
            {
            $s .= '<br/>';
            if ($perf->cancelled) $s .= '<span style="text-decoration: line-through; color:#444">';
/*
            if ($p->grouplistmode == 0)
                $s .= $perf->showorder;
            else if ($p->grouplistmode == 1)
                $s .= timeToString($perf->time);
            else
*/
                $s .= $perf->showorder . ' (' . timeToString($perf->time) . ')';
            $s .= ' <a href="show.php?id=' . $perf->performerid . '">' . $perf->performer->title . '</a>';
            if ($perf->cancelled) $s .= '</span> (cancelled)';
            }
        }
    $s .= '</td>';

    $s .= sprintf('<td%s><a href="venue.php?id=%d">%s</a>',$tdtags,$l->venueid,$l->venue->name);
    if ($l->venuenote != '') $s .= ' (' . $l->venuenote . ')';
    $s .= '</td>';
    if ($l->cancelled)
        $s .= "<td>(cancelled)</td>";
    $s .= "</tr>\n";
    return $s;
    }

function date_installations($date)
    {
    global $listingList;
    $list = array();
    foreach ($listingList as $l)
        {
        if (($l->installation) && ($l->date == $date))
            {
            $s = sortingKey($l->venue->name . $l->proposal->title) . calendarRow($l->id,false);
            $list[] = $s;
            }
        }
    sort($list);
    echo '<table class="colorized" cellpadding=3>' . implode("\n",$list) . '</table>';
    }

$headerExtras = "";

bifPageheader(dateToString($date),$headerExtras);

echo "<h3>Shows</h3>\n";
date_performances($date);
echo "<h3>Installations</h3>\n";
date_installations($date);

bifPagefooter();
?>
