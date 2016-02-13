<?php
require_once 'init.php';
requirePrivilege('scheduler');
connectDB();
require_once 'scheduler.php';
$proposal = POSTvalue('proposal',0);
$venue = POSTvalue('venue',0);
$venuenote = POSTvalue('venuenote');
$starttime = POSTvalue('starttime',0);
$endtime = POSTvalue('endtime',0);
$installation = POSTvalue('installation',0);
$note = POSTvalue('note');
for ($d=0; $d < $festivalNumberOfDays; $d++)
    {
    $date = dayToDate($d);
    if ($_POST[$date] == '1')
        {
        $stmt = dbPrepare("insert into listing (date,proposal,venue,venuenote,starttime,endtime,installation,note) values (?,?,?,?,?,?,?,?)");
        $stmt->bind_param('siisiiis',$date,$proposal,$venue,$venuenote,$starttime,$endtime,$installation,$note);
        $stmt->execute();
        $stmt->close();
        }
    }
header("location:".POSTvalue('returnto'));
?>
