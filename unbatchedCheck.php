<?php
require_once 'init.php';
connectDB();
requireLogin();
requirePrivilege(array('scheduler','organizer'));
require_once 'util.php';

if (isset($_POST['batch']))
    $checkedBatches = $_POST['batch'];
else
/*** UGLY BIT: to make things easier right now - these are the default "primary batches" for 2014 ***/
    $checkedBatches = array(4202, 4203, 4204, 4205, 4206, 4207, 4208, 4480);

bifPageheader('check for unbatched proposals');
?>
<p>
Select the batches that are considered "primary" and are being scheduled by someone.  This script will report any proposals that are NOT in any of the selected batches.
</p>
<form method="POST" action="unbatchedCheck.php">
<table>

<?php
$stmt = dbPrepare('select `id`, `name` from `batch` where `festival` = ? order by name');
$stmt->bind_param('i',getFestivalID());
$stmt->execute();
$stmt->bind_result($batch_id,$batch_name);
$count = 0;
while ($stmt->fetch())
    {
    if ($count % 3 == 0)
        echo "<tr>\n";
    echo "<td><input type='checkbox' name='batch[]' value='$batch_id'";
    if (in_array($batch_id,$checkedBatches))
        echo " checked";
    echo "><a href='batch.php?id=$batch_id'>$batch_name</a></td>\n";
    $count = $count + 1;
    if ($count % 3 == 0)
        echo "</tr>\n";
    }
$stmt->close();
?>

</table>
<input type="submit" name="submit" value="Perform check" />
</form>

<?php
if (isset($_POST['submit']))
    {
    echo "<p>These proposals are in none of the selected batches:</p>\n<ul>\n";
    $stmt = dbPrepare('select proposal_id,batch_id from proposalBatch join proposal on proposal_id=proposal.id where proposal.festival=? and proposal.deleted=0');
    $stmt->bind_param('i',getFestivalID());
    $stmt->execute();
    $stmt->bind_result($proposal_id,$batch_id);
    $proposalBatch = array();
    while ($stmt->fetch())
        {
        if (!isset($proposalBatch[$proposal_id]))
            $proposalBatch[$proposal_id] = array();
        $proposalBatch[$proposal_id][] = $batch_id;
        }
    $stmt->close();

    $stmt = dbPrepare('select id,title from proposal where festival=? and deleted=0');
    $stmt->bind_param('i',getFestivalID());
    $stmt->execute();
    $stmt->bind_result($proposal_id,$title);
    $foundOne = False;
    while ($stmt->fetch())
        {
        $bad = False;
        if (isset($proposalBatch[$proposal_id]))
            {
            if (count(array_intersect($checkedBatches, $proposalBatch[$proposal_id])) == 0)
                $bad = True;
            }
        else
            $bad = True;
        if ($bad)
            {
            echo "<li><a href='proposal.php?id=$proposal_id'>$title</a></li>\n";
            $foundOne = True;
            }
        }
    $stmt->close();
    echo "</ul>\n";
    if (!$foundOne)
        echo "<p><em>None found</em></p>\n";
    }

bifPagefooter();
?>
