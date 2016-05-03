<?php
require 'init.php';
connectDB();
requirePrivilege(array('scheduler','organizer'));
require 'util.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="volinfo.csv"');
header('Pragma: no-cache');
header('Expires: 0');
$STDOUT = fopen('php://output', 'w');

$festival = getFestivalID();
$stmt = dbPrepare('select id,title,info_json from proposal where deleted=0 and festival=? order by title');
$stmt->bind_param('i',$festival);
$stmt->execute();
$stmt->bind_result($id,$title,$info_json);
while ($stmt->fetch())
    {
    $info = json_decode($info_json,true);
    $url = "http://infringebuffalo.org/db2/proposal.php?id=$id";
    $contact = getInfo($info,"Contact info");
    $type = getInfo($info,"Type");
    $volunteer = getInfo($info,"Volunteering");
    $contact = str_replace("\n"," ",$contact);
    $volunteer = str_replace("\n"," ",$volunteer);
    fputcsv($STDOUT,array($title,$url,$contact,$volunteer),',','"');
    }
fclose($STDOUT);
?>
