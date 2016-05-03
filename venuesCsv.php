<?php
require_once 'init.php';
connectDB();
requirePrivilege(array('scheduler','organizer'));
require_once 'util.php';

class venueRow
    {
    function __construct($id,$name,$info)
        {
        $this->id = $id;
        $this->name = $name;
        $this->info = $info;
        }
    function name()
        {
        return $this->name;
        }
    function id()
        {
        return $this->id;
        }
    function summary($labels)
        {
        $s = array();
        foreach ($labels as $l)
            {
            if (is_array($this->info) && array_key_exists($l,$this->info))
                $value = $this->info[$l];
            else
                $value = '';
            if ($value == '')
                $value = ' ';
            $s[] = $value;
            }
        return $s;
        }
    }

function addSummaryLabels(&$labels,$info)
    {
    if (!is_array($info))
        return;
    foreach ($info as $k=>$v)
        if (!in_array($k,$labels))
            $labels[] = $k;
    }

function simplifyInfo($info,$labels)
    {
    $newinfo = array();
    foreach ($info as $i)
        {
        $label = $i[0];
        foreach ($labels as $l)
            if (strcasecmp($l,$label) == 0)
                $label = $l;
        $newinfo[$label] = $i[1];
        }
    return $newinfo;
    }

log_message("downloaded CSV of venues");

$festival = GETvalue('festival',getFestivalID());
$stmt = dbPrepare('select `id`, `name`, `info_json` from `venue` where `deleted` = 0 and `festival` = ?');
$stmt->bind_param('i',$festival);

$rows = array();
$labels = array();
$stmt->execute();
$stmt->bind_result($id,$name,$info_json);
while ($stmt->fetch())
    {
    if ($name == '')
        $title = '!!NEEDS A NAME!!';
    $info = json_decode($info_json,true);
    $info2 = simplifyInfo($info,$labels);
    $rows[] = new venueRow($id,$name,$info2);
    addSummaryLabels($labels,$info2);
    }
$stmt->close();

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="venues.csv"');
header('Pragma: no-cache');
header('Expires: 0');
$STDOUT = fopen('php://output', 'w');

$fields = array("name", "id");
foreach ($labels as $l)
    $fields[] = $l;
fputcsv($STDOUT, $fields);

foreach ($rows as $r)
    {
    $fields = array($r->name(), $r->id());
    $summary = $r->summary($labels);
    foreach ($summary as $s)
        $fields[] = $s;
    fputcsv($STDOUT, $fields);
    }

fclose($STDOUT);

?>
