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

echo "<ul>\n";
if (hasPrivilege(array('scheduler','organizer')))
    {
    echo <<<ENDSTRING
<li><a href="listBatches.php">Batches</a></li>
<li><a href="batch.php?id=0">All projects</a></li>
<ul><li>(<a href="batch.php?id=0&festival=960">2013 projects</a>)</li></ul>
<li><a href="unbatchedCheck.php">Check for unbatched projects</a></li>
<li><a href="listDeleted.php">Deleted projects</a></li>
<li><a href="listVenues.php">Venues</a>
<ul><li>(<a href="listVenues.php?festival=960">2013 venues</a>)</li></ul>
</li>
<li><a href="calendar.php">Calendar</a></li>
ENDSTRING;
    if (hasPrivilege('scheduler'))
        {
        echo <<<ENDSTRING
<li><a href="listUsers.php">Users</a></li>
<br>
<li>Submit a proposal:
<ul>
<li><a href="newTheatre.php">Theatre</a></li>
<li><a href="newMusic.php">Music</a></li>
<li><a href="newFilm.php">Film/video</a></li>
<li><a href="newVisualart.php">Visual art</a></li>
<li><a href="newDance.php">Dance</a></li>
<li><a href="newLiterary.php">Literary/poetry</a></li>
<li><a href="newStreet.php">Street performance</a></li>
</ul></li>
<li><a href="newProposal.php">New proposal (for someone else)</a></li>
<li><a href="newGroupshow.php">New group show</a></li>
<li><a href="newBatch.php">New batch</a></li>
<li><a href="newVenue.php">New venue</a></li>
<li><a href="newBatchColumn.php">New batch column</a></li>
<li><a href="newCard.php">New card (public contact)</a></li>
<li><a href="subscribe.php">Add person to mailing list</a></li>
<br>
<li><a href="preferences.php">Preferences</a></li>
<br>
ENDSTRING;
        }
    }
else
    {
    echo "<li>Proposals are now closed</li><br>\n";
/*
    if (hasPrivilege('confirmed'))
        {
        echo '<li>Submit a proposal:
<ul>
<li><a href="newTheatre.php">Theatre</a></li>
<li><a href="newMusic.php">Music</a></li>
<li><a href="newFilm.php">Film/video</a></li>
<li><a href="newVisualart.php">Visual art</a></li>
<li><a href="newDance.php">Dance</a></li>
<li><a href="newLiterary.php">Literary/poetry</a></li>
<li><a href="newStreet.php">Street performance</a></li>
</ul></li>';
        }
    else
        echo '<li>Your e-mail address must be verified before you can submit a proposal: <a href="verifyEmail.php">send verification message</a></li>' . "\n";
*/
    }

$festival = getFestivalID();
$stmt = dbPrepare('select id,title from proposal where proposerid=? and festival=? and deleted=0 order by title');
$stmt->bind_param('ii',$_SESSION['userid'],$festival);
$stmt->execute();
$stmt->bind_result($proposalid,$title);
$first = true;
while ($stmt->fetch())
    {
    if ($first)
        {
        echo "<li>Your proposals:<ul>\n";
        $first = false;
        }
    echo "<li><a href='proposal.php?id=$proposalid'>$title</a></li>\n";
    }
if (!$first)
    echo "</ul>\n";
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
<li><a href="editMyContact.php">Edit contact info</a></li>
<li><a href="changePassword.php">Change password</a></li>
<li><a href="logout.php">Logout</a></li>
</ul>

<?php
bifPagefooter();
?>
