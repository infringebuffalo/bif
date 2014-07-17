<?php
require_once 'init.php';
connectDB();
requirePrivilege(array('scheduler','organizer'));
require_once 'util.php';

if (!isset($_GET['id']))
    die('no listing id given');
else
    $listing_id = $_GET['id'];

$row = dbQueryByID('select listing.proposal,listing.venue,venuenote,date,starttime,endtime,installation,note,cancelled,proposal.title,venue.name from listing join proposal on listing.proposal=proposal.id join venue on listing.venue=venue.id where listing.id=?',$listing_id);
bifPageheader("listing for $row[title] at $row[name]");

if ($row['cancelled'] != 0)
    echo "<p>THIS LISTING IS CANCELLED</p>\n";
echo "<p>\n";
if ($row['installation'] == 1)
    echo "<a href='proposal.php?id=$row[proposal]'>$row[title]</a> installed at <a href='venue.php?id=$row[venue]'>$row[name]</a> ($row[venuenote]) on $row[date]\n";
else
    echo "<a href='proposal.php?id=$row[proposal]'>$row[title]</a> at <a href='venue.php?id=$row[venue]'>$row[name]</a> ($row[venuenote]) on $row[date], from $row[starttime] to $row[endtime]\n";
echo "</p>\n";
if ($row['note'] != '')
    echo "<p><em>Note:</em>$row[note]</p>\n";

bifPagefooter();
?>
