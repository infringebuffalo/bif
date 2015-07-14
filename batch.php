<?php
$STARTTIME = microtime(TRUE);
require_once 'init.php';
connectDB();
requirePrivilege(array('scheduler','organizer'));
require_once 'util.php';

$id = GETvalue('id',0);

if ($id != 0)
    {
    $stmt = dbPrepare('select name,description from `batch` where id=?');
    $stmt->bind_param('i',$id);
    $stmt->execute();
    $stmt->bind_result($name,$description);
    $stmt->fetch();
    $stmt->close();
    $pageTitle = 'batch: ' . $name;
    $pageDescription = "<p>$description</p>\n";
    $pageDescription .= "<p>";
    $pageDescription .= "<a href='batchEmail.php?id=$id'>[email addresses]</a>\n";
    $pageDescription .= "<a href='batchCsv.php?id=$id'>[csv spreadsheet]</a>\n";
    if (hasPrivilege('scheduler'))
        {
        $pageDescription .= "&nbsp;&nbsp;<a href='editBatch.php?id=$id'>[edit batch]</a>\n";
        $pageDescription .= "&nbsp;&nbsp;<a href='newBatchColumn.php?id=$id'>[new column]</a>\n";
        $pageDescription .= "&nbsp;&nbsp;<a href='autobatch.php?id=$id'>[auto-add to this batch]</a>\n";
        $pageDescription .= "&nbsp;&nbsp;<a href='batchChangeContact.php?id=$id'>[change festival contact for all]</a>\n";
        $pageDescription .= "&nbsp;&nbsp;<a href='batchAddField.php?id=$id'>[add info field to all]</a>\n";
        }
    $pageDescription .= "</p>\n";
    }
else
    {
    $pageTitle = 'all proposals';
    $pageDescription = '';
    }

$header = <<<ENDSTRING
<script src="jquery.min.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="tablesorter.css" />
<script src="jquery.tablesorter.min.js" type="text/javascript"></script>
<script type="text/javascript">
function showEditor(name)
    {
    $('.edit_info').hide();
    $('.show_info').show();
    $('#show_' + name).hide();
    $('#edit_' + name).show();
    }
function hideEditor(name)
    {
    $('#show_' + name).show();
    $('#edit_' + name).hide();
    }

$(document).ready(function() {
    $('.edit_info').hide();
    $('#batchtable').tablesorter();
 });
</script>
ENDSTRING;

bifPageheader($pageTitle, $header);
echo $pageDescription;


class propRow
    {
    function __construct($id,$title,$proposer_id,$proposer_name,$orgfields,$submitted,$access_ser)
        {
        $this->id = $id;
        $this->title = $title;
        $this->proposer_id = $proposer_id;
        $this->proposer_name = $proposer_name;
        $this->orgfields = $orgfields;
        $this->submitted = $submitted;
        $this->lastedit = $submitted;
        $this->lasteditByProposer = $submitted;
        $access = unserialize($access_ser);
        if ($access)
            {
            if (isset($access['lastedit'][$proposer_id]))
                {
                $this->lasteditByProposer = $access['lastedit'][$proposer_id];
                }
            $max = 0;
            foreach ($access['lastedit'] as $ds)
                {
                $d = strtotime($ds);
                if ($d > $max)
                    {
                    $this->lastedit = $ds;
                    $max = $d;
                    }
                }
            }
        }
    function title()
        {
        return '<a href="proposal.php?id=' . $this->id . '">' . $this->title . '</a>';
        }
    function proposer()
        {
        return '<a href="user.php?id=' . $this->proposer_id . '">' . $this->proposer_name . '</a>';
        }
    function submitted()
        {
        return $this->submitted;
        }
    function lastedit()
        {
        return $this->lastedit;
        }
    function lasteditByProposer()
        {
        return $this->lasteditByProposer;
        }
    function summary($labels)
        {
        $s = '';
        $i = 0;
        foreach ($labels as $l)
            {
            $idnum = $this->id . '_' . $i;
            if (is_array($this->orgfields) && array_key_exists($l,$this->orgfields))
                $value = $this->orgfields[$l];
            else
                $value = '';
            if ($value == '')
                $value = '_';
            $s .= '<td><span id="edit_' . $idnum . '" class="edit_info"><form method="POST" action="api.php"><input type="hidden" name="command" value="changeProposalOrgfield" /><input type="hidden" name="proposal" value="' . $this->id . '" /><input type="hidden" name="fieldlabel" value="' . $l . '" /><input type="text" name="newinfo" size="5" value="' . $value . '" /></form></span><span id="show_' . $idnum . '" class="show_info" onclick="showEditor(\'' . $idnum . '\');">' . $value . '</span></td>';
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
    $stmt = dbPrepare('select proposal.id, proposerid, name, title, orgfields, submitted, access from proposal join user on proposerid=user.id join proposalBatch on proposal.id=proposalBatch.proposal_id where proposalBatch.batch_id=? and deleted=0 order by title');
    $stmt->bind_param('i',$id);
    }
else
    {
    $festival = GETvalue('festival',getFestivalID());
    $stmt = dbPrepare('select `proposal`.`id`, `proposerid`, `name`, `title`, `orgfields`, `submitted`, `access` from `proposal` join `user` on `proposerid`=`user`.`id` where `deleted` = 0 and `festival` = ? order by `title`');
    $stmt->bind_param('i',$festival);
    }

$rows = array();
$labels = array();
$stmt->execute();
$stmt->bind_result($id,$proposer_id,$proposer_name,$title,$orgfields_ser,$submitted,$access_ser);
while ($stmt->fetch())
    {
    if ($title == '')
        $title = '!!NEEDS A TITLE!!';
    $orgfields = unserialize($orgfields_ser);
    $rows[] = new propRow($id,$title,$proposer_id,$proposer_name,$orgfields,$submitted,$access_ser);
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

$stmt = dbPrepare('select count(id) from listing where proposal=? and cancelled=0');
$prop_id = 0;
$stmt->bind_param('i',$prop_id);
$stmt->bind_result($showcount);
foreach ($rows as $r)
    {
    $prop_id = $r->id;
    $stmt->execute();
    $stmt->fetch();
    $r->totalShows = $showcount;
    }
$stmt->close();
$stmt = dbPrepare('select count(listing.id) from listing join groupPerformer on listing.proposal = groupPerformer.groupevent where groupPerformer.performer = ? and listing.cancelled=0 and groupPerformer.cancelled=0');
$stmt->bind_param('i',$prop_id);
$stmt->bind_result($showcount);
foreach ($rows as $r)
    {
    $prop_id = $r->id;
    $stmt->execute();
    $stmt->fetch();
    $r->totalShows += $showcount;
    }
$stmt->close();

$out = "<table id='batchtable' class='tablesorter'>\n<thead><tr><th>title</th><th>proposer</th><th>submitted</th><th>edited by proposer</th><th>edited</th><th># of shows</th>";
foreach ($labels as $l)
    $out .= "<th>$l</th>";
$out .= "</tr></thead>\n<tbody>\n";

$count = 0;

foreach ($rows as $r)
    {
    $out .= '<tr><td>' . $r->title() . '</td><td>' . $r->proposer() . '</td><td>' . $r->submitted() . '</td><td>' . $r->lasteditByProposer() . '</td><td>' . $r->lastedit . '</td><td>' . $r->totalShows . '</td>' . $r->summary($labels) . "</tr>\n";
    $count += 1;
    }
$out .= "</tbody>\n</table>\n";
echo $out;

$ENDTIME = microtime(TRUE);
$t = $ENDTIME - $STARTTIME;
echo "<p style='font-size:75%'>$count shows; page took $t seconds</p>";
bifPagefooter();
?>
