<?php
require_once 'init.php';
require_once 'util.php';

if (hasPrivilege('scheduler'))
    {
    echo <<<ENDSTRING
<ul>
<li><a href="listBatches.php">Batches</a></li>
<li><a href="listProposals.php">All proposals</a></li>
<li><a href="listVenues.php">Venues</a></li>
<li><a href="calendar.php">Calendar</a></li>
<br>
<li><a href="newGroupshow.php">New group show</a></li>
<li><a href="newBatch.php">New batch</a></li>
<li><a href="newVenue.php">New venue</a></li>
<li><a href="newCard.php">New card (public contact)</a></li>
<li><a href="logout.php">Logout</a></li>
</ul>
ENDSTRING;
    }
else
    {
    echo "My proposals [TBD]";
    }
?>
