<?php
die('disabled');
require_once 'init.php';
connectDB();
requirePrivilege(array('admin'));
require_once 'util.php';
require_once 'scheduler.php';

bifPageheader('autobatching');

$frombatch = 1035; /* Music batch */

$stmt = dbPrepare('select proposal.id, title from proposal join proposalBatch on proposal.id=proposalBatch.proposal_id where proposalBatch.batch_id=? and deleted=0 order by title');
$stmt->bind_param('i',$frombatch);

$props = array();
$stmt->execute();
$stmt->bind_result($id,$title);
while ($stmt->fetch())
    {
    if ($title == '')
        $title = '!!NEEDS A TITLE!!';
    $props[$id] = $title;
    }
$stmt->close();

$festival = getFestivalID();

echo "<pre>\n";
foreach ($props as $id=>$title)
    {
    $genre = getProposalInfo($id,'Main genre');
    if ($genre != '')
        {
        $batchname = 'music: ' . $genre;
        $batchid = getBatch($batchname,$festival,true,"All \"$genre\" music proposals");
        if ($batchid != 0)
            {
            echo "$title => $batchname\n";
            addToBatch($id,$batchid);
            }
        else
            echo "$title - no main genre given\n";
        }
    }
echo "</pre>\n";

bifPagefooter();

?>
