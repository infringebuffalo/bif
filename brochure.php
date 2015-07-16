<?php
require_once 'init.php';
connectDB();
requireLogin();
require_once 'scheduler.php';

$trimLength = GETvalue('trim',1000000);

getDatabase();
getPrograminfoList();

/* proposal types / headings used in 2014:
$headings = array('theatre'=>'theatre', 'literary'=>'literary', 'dance'=>'dance', 'music'=>'music', 'street'=>'street', 'film'=>'film & video', 'visualart'=>'visual art', 'groupmusic'=>'music group shows', 'groupmedia'=>'movie nights', 'group'=>'group shows');
*/

/* proposal types / headings used in 2015: */
$headings = array('Theatre'=>'theatre', 'Literary'=>'literary', 'Dance'=>'dance', 'Music'=>'music', 'Film/Video'=>'film & video', 'Visual_Art'=>'visual art', 'groupmusic'=>'music group shows', 'groupmedia'=>'movie nights', 'group'=>'group shows');

$list = array();
foreach (array_keys($headings) as $h)
    $list[$h] = array();

class instDates
    {
    function __construct($venue)
        {
        $this->venue = $venue;
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
                $start = -999;
                $end = -999;
                }
            }
        if ($start > -1)
            {
            if ($start == $end)
                $s .= date('M j, ',strtotime(dayToDate($start)));
            else
                $s .= date('M j - ',strtotime(dayToDate($start))) . date('M j, ',strtotime(dayToDate($end)));
            }
        $s .= $this->venue->name;
        return $s;
        }
    }

function trimText($s)
    {
    global $trimLength;
    $s = strip_tags($s);
    if (strlen($s) < $trimLength)
        return $s;
    $i = $trimLength;
    while (($i > 0) && (ctype_alpha(substr($s,$i,1))) && (ctype_alpha(substr($s,$i+1,1))))
        {
        $i--;
        }
    return substr($s,0,$i);
    }

function groupshowPerformerList($p)
    {
    $performerlist = '';
    $comma = '';
    foreach ($p->performers as $perf)
        {
        if (!$perf->cancelled)
            {
            $performerlist .= $comma . $perf->performer->title;
            $comma = ', ';
            }
        }
    if ($performerlist != '')
        return ' (' . $performerlist . ')<br/>';
    else
        return '';
    }

function performanceSchedule($p)
    {
    $a = array();
    foreach ($p->listings as $l)
        if ((!$l->installation) && ($l->proposal->id == $p->id) && (!$l->cancelled))
            {
            $s2 = sortingKey($l->date . $l->starttime) . dateToString($l->date) . ' ' . timeToString($l->starttime) . '-' . timeToString($l->endtime) . ' ' . $l->venue->name;
            if ($l->venuenote != '')
                $s2 .= ' (' . $l->venuenote . ')';
            $a[] = $s2;
            }
    foreach ($p->groupshows as $g)
        {
        if (!$g->cancelled)
          {
          foreach ($g->groupevent->listings as $l)
            if ((!$l->installation) && (!$l->cancelled))
                {
                if ($g->groupevent->grouplistmode > 0)
                    $s2 = sortingKey($l->date . $g->time) . dateToString($l->date) . ' ' . timeToString($g->time);
                else
                    $s2 = sortingKey($l->date . $l->starttime) . dateToString($l->date) . ' ' . timeToString($l->starttime) . '-' . timeToString($l->endtime);
                $s2 .= ' ' . $l->venue->name . ' [' . $g->groupevent->title . ']';
                $a[] = $s2;
                }
          }
        }
    if (count($a) > 0)
        {
        sort($a);
        return implode('<br/>',$a) . '<br/>';
        }
    else
        return '';
    }

function installationSchedule($p)
    {
    $a = array();
    foreach ($p->listings as $l)
        if (($l->installation) && (!$l->cancelled))
            {
            if (!isset($a[$l->venueid]))
                $a[$l->venueid] = new instDates($l->venue);
            $a[$l->venueid]->dates[] = $l->date;
            }
    $s = '';
    foreach ($a as $line)
        $s .= $line->output() . '<br/>';
    return $s;
    }

foreach ($programinfoList as $p)
  {
  if (!$proposalList[$p->id]->deleted)
    {
    $s = sortingKey($p->title);
    $s .= '<b>' . $p->title . '</b><br/>';
    $s .= '<pre>' . $p->website . "\n" . trimText($p->brochure_description) . '</pre>';
    if ($proposalList[$p->id]->isgroupshow)
        $s .= groupshowPerformerList($proposalList[$p->id]);
    $s .= performanceSchedule($proposalList[$p->id]);
    $s .= installationSchedule($proposalList[$p->id]);
    $list[$p->type][] = $s;
    }
  }

foreach (array_keys($headings) as $h)
    sort($list[$h]);

?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Buffalo Infringement Festival brochure info</title>
<link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>
<em>alphabetical list of shows</em>
<br/><br/>
<?php
foreach ($headings as $k=>$v)
    {
    echo '<h2>--' . $v . '--</h2>';
    echo implode("<br/>\n",$list[$k]);
    }
?>

<br/><br/><br/><hr/><br/>
<em>--calendar at a glance--</em>
<br/><br/>

<?php
for ($i = 0; $i < $festivalNumberOfDays; $i++)
    {
    $date = dayToDate('day' . $i);
    echo "<h2>" . dateToString($date) . "</h2>\n";
    $list = array();
    foreach ($listingList as $l)
        {
        if ((!$l->installation) && ($l->date == $date) && (!$l->cancelled))
            {
            $list[] = sortingKey($l->starttime . $l->endtime . $l->venue->name) . '<b>' . $l->proposal->title . '</b><br/>' . $l->venue->name . ' / ' . timeRangeToString($l->starttime,$l->endtime) . '<br/>';
            }
        }
    sort($list);
    echo implode('<br/>',$list);
    echo '<br/><br/>';
    }
?>

<br/><br/><br/><hr/><br/>
<em>--ongoing throughout the festival--</em>
<br/><em>(those without dates are full festival)</em>
<br/><br/>

<?php
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


global $proposalList;
$bigInstList = array();
foreach ($venueList as $v)
  {
  $vlist = array();
  foreach ($proposalList as $p)
    {
    $idates = new instDates2();
    foreach ($p->listings as $l)
        {
        if (($l->installation) && ($l->venueid == $v->id) && (!$l->cancelled))
            $idates->dates[] = $l->date;
        } 
    $is = $idates->output();
    if ($is == 'Jul 24 - Aug 3')
        $vlist[] = sortingKey($p->title) . $p->title . '<br/>';
    else if ($is != '')
        $vlist[] = sortingKey($p->title) . $p->title . ' (' . $is . ')<br/>';
    }
  if (count($vlist) > 0)
    {
    sort($vlist);
    $bigInstList[] = sortingKey($v->name) . '<h2>' . $v->name . '</h2>' . implode('',$vlist);
    }
  }
sort($bigInstList);
echo implode("\n",$bigInstList);
?>

</body>
</html>
