<?php
require_once 'init.php';
requirePrivilege(array('scheduler','organizer'));
connectDB();
require_once 'scheduler.php';

$id = GETvalue('id',0);
if ($id == 0)
    errorAndQuit('no venue id given');

$info = dbQueryByID('select name,info from venue where id=?',$id);

function textField($field)
    {
    global $info;
    return '<td>' . stripslashes($info[$field]) . '</td>';
    }

function textareaField($field)
    {
    global $info;
    return '<td>' . str_replace("\n", "<br/>\n", stripslashes($info[$field])) . '</td>';
    }

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-type" content="text/html;charset=utf-8" />
<title>Buffalo Infringement Festival venue</title>
<link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>
<h1><?php echo stripslashes($info['name']); ?></h1>

<?php

function contactInfo($id)
    {
    $s = '<div style="margin-left: 4em">';
    $s .= str_replace("\n","<br>\n",getProposalInfo($id,'Contact info'));
    $s .= '</div>';
    return $s;
    }

function sheetlistingRow($id,$append)
    {
    global $listingList;
    global $proposalList;
    $s = '<tr>';
    $l = $listingList[$id];
    $s .= '<td valign="top">';
    if ($l->installation) $s .= 'installation';
    else $s .= timeToString($l->starttime) . '&nbsp;-&nbsp;' . timeToString($l->endtime);
    $s .= '</td>';
    $p = $l->proposal;
    $s .= '<td><b>' . $p->title . '</b>';
    $s .= contactInfo($p->id);
    if ($p->isgroupshow)
        {
        foreach ($p->performers as $perf)
            {
            $s .= '<br/>';
            $s .= $perf->showorder . ' (' . timeToString($perf->time) . ')';
            $s .= ' <b>' . $perf->performer->title . '</b>';
            $s .= contactInfo($perf->performerid);
            }
        }
    $s .= '</td>';
    $s .= $append;
    $s .= "</tr>\n";
    return $s;
    }

$dayshows = array();
for ($i=0; $i < festivalNumberOfDays(); $i++)
    $dayshows[dayToDate($i)] = array();
getDatabase();
global $venueList;
foreach ($venueList[$id]->listings as $l)
    {
    if (!$l->installation)
        $dayshows[$l->date][] = sortingKey($l->starttime) . sheetlistingRow($l->id,'<td valign="top">'.stripslashes($l->venuenote).'</td>');
    }

for ($i=0; $i < festivalNumberOfDays(); $i++)
    {
    $s = '';
    $date = dayToDate($i);
    sort ($dayshows[$date]);
    foreach ($dayshows[$date] as $row)
        $s .= $row;
    if ($s != '')
        {
        echo "<h2>infringement schedule with contact info - DO NOT POST PUBLICLY</h2>\n";
        echo '<h3>' . dateToString($date,true) . '</h3><br/>';
        echo '<table rules="rows" cellpadding="6">';
        echo $s;
        echo "</table>\n";
        }
    }

class instDates2
    {
    function __construct()
        {
        $this->dates = array();
        }
    function output()
        {
        $s = '';
        sort($this->dates);
        $start = -999;
        $end = -999;
        foreach ($this->dates as $d)
            {
            $daynum = dateToDaynum($d);
            if ($start < 0)
                {
                $start = $daynum;
                $end = $daynum;
                }
            else if ($daynum == $end+1)
                $end = $daynum;
            else
                {
                if ($start == $end)
                    $s .= date('M j, ',strtotime(dayToDate($start)));
                else
                    $s .= date('M j - ',strtotime(dayToDate($start))) . date('M j, ',strtotime(dayToDate($end)));
                $start = $daynum;
                $end = $daynum;
                }
            }
        if ($start > -1)
            {
            if ($start == $end)
                $s .= date('M j',strtotime(dayToDate($start)));
            else
                $s .= date('M j - ',strtotime(dayToDate($start))) . date('M j',strtotime(dayToDate($end)));
            }
        return $s;
        }
    }

$fullFestivalDates = date('M j',dayToTimestamp(0)) . " - " . date('M j',dayToTimestamp(festivalNumberOfDays()-1));

global $proposalList;
$vlist = array();
foreach ($proposalList as $p)
    {
    $idates = new instDates2();
    foreach ($p->listings as $l)
        {
        if (($l->installation) && ($l->venueid == $id))
            $idates->dates[] = $l->date;
        }
    $is = $idates->output();
    if ($is == $fullFestivalDates)
        $vlist[] = sortingKey($p->title) . '<b>' . $p->title . '</b>' . contactInfo($p->id) . '<br/>';
    else if ($is != '')
        $vlist[] = sortingKey($p->title) . '<b>' . $p->title . '</b> (' . $is . ')' . contactInfo($p->id) . '<br/>';
    }
if (count($vlist) > 0)
    {
    sort($vlist);
    echo '<hr style="margin-top:4em; margin-bottom:4em">';
    echo "<h2>infringement schedule with contact info - DO NOT POST PUBLICLY</h2>\n";
    echo '<h2>installations</h2>';
    echo implode('',$vlist);
    }

?>

</body>
</html>
