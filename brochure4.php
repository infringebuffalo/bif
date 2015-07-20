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

function listData($prop, $l, $g, $type)
    {
    global $proposalList, $programinfoList;
    if ($type == 'title')
        return $prop->title;
    else if ($type == 'venue')
        {
        if ($g == NULL)
            return $l->venue->name;
        else
            return $l->venue->name . ' [' . $g->groupevent->title . ']';
        }
    else if ($type == 'time')
        {
        if ($g == NULL)
            return timeRangeToString($l->starttime,$l->endtime);
        else
            return timeToString($g->time);
        }
    else if ($type == 'description')
        {
        $pinfo = $programinfoList[$prop->id];
        if ($pinfo->brochure_description == '')
            return $pinfo->website . ' TBD';
        else
            return $pinfo->website . ' ' . $pinfo->brochure_description;
        }
    else if ($type == 'date')
        return dateToString($l->date);
    }

function generateList($genre,$type)
    {
    global $proposalList, $programinfoList;
    $list = array();
    foreach ($programinfoList as $p)
      {
      if (($genre=='') || (!strcasecmp($p->type,$genre)))
        {
        $prop = $proposalList[$p->id];
        if (!$prop->deleted)
            {
            if (($type == 'title') || ($type == 'description'))
                $list[] = sortingKey($prop->title) . listData($prop, NULL, NULL, $type);
            else
                {
                $sublist = array();
                foreach ($prop->listings as $l)
                    {
                    if ((!$l->installation) && ($l->proposal->id == $p->id) && (!$l->cancelled))
                        {
                        $sublist[] = sortingKey($l->date . $l->starttime) . listData($prop,$l,NULL,$type);
                        }
                    }
                foreach ($prop->groupshows as $g)
                    {
                    if (!$g->cancelled)
                        {
                        foreach ($g->groupevent->listings as $l)
                            if ((!$l->installation) && (!$l->cancelled))
                                {
                                $sublist[] = sortingKey($l->date . $l->starttime) . listData($prop,$l,$g,$type);
                                }
                        }
                    }
                sort($sublist);
                $list[] = sortingKey($prop->title) . implode("\n<br>\n",$sublist);
                }
            }
        }
      }
    sort($list);
    echo implode("\n<br><br>\n",$list);
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
        if ($l->proposal->isgroupshow)
            {
            foreach ($l->proposal->performers as $perf)
                {
                $id = $perf->performer->id;
                if (!array_key_exists($id,$count))
                    $count[$id] = 0;
                $count[$id] = $count[$id] + 1;
                }
            }
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
