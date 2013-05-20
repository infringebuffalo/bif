<?php
require_once 'init.php';
connectDB();
requireLogin();

$batchid = GETvalue('id');
$cur = GETvalue('cur');
$dir = GETvalue('dir');

$stmt = dbPrepare('select proposal_id from proposalBatch join proposal on proposal.id=proposalBatch.proposal_id where proposalBatch.batch_id=? order by title');
$stmt->bind_param('i',$batchid);
$stmt->execute();
$stmt->bind_result($proposalid);
$first = 0;
$prev = 0;
$dest = 0;
while ($stmt->fetch())
    {
    if (!$first)
        $first = $proposalid;
    if (($dir == -1) && ($proposalid == $cur) && ($prev != 0))
        {
        $dest = $prev;
        break;
        }
    else if (($dir == 1) && ($prev == $cur))
        {
        $dest = $proposalid;
        break;
        }
    $prev = $proposalid;
    }
$stmt->close();
if ($dest == 0)
    {
    if ($dir == -1)
        $dest = $proposalid;
    else
        $dest = $first;
    }

header("Location: proposal.php?id=$dest");
?>
