<?php
require_once 'init.php';
connectDB();
requirePrivilege(array('organizer','scheduler'));
require_once 'util.php';
require_once 'scheduler.php';

$trimLength = GETvalue('trim',140);

bifPageheader('trim brochure info');

getPrograminfoList();

foreach ($programinfoList as $prop)
    {
    if (strlen($prop->brochure_description) > $trimLength)
        {
        $a = substr($prop->brochure_description,0,$trimLength);
        $b = substr($prop->brochure_description,$trimLength);
        echo "<em><a href='proposal.php?id=$prop->id'>$prop->title</a></em><br><span>$a</span><span style='color:red'>$b</span><br><br>\n";
        }
    }

bifPagefooter();
?>
