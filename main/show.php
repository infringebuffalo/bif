<?php
require 'db2/init.php';
connectDB();
require 'db2/scheduler.php';
getDatabase();
$id=$_GET['id'];
if (!is_numeric($id)) die();

$stmt = dbPrepare('select title,info from proposal where id=?');
$stmt->bind_param('i',$id);
$stmt->execute();
$stmt->bind_result($title,$info_ser);
if (!$stmt->fetch())
    {
    $stmt->close();
    die('no such show');
    }
$stmt->close();
$info = unserialize($info_ser);

$web_description = getInfo($info,'Description for web');
$image = getInfo($info,'Image link');
$icon = getInfo($info,'icon');
$website = getInfo($info,'Website');
$brochure_description = getInfo($info,'Description for brochure');

$headerExtras = '<meta name="title" content="' . $title . '" />' . "\n";
/*
if ($image != '')
    $headerExtras .= '<link rel="image_src" href="' . $image . '" />' . "\n";
*/
require 'bif.php';
bifPageheader($title,$headerExtras);

$brochureText = '';
if ($icon != '')
    $brochureText .= '<img align="right" src="db2/uploads/file' . $icon . '.jpg">';
else if (strlen($image) > 7)
    $brochureText .= '<img align="right" width=300 src="' . $image . '">';
if (strlen($web_description) > 2)
    $brochureText .= str_replace("\n", "<br>\n", $web_description);
else if ($brochure_description != '')
    $brochureText .= $brochure_description . '<br/>';
$brochureText .= '<p>';
if ($website != '') $brochureText .= '<b>Website:</b> ' . linkedURL($website) . '<br/>';
$brochureText .= '</p>';

$brochureText .= HTML_scheduleNonadmin($id);

echo $brochureText;

echo '<br/>';
echo '<a name="fb_share" type="icon_link" href="http://www.facebook.com/sharer.php">Share</a><script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>';

bifPagefooter();

function getInfo($info,$field)
    {
    foreach ($info as $i)
        if (is_array($i) && array_key_exists(0,$i) && ($i[0] == $field))
            return $i[1];
    return '';
    }

function HTML_scheduleNonadmin($id)
    {
    $out = '<div class="schedulebox"><b>Schedule:</b><table>';
    
    global $proposalList;
    $prop = $proposalList[$id];
    
    $list = array();
    foreach ($prop->listings as $l)
        if ($l->proposal->id == $prop->id)
            $list[] = sortingKey($l->date . $l->starttime) . str_replace('proposal.php','show.php',listingRow($l->id,true,true,true,false,false));
    foreach ($prop->groupshows as $g)
        {
        foreach ($g->groupevent->listings as $l)
            $list[] = sortingKey($l->date . $l->starttime) . str_replace('proposal.php','show.php',listingRow($l->id,true,true,true,true,true,'','',$id));
        }
    sort($list);
    $out .= implode("\n",$list);
    $out .= '</table>';
    
    if ($prop->isgroupshow)
        {
        $out .= '<p>Performers:</p><table>';
        foreach ($prop->performers as $perf)
            {
            if ($perf->cancelled) $ptags = ' style="text-decoration: line-through; color:#444"';
            else $ptags = '';
            $out .= '<tr>';
/*
            if ($prop->grouplistmode == 0)
                { if ($perf->showorder != 0) $out .= '<td' . $ptags . '>' . $perf->showorder . '</td>'; }
            else if ($prop->grouplistmode == 1)
                $out .= '<td' . $ptags . '>' . timeToString($perf->time) . '</td>';
            else 
*/
                {
                $out .= '<td' . $ptags . '>' . $perf->showorder . '</td>';
                $out .= '<td' . $ptags . '>' . timeToString($perf->time) . '</td>';
                }
            $out .= '<td' . $ptags . '><a href="show.php?id=' . $perf->performerid . '">' . $perf->performer->title . '</a></td>';
            $out .= '<td' . $ptags . '>' . $perf->note . '</td>';
            if ($perf->cancelled) $out .= '<td>(cancelled)</td>';
            $out .= '</tr>';
            } 
        $out .= '</table>';
        }
    $out .= '</div>';
    return $out; 
    }
?>
