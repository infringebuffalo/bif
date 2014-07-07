<?php
require_once 'init.php';
connectDB();
requireLogin();
require_once 'util.php';
require_once 'scheduler.php';

main();

function main()
    {
    $proposal_id = GETvalue('id',0);
    if ($proposal_id == 0)
        die('no proposal selected');
    $proposal = new ProposalData($proposal_id);
    if ((!hasPrivilege(array('scheduler','organizer'))) &&
        ($proposal->proposer_id != $_SESSION['userid']))
            {
            header('Location: .');
            die();
            }
    $canSeeSchedule = hasAccess($_SESSION['userid'],'viewschedule',$proposal->access) || hasPrivilege(array('scheduler','organizer'));
    $canEditSchedule = hasPrivilege('scheduler');

    bifPageheader('proposal: ' . $proposal->title, proposalPageHeader($proposal->availability));
    if ($canSeeSchedule)
        echo proposalScheduleDiv($proposal,$canEditSchedule);
    if ($proposal->isgroupshow)
        echo proposalGroupShowPerformers($proposal,$canEditSchedule);
    if (hasPrivilege('scheduler'))
        echo proposalSchedulingDiv($proposal);
    echo proposalArtvoiceText($proposal);
/*
    echo "<div><a href=\"imageUpload.php?id=$proposal_id\">upload image for web</a></div>\n";
*/
    if (hasPrivilege('scheduler'))
        echo proposalSideControlDiv($proposal);
    echo proposalMainInfo($proposal);
    bifPagefooter();
    }



function proposalArtvoiceText($proposal)
    {
    $html = "<div class='artvoice'>\n<p>Artvoice text (to correct this, edit the &quot;Title&quot;, &quot;Website&quot;, or &quot;Description for brochure&quot; below):</p>";
    $html .= "<p>\n<b>" .  htmlspecialchars($proposal->title,ENT_COMPAT | ENT_HTML5, "UTF-8") . "</b><br>\n";
    $s = $proposal->fieldByLabel('Website');
    if ($s != '')
        $html .= "<em>" . htmlspecialchars($s,ENT_COMPAT | ENT_HTML5, "UTF-8") . "</em><br>\n";
    $s = $proposal->fieldByLabel('Description for brochure');
    if ($s != '')
        {
	$s1 = htmlspecialchars(substr($s,0,140),ENT_COMPAT | ENT_HTML5, "UTF-8");
	$s2 = htmlspecialchars(substr($s,140),ENT_COMPAT | ENT_HTML5, "UTF-8");
        $html .= $s1 . "<span style='color:red'>" . $s2 . "</span><br>\n";
        if (strlen($s) > 140)
            $html .= "<br>[text in red is over the 140 character limit, and will be DELETED automatically when creating the brochure]<br>\n";
        }
    $html .= "</div>\n";
    return $html;
    }


function proposalMainInfo($proposal)
    {
    $html = '';
    $html .= '<span>(<b>NOTE: when editing, you must save any changed field before going to edit another field</b>)</span>';
    $html .= '<table cellpadding="3">';
    
    $html .= "<tr>\n<th class='editField' id='fieldTitle'>Title</th>\n<td>\n";
    $html .= "<div id='show_fieldTitle' class='show_info'>" . htmlspecialchars($proposal->title,ENT_COMPAT | ENT_HTML5, "UTF-8") . "</div>\n";
    $html .= "<div id='edit_fieldTitle' class='edit_info'>" . beginApiCallHtml('changeProposalTitle',array('proposal'=>$proposal->id)) . "<input id='input_fieldTitle' type='text' name='newtitle' value=\"". htmlspecialchars($proposal->title,ENT_COMPAT | ENT_HTML5, "UTF-8") . "\" />" . endApiCallHtml('save') . "</div>\n";
    $html .= "</td>\n</tr>\n";
    
    $html .= "<tr><th>Festival</th><td>" . $proposal->festivalname . "</td></tr>\n";
    
    $html .= "<tr><th>Proposer</th><td><a href='user.php?id=" . $proposal->proposer_id . "'>" . $proposal->proposer_name . "</a>";
    if (hasPrivilege('scheduler'))
        $html .= "&nbsp;&nbsp;&nbsp;(<a href=\"changeOwner.php?id=" . $proposal->id . "\">change proposer</a>)";
    $html .= "</td></tr>\n";
    $html .= "<tr><th>Festival contact</th><td><a href='card.php?id=" . $proposal->orgcontactinfo['id'] . "'>" . $proposal->orgcontactinfo['name'] . "</a> (" . $proposal->orgcontactinfo['email'] . ")</td></tr>\n";
    foreach ($proposal->info as $fieldnum=>$v)
        {
        $html .= "<tr>\n<th class='editField' id='field$fieldnum'>$v[0]</th>\n<td>\n";
        $html .= "<div id='show_field$fieldnum' class='show_info'>" . multiline($v[1]) . "</div>\n";
        $html .= "<div id='edit_field$fieldnum' class='edit_info'>" . beginApiCallHtml('changeProposalInfo', array('proposal'=>$proposal->id, 'fieldnum'=>$fieldnum));
        $html .= "<textarea id='input_field$fieldnum' name='newinfo' cols='80'";
        if ($v[0] == 'Description for brochure')
            $html .=  " class='brochure_description'";
        $html .= ">$v[1]</textarea>\n<input type='submit' name='submit' value='save'>";
        if ($v[0] == 'Description for brochure')
            {
            $html .= "<div class='brochure_description_warning'>(max 140 characters)";
            if (hasPrivilege('scheduler'))
                $html .= " <a class='brochure_len_enforcer' onclick='$(\".brochure_description\").keyup(function(){ limitChars($(this), 140) });'>(enforce for me)</a>";
            $html .= "</div>\n";
            }
        $html .= "</form></div>\n";
        $html .= "</td>\n</tr>\n";
        }
    if (hasPrivilege('scheduler'))
        {
        $html .= "<tr id='edit_fieldNew' class='edit_info'><th>[add field]</th><td><form method='POST' action='api.php'><input type='hidden' name='command' value='addProposalInfoField' /><input type='hidden' name='proposal' value='" . $proposal->id . "' /><input type='text' name='fieldname'><input type='submit' name='submit' value='add'><button onclick='hideEditor(\"fieldNew\"); return false;'>don't add</button></form></td></tr>\n";
        $html .= "<tr id='show_fieldNew' class='show_info' onclick='showEditor(\"fieldNew\");'><th style='background:#ff8'>[add field]</th><td>&nbsp;</td></tr>\n";
        }
    $html .= "<tr><th>Availability</th><td>" . availTable($proposal->id,$proposal->availability) . "</td></tr>\n";
    $html .= '<tr><th>Submitted</th><td>' . $proposal->submitted . '</td></tr>';
    
    $html .= '</table>';
    
    return $html;
    }


class ProposalData
    {
    function __construct($proposal_id)
        {
        $this->id = $proposal_id;
        $stmt = dbPrepare('select `proposerid`, `info`, `availability`, `orgcontact`, `submitted`, `user`.`name`, `festival`.`name`, `proposal`.`access` from `proposal` join `user` on `proposerid`=`user`.`id` join `festival` on `proposal`.`festival`=`festival`.`id` where `proposal`.`id`=?');
        $stmt->bind_param('i',$proposal_id);
        $stmt->execute();
        $stmt->bind_result($proposer_id,$info_ser,$availability_ser,$orgcontact,$submitted,$proposer_name, $festivalname, $access_ser);
        $stmt->fetch();
        $stmt->close();
        
        $this->proposer_id = $proposer_id;
        $this->submitted = $submitted;
        $this->proposer_name = $proposer_name;
        $this->festivalname = $festivalname;
        $this->info = unserialize($info_ser);
        $this->availability = unserialize($availability_ser);
        $this->access = unserialize($access_ser);
        $this->orgcontactinfo = dbQueryByID('select `name`,`card`.`id`,`card`.`email` from `user` join `card` on `user`.`id`=`card`.`userid` where `user`.`id`=?',$orgcontact);
        getDatabase();
        global $proposalList;
        $this->title = $proposalList[$proposal_id]->title;
        $this->deleted = $proposalList[$proposal_id]->deleted;
        $this->listings = $proposalList[$proposal_id]->listings;
        $this->isgroupshow = $proposalList[$proposal_id]->isgroupshow;
        if ($this->isgroupshow)
            $this->performers = $proposalList[$proposal_id]->performers;
        }
    function fieldByLabel($label)
        {
        foreach ($this->info as $fieldnum=>$v)
            {
            if ($v[0] == $label)
                return $v[1];
            }
        return '';
        }
    }

function newBatchMenu($name,$batchlist)
    {
    $retstr = "<select name='$name'>\n";
    $festival = getFestivalID();
    $stmt = dbPrepare('select id,name from batch where festival=? order by name');
    $stmt->bind_param('i',$festival);
    $stmt->execute();
    $stmt->bind_result($id,$name);
    while ($stmt->fetch())
        {
        if (!array_key_exists($id,$batchlist))
            {
            $retstr .= "<option value='$id'>" . stripslashes($name) . "</option>\n";
            }
        }
    $stmt->close();
    $retstr .= "</select>\n";
    return $retstr;
    }


function hasAccess($user, $mode, $access)
    {
    if (!$access)
        return false;
    if (!isset($access[$user]))
        return false;
    return in_array($mode,$access[$user]);
    }

function proposalSideControlDiv($proposal)
    {
    $batchdiv = "<!--BEGIN SIDECONTROLDIV--><div style='float:right; width:20%'>\n";
    $batchlist = array();
    $stmt = dbPrepare('select batch_id,name from proposalBatch join batch on batch_id=batch.id where proposal_id=?');
    $stmt->bind_param('i',$proposal->id);
    $stmt->execute();
    $stmt->bind_result($batch_id,$batch_name);
    while ($stmt->fetch())
        {
        $batchlist[$batch_id] = $batch_name;
        $batchdiv .= "<span style='white-space:nowrap'><a href='batchMove.php?id=$batch_id&cur=" . $proposal->id . "&dir=-1'>&lt;-</a><a href='batch.php?id=$batch_id'>$batch_name</a><a href='batchMove.php?id=$batch_id&cur=" . $proposal->id . "&dir=1'>-&gt;</a>&nbsp;&nbsp;&nbsp;<form method='POST' action='api.php' style='display:inline'><input type='hidden' name='command' value='removeFromBatch' /><input type='hidden' name='proposal' value='" . $proposal->id . "' /><input type='hidden' name='batch' value='$batch_id'><input type='submit' name='submit' value='x' style='border:0px; padding:0; background: yellow'/></form></span><br>\n";
        }
    $stmt->close();
    $batchdiv .= "<form method='POST' action='api.php' style='white-space:nowrap'><input type='hidden' name='command' value='addToBatch' /><input type='hidden' name='proposal' value='" . $proposal->id . "' /><input type='submit' name='submit' value='add to'/>" . newBatchMenu('batch',$batchlist) . "</form>\n";
    if ($proposal->deleted)
        $batchdiv .= "<form method='POST' action='api.php'><input type='hidden' name='command' value='undeleteProposal' /><input type='hidden' name='id' value='" . $proposal->id . "' /><input type='submit' value='undelete project' /></form>\n";
    else
        $batchdiv .= "<form method='POST' action='api.php'><input type='hidden' name='command' value='deleteProposal' /><input type='hidden' name='id' value='" . $proposal->id . "' /><input type='submit' value='delete project' /></form>\n";
    if (!hasAccess($proposal->proposer_id,'viewschedule',$proposal->access))
        $batchdiv .= beginApiCallHtml('grantProposalAccess',array('proposal'=>$proposal->id,'user'=>$proposal->proposer_id,'mode'=>'viewschedule')) . endApiCallHtml('allow proposer to view schedule');
    $batchdiv .= "<br><a href=\"proposalForm.php?id=" . $proposal->id . "\">[original form]</a>\n";
    $batchdiv .= "<br><br>\n";
    $notes = getNotes($proposal->id);
    foreach ($notes as $n)
        {
        $batchdiv .= noteDiv($n,$proposal->id);
        }
    $batchdiv .= "<form method='POST' action='api.php'><input type='hidden' name='command' value='addNote' /><input type='hidden' name='entity' value='" . $proposal->id . "' /><textarea name='note' rows='2' cols='30'></textarea><br><input type='submit' name='submit' value='add note'/></form>\n";
    $batchdiv .= "</div><!--END SIDECONTROLDIV-->\n";
    return $batchdiv;
    }


function jsSafe($s)
    {
    $s = addslashes($s);
    $s = str_replace("\n","",$s);
    $s = str_replace("\r","",$s);
    return $s;
    }

function proposalPageHeader($availability)
    {
    $header = <<<ENDSTRING
<script src="jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
var availability = {
ENDSTRING;

    global $festivalNumberOfDays;
    for ($i=0; $i < $festivalNumberOfDays; $i++)
        if (is_array($availability) && array_key_exists($i,$availability))
            $header .= " $i : '" . dayToDateday($i) . ': ' . jsSafe($availability[$i]) . "',";

    $header .= <<<ENDSTRING
};

function showEditor(name)
    {
    $('#show_' + name).hide();
    $('#edit_' + name).show();
    $('#donteditButton_' + name).show();
    $('#editButton_' + name).hide();
    }
function hideEditor(name)
    {
    shownode = $('#show_'+name);
    editnode = $('#edit_'+name);
    inputnode = $('#input_'+name);
    shownode.show();
    origtext = shownode.html();
    inputnode.val(origtext);
    editnode.hide();
    $('#donteditButton_' + name).hide();
    $('#editButton_' + name).show();
    }

function showMyEditor()
    {
    name = $(this).parent().attr('id');
    $('#show_' + name).hide();
    $('#edit_' + name).show();
    $('#donteditButton_' + name).show();
    $('#editButton_' + name).hide();
    }
function hideMyEditor()
    {
    name = $(this).parent().attr('id');
    shownode = $('#show_'+name);
    editnode = $('#edit_'+name);
    inputnode = $('#input_'+name);
    shownode.show();
    origtext = shownode.html();
    inputnode.val(origtext);
    editnode.hide();
    $('#donteditButton_' + name).hide();
    $('#editButton_' + name).show();
    }

function showScheduler(name)
    {
    $('.scheduleForm').hide()
    $(name).show()
    }
function toggleEdit(rowname)
    {
    $('#' + rowname).toggle()
    $('#' + rowname + 'edit').toggle()
    }
function hoverFunc()
    {
    $('.availabilityInfo').html(availability[$(this).data("bifday")]);
    }
function unhoverFunc()
    {
    $('.availabilityInfo').html('&nbsp;');
    }
function limitChars(node, limit)
    {
    var text = node.val();
    $('.brochure_description_warning').html(text.length + ' characters, of ' + limit + ' max');
    if (text.length > limit)
        {
        node.val(text.substr(0,limit));
        return false;
        }
     else
        {
        return true;
        }
     }
 
function addEditButtons(index,element)
    {
    element = $(element);
    name = element.attr('id');
    editButton = $('<span class="fieldEditLink" id="editButton_' + name + '">[edit]</span>');
    editButton.click(showMyEditor);
    donteditButton = $('<span class="fieldDontEditLink" id="donteditButton_' + name + '">[don\'t edit]</span>');
    donteditButton.click(hideMyEditor);
    element.append(editButton);
    element.append(donteditButton);
    }

$(document).ready(function() {
    $('.editField').each(addEditButtons);
    $('.edit_info').hide();
    $('.calEntry').hover(hoverFunc,unhoverFunc);
ENDSTRING;

    if (!hasPrivilege('scheduler'))
        {
        $header .= "$('.brochure_description').keyup(function(){ limitChars($(this), 140) });\n";
        }

    $header .= <<<ENDSTRING
 });
</script>
ENDSTRING;
    return $header;
    }


function timeDateSortingKey($listing)
    {
    return '<!--' . $listing->date . ' ' . $listing->starttime . '-->';
    }

function proposalScheduleDiv($proposal,$canEditSchedule)
    {
    $list = array();
    foreach ($proposal->listings as $listing)
        {
        if (($listing->proposalid == $proposal->id) && ($canEditSchedule))
            $list[] = timeDateSortingKey($listing) . editableListingRow($listing->id,1,1,1,0,0);
        else
            $list[] = timeDateSortingKey($listing) . listingRow($listing->id,1,1,1,1,1);
        }
    sort($list);
    $schedulediv = "<!--BEGIN SCHEDULE--><div class='schedule'>\n";
    $schedulediv .= "<table>\n";
    $schedulediv .= implode($list);
    $schedulediv .= "</table>\n";
    $schedulediv .= "</div><!--END SCHEDULE-->\n";
    return $schedulediv;
    }


function proposalGroupShowPerformers($proposal,$canEditSchedule)
    {
    $out = "<!--BEGIN PERFORMERS--><div>\n";
    $out .= "<p>Performers:</p><table rules='all'>\n";
    $count = 0;
    foreach ($proposal->performers as $perf)
        {
        $count++;
        if ($perf->cancelled)
            $tdtags = ' class="cancelled"';
        else
            $tdtags = '';
        $out .= '<tr id="groupschedulerow' . $count . '">';
        $out .= '<td' . $tdtags . '><a href="proposal.php?id=' . $perf->performerid . '">' . $perf->performer->title . '</a></td>';
        $out .= '<td' . $tdtags . '>' . $perf->showorder . '</td>';
        $out .= '<td' . $tdtags . '>' . timeToString($perf->time) . '</td>';
        $out .= '<td' . $tdtags . '>' . $perf->note . '</td>';
        if ($canEditSchedule)
            {
            $out .= '<td' . $tdtags . '> &nbsp;<a href="" onclick="toggleEdit(\'groupschedulerow' . $count . '\'); return false;">edit</a>&nbsp; </td>';
            }
        $out .= '</tr>';
 
        if ($canEditSchedule)
            {
            $out .= '<tr id="groupschedulerow' . $count . 'edit" style="display:none">';
            $out .= '<td><a href="proposal.php?id=' . $perf->performerid . '">' . $perf->performer->title . '</a></td>';
            $out .= '<td colspan="3">' . beginApiCallHtml('changeGroupPerformer',array('groupperformerid'=>$perf->id));
            $out .= '<input type="text" name="showorder" size="2" value="' . $perf->showorder . '" />';
            $out .= timeMenu(11,28,'time',$perf->time);
            $out .= '<input type="text" name="note" value="' . $perf->note . '" />';
            $out .= '<input type="submit" value="Save" />';
            $out .= '</form></td>';
            $out .= '<td>';
            if ($perf->cancelled)
                $out .= beginApiCallHtml('uncancelGroupPerformer',array('groupperformerid'=>$perf->id)) . endApiCallHtml('uncancel');
            else
                $out .= beginApiCallHtml('cancelGroupPerformer',array('groupperformerid'=>$perf->id)) . endApiCallHtml('cancel');
            $out .= beginApiCallHtml('deleteGroupPerformer',array('groupperformerid'=>$perf->id)) . endApiCallHtml('delete');
            $out .= '<a href="" onclick="toggleEdit(\'groupschedulerow' . $count . '\'); return false;">don\'t edit</a></td>';
            }
        $out .= '</tr>';
        }
    $out .= "</table>\n";
    $out .= "</div><!--END PERFORMERS-->\n";
    return $out;
    }


function calEntry($day)
    {
    return '<input type="checkbox" name="' . dayToDate($day) . '" value="1" />';
    }

function calEntry2($day)
    {
    return '<input type="checkbox" name="' . dayToDate($day) . '" value="1" checked/>';
    }

function proposalSchedulingDiv($proposal)
    {
    $out = '<div class="schedulebox">[scheduling]<br/>';
    $out .= '<a href="" id="scheduleEventAnchor" onclick="showScheduler(\'#scheduleEventForm\'); return false">performance</a>&nbsp;<a href="" id="scheduleInstallationAnchor" onclick="showScheduler(\'#scheduleInstallationForm\'); return false">installation</a>&nbsp;&nbsp;';
    if ($proposal->isgroupshow)
        {
        $out .= '<a href="" id="schedulePerformerAnchor" onclick="showScheduler(\'#schedulePerformerForm\'); return false">performer</a>&nbsp;&nbsp;';
/*
        $out .= '<a href="" id="groupListModeAnchor" onclick="showScheduler(\'#groupListModeForm\'); return false">group-list-mode</a>&nbsp;&nbsp;';
*/
        }
    else
        $out .= '<a href="" id="scheduleGroupAnchor" onclick="showScheduler(\'#scheduleGroupForm\'); return false">group show</a>&nbsp;&nbsp;';
 
/*
    $out .= '<a href="" id="scheduleMassdeleteAnchor" onclick="showScheduler(\'#scheduleMassdeleteForm\'); return false">mass-delete</a>&nbsp;&nbsp;';
*/
    $out .= scheduleEventForm('proposal.php?id=' . $proposal->id, 'calEntry', $proposal->id, 0);
 
    $out .= scheduleInstallationForm('proposal.php?id=' . $proposal->id, 'calEntry2', $proposal->id, 0);
 
    if ($proposal->isgroupshow)
        {
        $out .= "<div class='scheduleForm' id='schedulePerformerForm' style='display: none'>\n";
        $out .= "<form method='POST' action='api.php'>\n";
        $out .= "<input type='hidden' name='command' value='scheduleGroupPerformer' />\n";
        $out .= "<input type='hidden' name='groupeventid' value='" . $proposal->id . "' />\n";
        $out .= "<input type='hidden' name='returnurl' value='proposal.php?id=" . $proposal->id . "' />\n";
        $out .= "<table>\n";
        $out .= "<tr>\n";
        $out .= "<td> " . showMenu('performerid',getProposalInfo($proposal->id,'batch')) . " </td>\n";
        $out .= "<td> Time " . timeMenu(11,28,'time') . " </td>\n";
        $out .= "<td> Order <input type='text' name='order' value='0' size='3'/> </td>\n";
        $out .= "<td> Note <input type='text' name='note' value='' size='12'/> </td>\n";
        $out .= "<td><input type='submit' name='submit' value='Add' /></td>\n";
        $out .= "</tr>\n";
        $out .= "</table>\n";
        $out .= "</form>\n";
        $out .= "</div>\n";
/*
        $out .= '<div class="scheduleForm" id="groupListModeForm" style="display:none">';
        $out .= 'Display performers list with:<form method="POST" action="api.php">';
        $out .= '<input type="hidden" name="command" value="groupListMode" />';
        $out .= '<input type="hidden" name="groupeventid" value="' . $proposal->id . '" />';
        $out .= '<input type="radio" name="grouplistmode" value="0" ';
        if ($info['grouplistmode'] == 0) $out .= 'checked ';
        $out .= '/>numbers<br/>';
        $out .= '<input type="radio" name="grouplistmode" value="1" ';
        if ($info['grouplistmode'] == 1) $out .= 'checked ';
        $out .= '/>times<br/>';
        $out .= '<input type="radio" name="grouplistmode" value="2" ';
        if ($info['grouplistmode'] == 2) $out .= 'checked ';
        $out .= '/>both<br/>';
        $out .= '<input type="submit" name="submit" value="Save" />';
        $out .= '</form>';
        $out .= '</div>';
*/
        }
    else
        {
        $out .= "<div class='scheduleForm' id='scheduleGroupForm' style='display: none'>\n";
        $out .= "<form method='POST' action='api.php'>\n";
        $out .= "<input type='hidden' name='command' value='scheduleGroupPerformer' />\n";
        $out .= "<input type='hidden' name='performerid' value='" . $proposal->id . "'>\n";
        $out .= "<input type='hidden' name='returnurl' value='proposal.php?id=" . $proposal->id . "'>\n";
        $out .= "<table>\n";
        $out .= "<tr>\n";
        $out .= "<td> " . groupShowMenu('groupeventid') . " </td>\n";
        $out .= "<td> Time " . timeMenu(11,28,'time') . " </td>\n";
        $out .= "<td> Order <input type='text' name='order' value='0' size='3'/> </td>\n";
        $out .= "<td> Note <input type='text' name='note' value='' size='12'/> </td>\n";
        $out .= "<td><input type='submit' name='submit' value='Add'></td>\n";
        $out .= "</tr>\n";
        $out .= "</table>\n";
        $out .= "</form>\n";
        $out .= "</div>\n";
        }
/*
    $out .= '<div class="scheduleForm" id="scheduleMassdeleteForm" style="display: none">';
    $out .= '<form method="POST" action="api.php">';
    $out .= '<input type="hidden" name="command" value="massDelete" />';
    $out .= '<input type="hidden" name="proposal" value="' . $proposal->id . '" />';
    $out .= '<input type="hidden" name="date" value="*" />';
    $out .= 'Delete all shows at ' . venueMenu('venue');
    $out .= '<input type="submit" name="submit" value="Delete" />';
    $out .= '</form>';
    $out .= '</div>';
*/
    $out .= '</div><br>';
    return $out;
    }


function availTable($proposal_id,$av)
    {
    global $festivalNumberOfDays;
    $s = "<table>\n";
    if (is_array($av))
        {
        for ($i=0; $i < $festivalNumberOfDays; $i++)
            {
            if (array_key_exists($i,$av))
                {
                $s .= "<tr>\n<th class='editField' id='fieldAvail$i'>" . dayToDateday($i) . "</th>\n";
                $s .= "<td>\n<div id='show_fieldAvail$i' class='show_info'>$av[$i]</div>\n";
                $s .= "<div id='edit_fieldAvail$i' class='edit_info'>" . beginApiCallHtml('changeProposalAvail', array('proposal'=>$proposal_id,'daynum'=>$i)) . "<textarea id='input_fieldAvail$i' name='newinfo' cols='40'>" . $av[$i] . "</textarea>" . endApiCallHtml('save') . "</div>\n</td>\n</tr>\n";
                }
            }
        }
    $s .= "</table>\n";
    return $s;
    }

?>
