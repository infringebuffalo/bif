<?php
require_once 'init.php';
connectDB();
requirePrivilege(array('organizer','scheduler'));
require_once 'util.php';
require_once 'scheduler.php';

bifPageheader('trim brochure info');

getPrograminfoList();

foreach ($programinfoList as $prop)
    {
    if (strlen($prop->brochure_description) > 140)
        {
        $a = substr($prop->brochure_description,0,140);
        $b = substr($prop->brochure_description,140);
        echo "<em><a href='proposal.php?id=$prop->id'>$prop->title</a></em><br><span>$a</span><span style='color:red'>$b</span><br><br>\n";
        }
    }

/*
if (hasPrivilege('admin'))
    {
    foreach ($programinfoList as $prop)
        {
        if (strlen($prop->brochure_description) > 140)
            {
            setProposalInfo($prop->id, 'Description for brochure', substr($prop->brochure_description,0,140));
            }
        }
    }
*/

bifPagefooter();
?>
