<?php
require_once 'init.php';
connectDB();
requirePrivilege(array('scheduler','organizer'));
require_once 'util.php';

$id = GETvalue('id',0);
$commas = GETvalue('commas',0);

if ($id == 0)
    {
    bifPageheader('email for all proposals');
    }
else
    {
    $row = dbQueryByID('select name from `batch` where id=?',$id);
    bifPageheader('email for batch: ' . $row['name']);
    }

echo "<p>E-mail addresses for proposers only (not any other contacts):</p>\n";
$output = "";

if ($id == 0)
    {
    $festival = GETvalue('festival',getFestivalID());
    $stmt = dbPrepare('select email from user join proposal on proposerid=user.id where proposal.festival=? and deleted=0 order by title');
    $stmt->bind_param('i',$festival);
    }
else
    {
    $stmt = dbPrepare('select email from user join proposal on proposerid=user.id join proposalBatch on proposal.id=proposalBatch.proposal_id where proposalBatch.batch_id=? and deleted=0 order by title');
    $stmt->bind_param('i',$id);
    }
$stmt->execute();
$stmt->bind_result($email);
while ($stmt->fetch())
    {
    if (($commas) && (strlen($output) > 0))
        $output .= ", ";
    $output .= $email;
    if (!$commas)
        $output .= "<br>\n";
    }
$stmt->close();

echo $output;

if ($commas)
    echo "<p><a href='batchEmail.php?id=$id&commas=0'>(list without commas)</a></p>";
else
    echo "<p><a href='batchEmail.php?id=$id&commas=1'>(list with commas)</a></p>";
bifPagefooter();
?>
