<?php
require 'db2/init.php';
connectDB();
require 'db2/scheduler.php';
getDatabase();

$list = array();
foreach ($venueList as $v)
    {
    if (count($v->listings) > 0)
        {
        $row = sortingKey($v->name) . '<tr><td><a href="venue.php?id=' . $v->id . '">' . $v->name . '</a></td>';
        $showdays = array();
        $instdays = array();
        foreach ($v->listings as $l)
            if (!$l->installation)
                $showdays[$l->date] = 1;
            else
                $instdays[$l->date] = 1;
        $row .= '<td><table><tr><colgroup span="11" width="24"></colgroup>';
        for ($i=0; $i < 11; $i++)
            {
            if (isset($showdays[dayToDate($i)]))
                $row .= '<td style="background:#8f8">' . (($i+24)%31+1) . '</td>';
            else
                $row .= '<td>&nbsp;&nbsp;</td>';
            }
        $row .= '</tr></table></td>';
        $row .= '<td><table><tr><colgroup span="11" width="24"></colgroup>';
        for ($i=0; $i < 11; $i++)
            {
            if (isset($instdays[dayToDate($i)]))
                $row .= '<td style="background:#8f8">' . (($i+24)%31+1) . '</td>';
            else
                $row .= '<td>&nbsp;&nbsp;</td>';
            }
        $row .= '</tr></table></td>';
        $list[] = $row . '</tr>';
        }
    }
sort($list);
$s = '<table class="colorized"><thead><tr><th>venue</th><th>performance dates</th><th>installation dates</th></tr></thead>';
$s .= implode("\n",$list);
$s .= '</table>';

require 'bif.php';
bifPageheader('All venues');
echo $s;
bifPagefooter();
?>
