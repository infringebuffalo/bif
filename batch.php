<?php
require_once 'init.php';
connectDB();
requireLogin();
require_once 'util.php';
require '../bif.php';

if (!isset($_GET['id']))
    die('no batch id given');
else
    $id = $_GET['id'];

$row = dbQueryByID('select name,description from `batch` where id=?',$id);
bifPageheader('batch: ' . $row['name']);

echo "<p>$row[description]</p>\n";
echo "<p><a href='editBatch.php?id=$id'>[edit batch]</a></p>";

$stmt = dbPrepare('select proposal.id, proposerid, name, title, orgfields from proposal join user on proposerid=user.id join proposalBatch on proposal.id=proposalBatch.proposal_id where proposalBatch.batch_id=?');
$stmt->bind_param('i',$id);
$stmt->execute();
$stmt->bind_result($id,$proposer_id,$proposer_name,$title,$orgfields_ser);
echo "<table>\n";
while ($stmt->fetch())
    {
    if ($title == '')
        $title = '!!NEEDS A TITLE!!';
    $orgfields = unserialize($orgfields_ser);
    echo "<tr><td><a href='proposal.php?id=$id'>$title</a></td><td><a href='user.php?id=$proposer_id'>$proposer_name</a></td></tr>\n";
    }
echo "</table>\n";
$stmt->close();

bifPagefooter();
?>
