<?php
require_once 'init.php';
require_once 'util.php';
require '../bif.php';
connectDB();

if (!loggedIn())
    {
    header('Location: loginForm.php');
    die();
    }

bifPageheader('proposals database');

echo "<ul>\n";
if (hasPrivilege('scheduler'))
    {
    echo <<<ENDSTRING
<li><a href="listBatches.php">Batches</a></li>
<li><a href="listProposals.php">All proposals</a></li>
<li><a href="listVenues.php">Venues</a></li>
<li><a href="calendar.php">Calendar</a></li>
<br>
<li><a href="newGroupshow.php">New group show</a></li>
<li><a href="newBatch.php">New batch</a></li>
<li><a href="newVenue.php">New venue</a></li>
<li><a href="newCard.php">New card (public contact)</a></li>
ENDSTRING;
    }
else
    {
    echo "<li>My proposals [TBD]</li>\n";
    }

echo <<<ENDSTRING
<br>
<li><a href="editMyContact.php">Edit contact info</a></li>
<li><a href="changePassword.php">Change password</a></li>
<li><a href="logout.php">Logout</a></li>
</ul>
ENDSTRING;

bifPagefooter();
?>
