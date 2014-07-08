<?php
require_once 'init.php';
connectDB();
require_once 'scheduler.php';
getDatabase();
getPrograminfoList();

if ((array_key_exists('id',$_GET)) && ($_GET['id']) && (is_numeric($_GET['id'])))
    $id=$_GET['id'];
else
    die();

function signlistingRow($id)
    {
    global $listingList;
    global $proposalList;
    $l = $listingList[$id];
    $cancelled = false;
    if ($l->cancelled) $cancelled = true;
    if ($cancelled) $tdtags = ' style="text-decoration: line-through; color:#444"';
    else $tdtags='';
    $s = '<tr>';
    if (1)
        {
        $s .= '<td' . $tdtags . '><span style="font-size:150%; font-style: italic">';
        $s .= str_replace(' ',' ',timeRangeToString($l->starttime,$l->endtime));
        $s .= '</span></td>';
        }
    if (1)
        {
        $p = $l->proposal;
        $s .= '<td' . $tdtags . '><span style="font-size:200%">' . $p->title . '</span>';
        if ($l->venuenote != '')
            $s .= '<br/><em>(' . $l->venuenote . ')</em>';
        if ($p->isgroupshow)
            {
            $s .= '<br/><div style="line-height: 1.25em">featuring:';
            foreach ($p->performers as $perf)
                {
                if ($perf->cancelled) $ptags = ' style="text-decoration: line-through; color:#444"';
                else $ptags = '';
                $s .= '<br/>&nbsp;&nbsp;<span' . $ptags . '>';
                $s .= timeToString($perf->time);
                $s .= '&nbsp;&nbsp;&nbsp;' . $perf->performer->title;
                $s .= '</span>';
                if ($perf->cancelled) $s .= ' (cancelled)';
                }
            $s .= '</div>';
            }
        $s .= '</td>';
        }
    if ($l->cancelled) $s .= '<td><em>cancelled</em></td>';
    $s .= "</tr>\n";
    return $s;
    }


$v = $venueList[$id];
$s = '';

$dayshows = array();
for ($i=0; $i < 11; $i++)
    $dayshows[dayToDate($i)] = array();
foreach ($v->listings as $l)
    {
    if (!$l->installation)
        {
        $s = sortingKey($l->starttime) . signlistingRow($l->id) . '<tr><td></td>';
        if (!$l->cancelled)
            $s .= '<td><div style="margin-left: 2em">' . $programinfoList[$l->proposal->id]->text() . '</div></td></tr>';
        else
            $s .= '<td></td></tr>';
        $dayshows[$l->date][] = $s;
        }
    }

for ($i=0; $i < 11; $i++)
    {
    $date = dayToDate($i);
    if (count($dayshows[$date]) > 0)
        {
        $s .= '<h1>Infringement Festival at ' . $v->name . '<br/>' . date('l, F j',strtotime($date)) . '</h1>';
        $s .= '<div class="rfloat"><img src="/2014_poster.jpg" width="180"></div>';
        sort ($dayshows[$date]);
        $s .= '<table cellpadding="5">';
        foreach ($dayshows[$date] as $row)
                $s .= $row . "\n";
        $s .= '</table>';
        $s .= '<br clear="all" /><br/><br/><br/>Visit WWW.INFRINGEBUFFALO.ORG for the complete schedule of over 700 events at over 80 venues';
        $s .= '<br clear="all" style="page-break-after: always" />' . "\n\n";
        }
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
.rfloat { float:right; margin-left:0.5em; display:inline}
</style>
<title>venue sign</title>
</head>
<body>
<?php
echo $s;
?>
</body>
</html>
