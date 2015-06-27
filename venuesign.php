<?php
require_once 'init.php';
ini_set('display_errors','1');
connectDB();
require_once 'scheduler.php';
getDatabase();
getPrograminfoList();

if ((array_key_exists('id',$_GET)) && ($_GET['id']) && (is_numeric($_GET['id'])))
    $id=$_GET['id'];
else
    die();

function qrcode($url)
    {
    $query = urlencode("qrcode $url");
    $ddg = "http://api.duckduckgo.com/?q=$query&format=json";
    $json = file_get_contents($ddg);
    $data = json_decode($json);
    preg_match('/<img.*>/U',$data->Answer,$matches);
    return $matches[0];
    }

function signlistingRow($id)
    {
    global $listingList;
    global $proposalList;
    global $programinfoList;
    $l = $listingList[$id];
    $cancelled = false;
    if ($l->cancelled) $cancelled = true;
    if ($cancelled) $tdtags = ' style="text-decoration: line-through; color:#444"';
    else $tdtags='';
    $p = $l->proposal;
    $img = qrcode('http://infringebuffalo.org/show.php?id=' . $p->id);
    $s = '<tr>';
    $s .= '<td' . $tdtags . '><span style="font-size:150%; font-style: italic">';
    $s .= str_replace(' ',' ',timeRangeToString($l->starttime,$l->endtime));
    $s .= '</span></td>';
    $s .= "<td $tdtags><span style='font-size:200%'>" . $p->title . "</span>\n";
    if ($l->venuenote != '')
        $s .= '<br><em>(' . $l->venuenote . ")</em>\n";
    if (!$l->cancelled)
        $s .= "<br>\n<div style='margin-left: 2em'>\n" . $programinfoList[$p->id]->text() . "</div>\n";
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
    $s .= "</td>\n";
    if ($l->cancelled)
        $s .= "<td><em>cancelled</em></td>\n";
    else
        $s .= "<td>$img</td>\n";
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
        $s = sortingKey($l->starttime) . signlistingRow($l->id);
        $dayshows[$l->date][] = $s;
        }
    }

$sign = '';
for ($i=0; $i < 11; $i++)
    {
    $date = dayToDate($i);
    if (count($dayshows[$date]) > 0)
        {
        $sign .= '<h1>Infringement at ' . $v->name . '<br/>' . date('l, F j',strtotime($date)) . "</h1>\n";
/*        $sign .= '<div class="rfloat"><img src="/2014_poster.jpg" width="180"></div>'; */
        sort ($dayshows[$date]);
        $sign .= "<table cellpadding='5'>\n";
        foreach ($dayshows[$date] as $row)
                $sign .= $row . "\n";
        $sign .= "</table>\n";
        $sign .= '<br clear="all" /><br/><br/><br/>Visit WWW.INFRINGEBUFFALO.ORG for the complete schedule of over 700 events at over 90 venues';
        $sign .= "<br clear='all' style='page-break-after: always' />\n\n";
        }
    }


$sign .= "<div style='text-align:center'>\n";
$sign .= "<h1>Buffalo Infringement Festival</h1>\n<br><br><br>\n";
$sign .= "<p style='font-size:x-large'>Scan code to see what's happening nearby NOW!:</p>\n";
$sign .= str_replace('<img','<img width="300"',qrcode("http://infringebuffalo.org/near.php?venue=$id&qr=1"));
$stmt = dbPrepare('select info from venue where id=?');
$stmt->bind_param('i',$id);
$stmt->execute();
$stmt->bind_result($info_ser);
$stmt->fetch();
$stmt->close();
$info = unserialize($info_ser);
$nearurl = getInfo($info,"near shows url");
if ($nearurl != '')
    $sign .= "<br><br>\n<p>Or visit<br> $nearurl</p>\n";
$sign .= "</div>\n";

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
echo $sign;
?>
</body>
</html>
