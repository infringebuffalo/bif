<?php
require_once 'init.php';
connectDB();
requirePrivilege(array('scheduler','organizer'));
require_once 'util.php';

$id = GETvalue('id',0);

if ($id != 0)
    {
    $row = dbQueryByID('select name from `batch` where id=?',$id);
    $batchTitle = $row['name'];
    }
else
    {
    $batchTitle = 'allproposals';
    }


class propRow
    {
    function __construct($id,$title,$proposer_id,$proposer_name,$orgfields,$submitted)
        {
        $this->id = $id;
        $this->title = $title;
        $this->proposer_id = $proposer_id;
        $this->proposer_name = $proposer_name;
        $this->orgfields = $orgfields;
        $this->submitted = $submitted;
        }
    function title()
        {
        return $this->title;
        }
    function proposer()
        {
        return $this->proposer_name;
        }
    function submitted()
        {
        return $this->submitted;
        }
    function summary($labels)
        {
        $s = array();
        $i = 0;
        foreach ($labels as $l)
            {
            $idnum = $this->id . '_' . $i;
            if (is_array($this->orgfields) && array_key_exists($l,$this->orgfields))
                $value = $this->orgfields[$l];
            else
                $value = '';
            if ($value == '')
                $value = ' ';
            $s[] = $value;
            $i = $i + 1;
            }
        return $s;
        }
    }

function addSummaryLabels(&$labels,$orgfields)
    {
    if (!is_array($orgfields))
        return;
    foreach ($orgfields as $k=>$v)
        if (!in_array($k,$labels))
            $labels[] = $k;
    }

if ($id != 0)
    {
    $stmt = dbPrepare('select proposal.id, proposerid, name, title, orgfields_json, submitted from proposal join user on proposerid=user.id join proposalBatch on proposal.id=proposalBatch.proposal_id where proposalBatch.batch_id=? and deleted=0 order by title');
    $stmt->bind_param('i',$id);
    }
else
    {
    $festival = GETvalue('festival',getFestivalID());
    $stmt = dbPrepare('select `proposal`.`id`, `proposerid`, `name`, `title`, `orgfields_json`, `submitted` from `proposal` join `user` on `proposerid`=`user`.`id` where `deleted` = 0 and `festival` = ? order by `title`');
    $stmt->bind_param('i',$festival);
    }

$rows = array();
$labels = array();
$stmt->execute();
$stmt->bind_result($id,$proposer_id,$proposer_name,$title,$orgfields_json,$submitted);
while ($stmt->fetch())
    {
    if ($title == '')
        $title = '!!NEEDS A TITLE!!';
    $orgfields = json_decode($orgfields_json,true);
    $rows[] = new propRow($id,$title,$proposer_id,$proposer_name,$orgfields,$submitted);
    addSummaryLabels($labels,$orgfields);
    }
$stmt->close();
if (isset($_SESSION['preferences']['summaryFields']))
    {
    $mylabels = array();
    foreach ($_SESSION['preferences']['summaryFields'] as $s)
        if (in_array($s,$labels))
            $mylabels[] = $s;
    $labels = $mylabels;
    }
sort($labels);

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="batch_' . $batchTitle . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');
$STDOUT = fopen('php://output', 'w');

$fields = array("title", "proposer", "submitted");
foreach ($labels as $l)
    $fields[] = $l;
fputcsv($STDOUT, $fields);

foreach ($rows as $r)
    {
    $fields = array($r->title(), $r->proposer(), $r->submitted());
    $summary = $r->summary($labels);
    foreach ($summary as $s)
        $fields[] = $s;
    fputcsv($STDOUT, $fields);
    }

fclose($STDOUT);

?>
