<?php
require 'init.php';
connectDB();
requirePrivilege(array('scheduler','organizer'));

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="volinfo.csv"');
header('Pragma: no-cache');
header('Expires: 0');
$STDOUT = fopen('php://output', 'w');

$stmt = dbPrepare('select id,title,info from proposal where deleted=0 order by title');
$stmt->execute();
$stmt->bind_result($id,$title,$info_ser);
while ($stmt->fetch())
    {
    $info = unserialize($info_ser);
    $url = "http://infringebuffalo.org/db2/proposal.php?id=$id";
    $contact = getInfo($info,"Contact info");
    $type = getInfo($info,"Type");
    if ($type == 'music')
        {
        $help = getInfo($info,"How help BIF");
        $volunteer = getInfo($info,"Will volunteer");
        }
    else
        {
        $help = getInfo($info,"How will you help infringement");
        $volunteer = getInfo($info,"What can you provide to help");
        }
    $contact = str_replace("\n"," ",$contact);
    $help = str_replace("\n"," ",$help);
    $volunteer = str_replace("\n"," ",$volunteer);
    fputcsv($STDOUT,array($title,$url,$contact,$help,$volunteer),',','"');
    }
fclose($STDOUT);

function getInfo($info,$field)
    {
    foreach ($info as $i)
        if (is_array($i) && array_key_exists(0,$i) && ($i[0] == $field))
            return $i[1];
    return '';
    }
?>
