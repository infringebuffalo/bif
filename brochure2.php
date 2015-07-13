<?php
/* Outputs schedule information in form requested by James, for 2015 brochure */

require_once 'init.php';
connectDB();
requireLogin();
require_once 'scheduler.php';

if (!isset($_GET['t']))
    $type = 'title';
else
    $type = $_GET['t'];

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
for ($i = 0; $i < $festivalNumberOfDays; $i++)
    {
    $date = dayToDate('day' . $i);
    $dateText = dateToString($date);
    $list = array();
    foreach ($listingList as $l)
        {
        if ((!$l->installation) && ($l->date == $date) && (!$l->cancelled))
            {
            if ($type == 'title')
                $list[] = sortingKey($l->starttime . $l->endtime . $l->venue->name) . $l->proposal->title;
            else if ($type == 'venue')
                $list[] = sortingKey($l->starttime . $l->endtime . $l->venue->name) . $l->venue->name;
            else if ($type == 'time')
                $list[] = sortingKey($l->starttime . $l->endtime . $l->venue->name) . $dateText . ' ' . timeRangeToString($l->starttime,$l->endtime);
            }
        }
    sort($list);
    echo implode('<br>',$list);
    echo '<br>';
    }
?>

</body>
</html>
