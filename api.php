<?php
require_once 'init.php';
connectDB();
require_once 'util.php';
require_once 'scheduler.php';

class apiFunction
    {
    function __construct($name,$schedulerPriv,$adminPriv)
        {
        $this->name = $name;
        $this->reflector = new ReflectionFunction($name);
        $this->params = $this->reflector->getParameters();
        $this->schedulerPriv = $schedulerPriv;  
        $this->adminPriv = $adminPriv;
        }
    function call()
        {
        if ($this->adminPriv)
            requirePrivilege('admin');
        if ($this->schedulerPriv)
            requirePrivilege('scheduler');
        $args = array();
        foreach ($this->params as $param)
            $args[] = POSTvalue($param->name);
        call_user_func_array($this->name,$args);
        }
    }

$api = array(new apiFunction('newVenue',1,0),
            new apiFunction('newCard',1,0),
            new apiFunction('newBatch',1,0),
            new apiFunction('newGroupshow',1,0),
            new apiFunction('scheduleEvent',1,0),
            new apiFunction('scheduleGroupPerformer',1,0),
            new apiFunction('updateContact',0,0),
            new apiFunction('updatePassword',0,0),
            new apiFunction('changeBatchDescription',1,0),
            new apiFunction('changeBatchMembers',1,0),
            new apiFunction('addToBatch',1,0),
            new apiFunction('removeFromBatch',1,0),
            new apiFunction('changeProposalTitle',0,0),
            new apiFunction('changeProposalInfo',0,0),
            new apiFunction('changeProposalOrgfield',0,0),
            new apiFunction('changeProposalAvail',0,0),
            new apiFunction('deleteProposal',1,0),
            new apiFunction('undeleteProposal',1,0),
            new apiFunction('deleteVenue',1,0),
            new apiFunction('undeleteVenue',1,0),
            new apiFunction('changeVenueInfo',1,0),
            new apiFunction('changeVenueName',1,0),
            new apiFunction('changeVenueShortname',1,0),
            new apiFunction('changeListing',1,0),
            new apiFunction('cancelListing',1,0),
            new apiFunction('uncancelListing',1,0),
            new apiFunction('deleteListing',1,0),
            new apiFunction('subscribe',1,0),
            new apiFunction('addPrivilege',0,1),
            new apiFunction('removePrivilege',0,1),
            );

$command = POSTvalue('command');
if ($command == '')
    {
    header('Location: .');
    die();
    }

$returnurl = POSTvalue('returnurl');

$called = false;
foreach ($api as $a)
    {
    if ($a->name == $command)
        {
        $a->call();
        $called = true;
        break;
        }
    }
if (!$called)
    log_message('unknown api command "' . $command . '"');

if ($returnurl == '')
    header('location:' . $_SERVER['HTTP_REFERER']);
else
    header('location:' . $returnurl);


function newVenue($name,$shortname)
    {
    $defaultInfo = array(array('owner',''), array('address',''), array('phone',''), array('website',''), array('contact',''), array('contact phone',''), array('contact e-mail',''), array('venue type',''), array('allowed performances',''), array('best performances',''), array('performance space',''), array('wall space',''));
    $venueid = newEntityID('venue');
    $stmt = dbPrepare('insert into `venue` (`id`, `name`, `shortname`, `festival`, `info`) values (?,?,?,?,?)');
    $festival = getFestivalID();
    $info = serialize($defaultInfo);
    $stmt->bind_param('issis',$venueid,$name,$shortname,$festival,$info);
    $stmt->execute();
    $stmt->close();
    log_message('newVenue ' . $venueid . ' : ' . $name);
    global $returnurl;
    $returnurl = 'venue.php?id=' . $venueid;
    }

function newCard($userid,$role,$email,$phone,$snailmail)
    {
    $cardid = newEntityID('card');
    $stmt = dbPrepare('insert into `card` (`id`, `userid`, `role`, `email`, `phone`, `snailmail`) values (?,?,?,?,?,?)');
    $stmt->bind_param('iissss',$cardid,$userid,$role,$email,$phone,$snailmail);
    $stmt->execute();
    $stmt->close();
    log_message('newCard ' . $cardid . ' : ' . $role . ' / ' . $email);
    }

function newBatch($name,$description)
    {
    $batchid = newEntityID('batch');
    $stmt = dbPrepare('insert into `batch` (`id`, `name`, `festival`, `description`) values (?,?,?,?)');
    $festival = getFestivalID();
    $stmt->bind_param('isis',$batchid,$name,$festival,$description);
    $stmt->execute();
    $stmt->close();
    log_message('newBatch ' . $batchid . ' : ' . $name);
    }

function newGroupshow($title,$description,$batch)
    {
    $showid = newEntityID('proposal');
    $stmt = dbPrepare('insert into `proposal` (`id`, `proposerid`, `festival`, `title`, `info`, `orgcontact`, `isgroupshow`) values (?,?,?,?,?,?,1)');
    $festival = getFestivalID();
    $proposerid = $_SESSION['userid'];
    $info = array(array('Description',$description),array('batch',$batch));
    $info_ser = serialize($info);
    $orgcontact = $proposerid;
    $stmt->bind_param('iiissi',$showid,$proposerid,$festival,$title,$info_ser,$orgcontact);
    $stmt->execute();
    $stmt->close();
    log_message('newGroupshow ' . $showid . ' : ' . $title);
    $groupbatchid = getBatch('group',getFestivalID(),true,'All group shows');
    addToBatch($showid,$groupbatchid);
    }


function scheduleEvent()
    {
    global $festivalNumberOfDays;
    $proposal = POSTvalue('proposal',0);
    $venue = POSTvalue('venue',0);
    $venuenote = POSTvalue('venuenote');
    $starttime = POSTvalue('starttime',0);
    $endtime = POSTvalue('endtime',0);
    $installation = POSTvalue('installation',0);
    $note = POSTvalue('note');
    log_message('scheduleEvent ' . $proposal);
    for ($d=0; $d < $festivalNumberOfDays; $d++)
        {
        $date = dayToDate($d);
        if ($_POST[$date] == '1')
            {
            $listingid = newEntityID('listing');
            log_message(' scheduling day ' . $date);
            $stmt = dbPrepare("insert into listing (id,date,proposal,venue,venuenote,starttime,endtime,installation,note) values (?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param('isiisiiis',$listingid,$date,$proposal,$venue,$venuenote,$starttime,$endtime,$installation,$note);
            if (!$stmt->execute())
                log_message($stmt->error);
            $stmt->close();
            }
        }
    }

function scheduleGroupPerformer()
    {
    $id = newEntityID('groupPerformer');
    $groupevent = POSTvalue('groupeventid');
    $performer = POSTvalue('performerid');
    $order = POSTvalue('order');
    $time = POSTvalue('time');
    $note = POSTvalue('note');
    $stmt = dbPrepare('insert into groupPerformer (id, groupevent, performer, showorder, time, note) values (?,?,?,?,?,?)');
    $stmt->bind_param('iiiiis',$id,$groupevent,$performer,$order,$time,$note);
    $stmt->execute();
    $stmt->close();
    }

function updateContact()
    {
    $id=POSTvalue('id');
    if (($id != $_SESSION['userid']) && (!hasPrivilege('scheduler')))
        {
        log_message("tried to change contact info for userid $id");
        header('Location: .');
        die();
        }
    $stmt = dbPrepare("update user set name=?, phone=?, snailmail=? where id=?");
    $stmt->bind_param('sssi', $_POST['name'], $_POST['phone'], $_POST['snailmail'], $id);
    $stmt->execute();
    $stmt->close();
    }

function updatePassword()
    {
    $newpassword1 = POSTvalue('newpassword1');
    $newpassword2 = POSTvalue('newpassword2');
    $encNewpassword = md5($newpassword1);
    $encOldpassword = md5(POSTvalue('oldpassword'));
    $id = $_SESSION['userid'];
    $username = $_SESSION['username'];
    if ($newpassword1 != $newpassword2)
        {
        log_message("change password failed - new password mismatch");
        $_SESSION['changepasswordError'] = 'Failed to change password: new password and confirmation did not match.';
        header('Location: changePassword.php');
        die();
        }
    $row = dbQueryByString('select password from user where email=?', $username);
    if ((!$row) || ($row['password'] != $encOldpassword))
        {
        log_message("change password failed - wrong old password");
        $_SESSION['changepasswordError'] = 'Failed to change password: old password was incorrect.';
        header('Location: changePassword.php');
        die();
        }
    $stmt = dbPrepare('update user set password=?,newpassword=? where id=?');
    $stmt->bind_param('ssi',$endNewpassword,$encNewpassword,$id);
    $stmt->execute();
    $stmt->close();
    log_message("changed password");
    }

function changeBatchDescription($id,$description)
    {
    $stmt = dbPrepare('update batch set description=? where id=?');
    $stmt->bind_param('si',$description,$id);
    $stmt->execute();
    $stmt->close();
    log_message("changed description of batch $id");
    }

function changeBatchMembers()
    {
    $batchid = POSTvalue('id');
    $stmt = dbPrepare('delete from proposalBatch where batch_id=?');
    $stmt->bind_param('i',$batchid);
    $stmt->execute();
    $stmt->close();
    foreach ($_POST['proposal'] as $proposalid)
        {
        $stmt = dbPrepare('insert into proposalBatch (proposal_id,batch_id) values (?,?)');
        $stmt->bind_param('ii',$proposalid,$batchid);
        $stmt->execute();
        $stmt->close();
        }
    log_message("changed membership of batch $batchid");
    }

function addToBatch($proposal,$batch)
    {
    $stmt = dbPrepare('select count(*) from proposalBatch where proposal_id=? and batch_id=?');
    $stmt->bind_param('ii',$proposal,$batch);
    $stmt->execute();
    $count = 0;
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    if ($count > 0)
        return;
    $stmt = dbPrepare('insert into proposalBatch (proposal_id,batch_id) values (?,?)');
    $stmt->bind_param('ii',$proposal,$batch);
    $stmt->execute();
    $stmt->close();
    }

function removeFromBatch($proposal,$batch)
    {
    $stmt = dbPrepare('delete from proposalBatch where proposal_id=? and batch_id=?');
    $stmt->bind_param('ii',$proposal,$batch);
    $stmt->execute();
    $stmt->close();
    }

function changeProposalTitle($proposal,$newtitle)
    {
    $stmt = dbPrepare('update proposal set title=? where id=?');
    $stmt->bind_param('si',$newtitle,$proposal);
    $stmt->execute();
    $stmt->close();
    log_message("changed proposal $proposal title to '$newtitle'");
    }

function changeProposalInfo($proposal,$fieldnum,$newinfo)
    {
    $info_ser = dbQueryByID('select info from proposal where id=?',$proposal);
    if ($info_ser == NULL)
        return;
    $info = unserialize($info_ser['info']);
    $oldinfo = $info[$fieldnum][1];
    $info[$fieldnum] = array($info[$fieldnum][0], filter_var($newinfo, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH));
    $info_ser = serialize($info);
    $stmt = dbPrepare('update proposal set info=? where id=?');
    $stmt->bind_param('si',$info_ser,$proposal);
    $stmt->execute();
    $stmt->close();
    log_message("changed proposal $proposal field $fieldnum from '$oldinfo' to '$newinfo'");
    }

function changeProposalOrgfield($proposal,$fieldlabel,$newinfo)
    {
    $orgfields_ser = dbQueryByID('select orgfields from proposal where id=?',$proposal);
    if ($orgfields_ser == NULL)
        return;
    $orgfields = unserialize($orgfields_ser['orgfields']);
    $oldinfo = $orgfields[$fieldlabel];
    $orgfields[$fieldlabel] = filter_var($newinfo, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
    $orgfields_ser = serialize($orgfields);
    $stmt = dbPrepare('update proposal set orgfields=? where id=?');
    $stmt->bind_param('si',$orgfields_ser,$proposal);
    $stmt->execute();
    $stmt->close();
    log_message("changed proposal $proposal field $fieldlabel from '$oldinfo' to '$newinfo'");
    }

function changeProposalAvail($proposal,$daynum,$newinfo)
    {
    $info_ser = dbQueryByID('select availability from proposal where id=?',$proposal);
    if ($info_ser == NULL)
        return;
    $info = unserialize($info_ser['availability']);
    $oldinfo = $info[$daynum];
    $info[$daynum] = filter_var($newinfo, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
    $info_ser = serialize($info);
    $stmt = dbPrepare('update proposal set availability=? where id=?');
    $stmt->bind_param('si',$info_ser,$proposal);
    $stmt->execute();
    $stmt->close();
    log_message("changed proposal $proposal availability $daynum from '$oldinfo' to '$newinfo'");
    }

function deleteProposal($id)
    {
    $stmt = dbPrepare('update proposal set deleted=1 where id=?');
    $stmt->bind_param('i',$id);
    $stmt->execute();
    $stmt->close();
    log_message("deleted proposal $id");
    }

function undeleteProposal($id)
    {
    $stmt = dbPrepare('update proposal set deleted=0 where id=?');
    $stmt->bind_param('i',$id);
    $stmt->execute();
    $stmt->close();
    log_message("undeleted proposal $id");
    }

function deleteVenue($id)
    {
    $stmt = dbPrepare('update venue set deleted=1 where id=?');
    $stmt->bind_param('i',$id);
    $stmt->execute();
    $stmt->close();
    log_message("deleted venue $id");
    }

function undeleteVenue($id)
    {
    $stmt = dbPrepare('update venue set deleted=0 where id=?');
    $stmt->bind_param('i',$id);
    $stmt->execute();
    $stmt->close();
    log_message("undeleted venue $id");
    }

function changeVenueInfo($venue,$fieldnum,$newinfo)
    {
    $info_ser = dbQueryByID('select info from venue where id=?',$venue);
    if ($info_ser == NULL)
        return;
    $info = unserialize($info_ser['info']);
    $oldinfo = $info[$fieldnum][1];
    $info[$fieldnum] = array($info[$fieldnum][0], filter_var($newinfo, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH));
    $info_ser = serialize($info);
    $stmt = dbPrepare('update venue set info=? where id=?');
    $stmt->bind_param('si',$info_ser,$venue);
    $stmt->execute();
    $stmt->close();
    log_message("changed venue $venue field $fieldnum from '$oldinfo' to '$newinfo'");
    }

function changeVenueName($venue,$newinfo)
    {
    $stmt = dbPrepare('update venue set name=? where id=?');
    $stmt->bind_param('si',$newinfo,$venue);
    $stmt->execute();
    $stmt->close();
    log_message("changed venue $venue name to '$newinfo'");
    }

function changeVenueShortname($venue,$newinfo)
    {
    $stmt = dbPrepare('update venue set shortname=? where id=?');
    $stmt->bind_param('si',$newinfo,$venue);
    $stmt->execute();
    $stmt->close();
    log_message("changed venue $venue shortname to '$newinfo'");
    }

function changeListing($listingid,$venue,$venuenote,$date,$starttime,$endtime,$note)
    {
    $installation = POSTvalue('installation',0);
    $stmt = dbPrepare('update listing set venue=?,venuenote=?,date=?,starttime=?,endtime=?,installation=?,note=? where id=?');
    $stmt->bind_param('issssisi',$venue,$venuenote,$date,$starttime,$endtime,$installation,$note,$listingid);
    if (!$stmt->execute())
        log_message($stmt->error);
    $stmt->close();
    log_message("change listing $listingid to venue $venue ($venuenote) / date $date / start $starttime / end $endtime / install $installation / note $note");
    }

function cancelListing($listingid)
    {
    $stmt = dbPrepare('update listing set cancelled=1 where id=?');
    $stmt->bind_param('i',$listingid);
    $stmt->execute();
    $stmt->close();
    log_message("cancel listing $listingid");
    }

function uncancelListing($listingid)
    {
    $stmt = dbPrepare('update listing set cancelled=0 where id=?');
    $stmt->bind_param('i',$listingid);
    $stmt->execute();
    $stmt->close();
    log_message("uncancel listing $listingid");
    }

function deleteListing($listingid)
    {
    $stmt = dbPrepare('delete from listing where id=?');
    $stmt->bind_param('i',$listingid);
    $stmt->execute();
    $stmt->close();
    $stmt = dbPrepare('delete from entity where id=?');
    $stmt->bind_param('i',$listingid);
    $stmt->execute();
    $stmt->close();
    log_message("delete listing $listingid");
    }

function subscribe($address,$mailinglist)
    {
    $email = $mailinglist . '-subscribe-' . str_replace('@','=',$address) . '@infringebuffalo.org';
    $subject = '';
    $body = '';
    $header = 'From: dave@infringebuffalo.org';
    log_message("subscribing $email to $mailinglist");
    if (loggedMail($email, $subject, $body, $header))
        {
        $_SESSION['adminmessage'] .= '<p>request to add ' . $address . ' to ' . $mailinglist . '@infringebuffalo.org mailing list has been sent</p>';
        }
    else
        {
        $_SESSION['adminmessage'] .= '<p>e-mail failed on request to add ' . $address . ' to ' . $mailinglist . '@infringebuffalo.org</p>';
        }
    }

function addPrivilege($userid,$privilege)
    {
    $privilege = '/' . $privilege . '/';
    $row = dbQueryByID('select privs from user where id=?',$userid);
    if (($row) && (stripos($row['privs'],$privilege) === false))
        {
        $privlist = $row['privs'] . $privilege;
        $stmt = dbPrepare('update user set privs=? where id=?');
        $stmt->bind_param('si',$privlist,$userid);
        $stmt->execute();
        $stmt->close();
        log_message("added privilege $privilege for user $userid");
        }
    }

function removePrivilege($userid,$privilege)
    {
    $privilege = '/' . $privilege . '/';
    $row = dbQueryByID('select privs from user where id=?',$userid);
    if (($row) && (stripos($row['privs'],$privilege) !== false))
        {
        $privlist = $row['privs'];
        $privlist = str_replace($privilege,'',$privlist);
        $stmt = dbPrepare('update user set privs=? where id=?');
        $stmt->bind_param('si',$privlist,$userid);
        $stmt->execute();
        $stmt->close();
        log_message("removed privilege $privilege from user $userid");
        }
    }

?>
