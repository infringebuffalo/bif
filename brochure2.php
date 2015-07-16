<?php
/* Outputs schedule information in form requested by James, for 2015 brochure */

require_once 'init.php';
connectDB();
requireLogin();
require_once 'scheduler.php';
ini_set('display_errors','1');

if (!isset($_GET['t']))
    $type = 'title';
else
    $type = $_GET['t'];

if (!isset($_GET['g']))
    $genre = '';
else
    $genre = $_GET['g'];

getDatabase();
getPrograminfoList();
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Buffalo Infringement Festival brochure info</title>
<link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>
<?php

function generateList($genre,$type)
    {
    global $festivalNumberOfDays, $listingList, $programinfoList;
    for ($i = 0; $i < $festivalNumberOfDays; $i++)
        {
        $date = dayToDate('day' . $i);
        $dateText = dateToString($date);
        $list = array();
        foreach ($listingList as $l)
            {
            $pinfo = $programinfoList[$l->proposal->id];
            if ((!$l->installation) && ($l->date == $date) && (!$l->cancelled) && (($genre == '') || (!strcasecmp($pinfo->type,$genre))))
                {
                $s = sortingKey($l->starttime . $l->endtime . $l->venue->name);
                if ($type == 'title')
                    $list[] = $s . $l->proposal->title;
                else if ($type == 'venue')
                    $list[] = $s . $l->venue->name;
                else if ($type == 'time')
                    $list[] = $s . timeRangeToString($l->starttime,$l->endtime);
                else if ($type == 'description')
                    {
                    if ($pinfo->brochure_description == '')
                        $list[] = $s . $pinfo->website . ' TBD';
                    else
                        $list[] = $s . $pinfo->website . ' ' . $pinfo->brochure_description;
                    }
                else if ($type == 'date')
                    $list[] = $s . $dateText;
                }
            }
        sort($list);
        echo implode("\n<br><br>\n",$list);
        if (count($list) > 0)
            echo "\n<br><br>\n";
        }
    }

function generateShowCount($genre)
    {
    global $festivalNumberOfDays, $listingList, $programinfoList;
    $count = array();
    foreach ($listingList as $l)
        {
        $id = $l->proposal->id;
        if (!array_key_exists($id,$count))
            $count[$id] = 0;
        $count[$id] = $count[$id] + 1;
        }
    $list = array();
    foreach ($programinfoList as $p)
        {
        if (($genre == '') || (!strcasecmp($p->type,$genre)))
            $list[] = $p->title . ' ::: ' . $count[$p->id];
        }
    natcasesort($list);
    echo implode("\n<br><br>\n",$list);
    }


if ($type == 'count')
    generateShowCount($genre);
else
    generateList($genre, $type);

?>

</body>
</html>
