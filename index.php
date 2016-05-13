<?php
require_once 'init.php';
require_once 'util.php';
connectDB();

if (!loggedIn())
    {
    header('Location: loginForm.php');
    die();
    }

bifPageheader('proposals database');

if ((array_key_exists('adminmessage',$_SESSION)) && ($_SESSION['adminmessage'] != ''))
    {
    echo '<div style="background:#ffff80; padding:0.5em; width:790px;">' . $_SESSION['adminmessage'] . '</div>';
    unset($_SESSION['adminmessage']);
    }

if (hasPrivilege('scheduler'))
    {
    echo "<div style='float:right; width:40%; border-style:solid; border-width:1px'>\n<p>Scheduler changes:\n<ul>\n";
    echo "<li>13 May '16 - new feature: list of festival contacts - please add some</li>\n";
    echo "<li>13 May '16 - can now revoke proposer's access to schedule</li>\n";
    echo "<li>6 May '16 - added list of a group show performers' contact emails</li>\n";
    echo "<li>6 May '16 - added simple search function</li>\n";
    echo "<li>6 May '16 - can now get list of all of a batch's contact emails (not just proposers)</li>\n";
    echo "<li>5 May '16 - deleting a show will delete any scheduled performances it has</li>\n";
    echo "<li>5 May '16 - batches can be deleted</li>\n";
    echo "<li>3 May '16 - some internal data converted to JSON; this should avoid future encoding errors</li>\n";
    echo "</p></div>\n";
    }

echo "<ul>\n";
if (hasPrivilege(array('scheduler','organizer')))
    {
    echo <<<ENDSTRING
<li><a href="listBatches.php">Batches</a></li>
<ul><li><a href="newBatch.php">New batch</a></li>
    <li><a href="unbatchedCheck.php">Check for unbatched projects</a></li>
</ul>
<li><a href="batch.php?id=0">All projects</a></li>
<ul><li>(<a href="batch.php?id=0&festival=4200">2015 projects</a>)</li>
    <li>(<a href="batch.php?id=0&festival=4102">2014 projects</a>)</li>
    <li>(<a href="batch.php?id=0&festival=960">2013 projects</a>)</li>
    <li><a href="listDeleted.php">Deleted projects</a></li>
    <li><form method="POST" action="search.php"><input type="text" name="target" size="10" value=""><input type="submit" name="submit" value="search"></form></li>
</ul>
<li><a href="listCategories.php">Categories (for public website)</a></li>
<li><a href="listVenues.php">Venues</a>
<ul><li><a href="newVenue.php">make new venue</a></li></ul>
<ul><li>(<a href="listVenues.php?festival=4200">2015 venues</a>)</li>
    <li>(<a href="listVenues.php?festival=4102">2014 venues</a>)</li>
    <li>(<a href="listVenues.php?festival=960">2013 venues</a>)</li></ul>
</li>
<li><a href="calendar.php">Calendar</a></li>
<li><a href="listUsers.php">Users</a></li>
ENDSTRING;
    }
if (hasPrivilege('admin'))
    {
    echo "<li><a href='newContact.php'>New festival contact</a></li>\n";
    echo "<li><a href='log.php'>View log</a></li>\n";
    }
if (hasPrivilege('scheduler'))
    {
    echo "<li><a href='preferences.php'>Preferences</a></li>\n";
    echo "<br><li><a href='newGroupshow.php'>Create a Group Show</a></li>\n";
    }
    
if (hasPrivilege(array('confirmed','scheduler','organizer')))
    {
    if (hasPrivilege(array('scheduler','organizer')))
        echo "<br><br><li><a href='Infringement_Proposal.php'>Submit a proposal</a></li><br>\n";
    }
else
    {
    echo "<li>Your e-mail address must be verified before you can submit a proposal: <a href='verifyEmail.php'>send verification message</a></li><br>\n";
    }

if (hasPrivilege(array('confirmed','scheduler','organizer')))
    {
    echo "<li><a href='listContacts.php'>Festival contacts</a></li><br>\n";
    }


$festival = getFestivalID();
$stmt = dbPrepare('select id,title,isgroupshow from proposal where proposerid=? and festival=? and deleted=0 order by title');
$stmt->bind_param('ii',$_SESSION['userid'],$festival);
$stmt->execute();
$stmt->bind_result($proposalid,$title,$isgroupshow);
$first = true;
while ($stmt->fetch())
    {
    if ($first)
        {
        echo "<li>My proposals:<ul>\n";
        $first = false;
        }
		if ($isgroupshow == 0){
          echo "<li><a href='proposal.php?id=$proposalid'>$title</a></li>\n";
	    }else{
		  echo "<li><a href='proposal.php?id=$proposalid'>(Group Show) - $title</a></li>\n";
		}
    }
if (!$first)
    echo "</ul>\n";
else
    echo "<li>You have not submitted any proposals\n<br>\n";
$stmt->close();

/*
$stmt = dbPrepare('select id,title from proposal where proposerid=? and festival!=? and deleted=0 order by title');
$stmt->bind_param('ii',$_SESSION['userid'],$festival);
$stmt->execute();
$stmt->bind_result($proposalid,$title);
$first = true;
while ($stmt->fetch())
    {
    if ($first)
        {
        echo "<li>Your proposals from previous festivals:<ul>\n";
        $first = false;
        }
    echo "<li><a href='proposal.php?id=$proposalid'>$title</a></li>\n";
    }
if (!$first)
    echo "</ul>\n";
$stmt->close();
*/
?>

<br>
<li><a href="editUserInfo.php">Edit my contact info</a></li>
<li><a href="changePassword.php">Change password</a></li>
<li><a href="logout.php">Logout</a></li>
</ul>

<?php
bifPagefooter();
?>
