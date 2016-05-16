<?php
require_once 'init.php';
connectDB();
requirePrivilege(array('scheduler','organizer'));
require_once 'util.php';
require_once 'scheduler.php';

bifPageheader('check for scheduling issues');
?>
<p>
This script currently checks for show listings where the end time is before the start time, and ones where the date is outside the range of festival days.
</p>
<p>
Note: you might see incorrect "Date is not during the festival" messages (timezone problem).  If so, log out and back in.
</p>
<?php
getDatabase();
$issues = array();
checkListingTimeConsistency();
checkInvalidDates();
if (count($issues) == 0)
    echo "<p>No issues found.</p>\n";
else
    {
    echo "<ul>\n";
    foreach ($issues as $label => $issuelist)
        {
        echo applyIDMacro("<li>$label</a>:<ul>");
        foreach ($issuelist as $i)
            echo applyIDMacro("<li> $i\n");
        echo "</ul></li>\n";
        }
    echo "</ul>\n";
    }

function addIssue($id,$label,$text)
    {
    global $issues;
    if ($id != 0)
        $label = "{ID:$id $label}";
    if (!array_key_exists($label,$issues))
        $issues[$label] = array();
    $issues[$label][] = $text;
    }

function checkListingTimeConsistency()
    {
    global $listingList;
    foreach ($listingList as $id=>$listing)
        {
        if (!$listing->installation)
            {
            if ($listing->starttime > $listing->endtime)
                addIssue($listing->proposalid,$listing->proposal->title,"Listing {ID:$id}: End time is before start time");
            else if ($listing->starttime == $listing->endtime)
                addIssue($listing->proposalid,$listing->proposal->title,"Listing {ID:$id}: Start and end time are the same");
            }
        }
    }

function checkInvalidDates()
    {
    global $listingList;
    foreach ($listingList as $id=>$listing)
        {
        if (!$listing->installation)
            {
            $daynum = dateToDaynum($listing->date);
            if (($daynum < 0) || ($daynum >= festivalNumberOfDays()))
                addIssue($listing->proposalid,$listing->proposal->title,"Listing {ID:$id}: Date is not during festival ($daynum)");
            }
        }
    }

bifPagefooter();
?>
