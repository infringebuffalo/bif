<?php

function newVenue($name,$shortname)
    {
    $defaultInfo = array(array('Owner',''),
                         array('Address',''),
                         array('Phone',''),
                         array('Website',''),
                         array('Contact',''),
                         array('Contact phone',''),
                         array('Contact e-mail',''),
                         array('Venue type',''),
                         array('Allowed performances',''),
                         array('Best performances',''),
                         array('Performance space',''),
                         array('Wall space',''),
                         array('Infringement contact',''),
                         array('Capacity',''),
                         array('Stage',''),
                         array('Sound system/cost',''),
                         array('Age Restrictions',''),
                         array('Alcohol served',''),
                         array('Other beverages served',''),
                         array('Food served',''),
                         array('BYOB',''),
                         array('Backline available',''),
                         array('Door person provided?',''),
                         array('Dressing rooms',''),
                         array('Hospitality for performers',''),
                         array('Seating',''),
                         array('Parking',''),
                         array('Performer load-in',''),
                         array('Wheelchair accessible',''),
                         array('Regular business hours',''),
                         array('Description for web',''),
                         array('latitude',''),
                         array('longitude','')
                        );
    $venueid = newEntityID('venue');
    $festival = getFestivalID();
    $stmt = dbPrepare('insert into `venue` (`id`, `name`, `shortname`, `festival`, `info`) values (?,?,?,?,?)');
    $info = serialize($defaultInfo);
    $stmt->bind_param('issis',$venueid,$name,$shortname,$festival,$info);
    $stmt->execute();
    $stmt->close();
    log_message('newVenue ' . $venueid . ' : ' . $name);
    global $returnurl;
    $returnurl = 'venue.php?id=' . $venueid;
    }

function copyVenue($id)
    {
    $row = dbQueryByID('select name,shortname,info from venue where id=?',$id);
    $name = $row['name'];
    $shortname = $row['shortname'];
    $info = $row['info'];
    $newvenueid = newEntityID('venue');
    $festival = getFestivalID();
    $stmt = dbPrepare('insert into `venue` (`id`, `name`, `shortname`, `festival`, `info`) values (?,?,?,?,?)');
    $stmt->bind_param('issis',$newvenueid,$name,$shortname,$festival,$info);
    $stmt->execute();
    $stmt->close();
    log_message('copyVenue ' . $id . ' (' . $name . ') to ' . $newvenueid);
    global $returnurl;
    $returnurl = 'venue.php?id=' . $newvenueid;
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
    $festival = getFestivalID();
    $stmt = dbPrepare('insert into `batch` (`id`, `name`, `festival`, `description`) values (?,?,?,?)');
    $stmt->bind_param('isis',$batchid,$name,$festival,$description);
    $stmt->execute();
    $stmt->close();
    log_message('newBatch ' . $batchid . ' : ' . $name);
    }

function newGroupshow($title,$description,$batch)
    {
    $showid = newEntityID('proposal');
    $festival = getFestivalID();
    $stmt = dbPrepare('insert into `proposal` (`id`, `proposerid`, `festival`, `title`, `info`, `orgcontact`, `isgroupshow`) values (?,?,?,?,?,?,1)');
    $proposerid = $_SESSION['userid'];
    $info = array(array('Type','group'),array('Description for web',''),array('Description for brochure',''), array('batch',$batch));
    $info_ser = serialize($info);
    $orgcontact = $proposerid;
    $stmt->bind_param('iiissi',$showid,$proposerid,$festival,$title,$info_ser,$orgcontact);
    $stmt->execute();
    $stmt->close();
    log_message('newGroupshow ' . $showid . ' : ' . $title);
    $groupbatchid = getBatch('group',getFestivalID(),true,'All group shows');
    addToBatch($showid,$groupbatchid);
    global $returnurl;
    $returnurl = 'proposal.php?id=' . $showid;
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
            log_message("scheduling proposal $proposal at venue $venue on $date");
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

function changeBatchDescription($id,$name,$description)
    {
    $stmt = dbPrepare('update batch set name=?,description=? where id=?');
    $stmt->bind_param('ssi',$name,$description,$id);
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

function removeFromBatch($proposal,$batch)
    {
    $stmt = dbPrepare('delete from proposalBatch where proposal_id=? and batch_id=?');
    $stmt->bind_param('ii',$proposal,$batch);
    $stmt->execute();
    $stmt->close();
    log_message("removed proposal $proposal from batch $batch");
    }

function getProposerID($proposal)
    {
    $row = dbQueryByID('select `proposerid` from proposal where id=?',$proposal);
    return $row['proposerid'];
    }

function updateProposalLastedit($proposal,$user)
    {
    $row = dbQueryByID('select access from proposal where id=?',$proposal);
    $access = unserialize($row['access']);
    if (!$access)
        $access = array();
    if (!isset($access['lastedit']))
        $access['lastedit'] = array();
    $access['lastedit'][$user] = strftime('%F %T');
    $access_ser = serialize($access);
    $stmt = dbPrepare('update proposal set access=? where id=?');
    $stmt->bind_param('si',$access_ser,$proposal);
    $stmt->execute();
    $stmt->close();
    }

function changeProposalTitle($proposal,$newtitle)
    {
    $isOwner = (getProposerID($proposal) == $_SESSION['userid']);
    if (!hasPrivilege('scheduler') && (!$isOwner))
        return;
    $stmt = dbPrepare('update proposal set title=? where id=?');
    $stmt->bind_param('si',$newtitle,$proposal);
    $stmt->execute();
    $stmt->close();
    updateProposalLastedit($proposal,$_SESSION['userid']);
    log_message("changed proposal $proposal title to '$newtitle'");
    }

function changeProposalInfo($proposal,$fieldnum,$newinfo)
    {
    $isOwner = (getProposerID($proposal) == $_SESSION['userid']);
    if (!hasPrivilege('scheduler') && (!$isOwner))
        return;
    $info_ser = dbQueryByID('select info from proposal where id=?',$proposal);
    if ($info_ser == NULL)
        return;
    $info = unserialize($info_ser['info']);
    $oldinfo = $info[$fieldnum][1];
    $info[$fieldnum] = array($info[$fieldnum][0], $newinfo);
    $info_ser = serialize($info);
    $stmt = dbPrepare('update proposal set info=? where id=?');
    $stmt->bind_param('si',$info_ser,$proposal);
    $stmt->execute();
    $stmt->close();
    updateProposalLastedit($proposal,$_SESSION['userid']);
    log_message("changed proposal $proposal field $fieldnum from '$oldinfo' to '$newinfo'");
    }

function changeProposalOrgfield($proposal,$fieldlabel,$newinfo)
    {
    if (!hasPrivilege('scheduler') && (getProposerID($proposal) != $_SESSION['userid']))
        return;
    $orgfields_ser = dbQueryByID('select orgfields from proposal where id=?',$proposal);
    if ($orgfields_ser == NULL)
        return;
    $orgfields = unserialize($orgfields_ser['orgfields']);
    $oldinfo = $orgfields[$fieldlabel];
    $orgfields[$fieldlabel] = $newinfo;
    $orgfields_ser = serialize($orgfields);
    $stmt = dbPrepare('update proposal set orgfields=? where id=?');
    $stmt->bind_param('si',$orgfields_ser,$proposal);
    $stmt->execute();
    $stmt->close();
    log_message("changed proposal $proposal field $fieldlabel from '$oldinfo' to '$newinfo'");
    }

function changeProposalAvail($proposal,$daynum,$newinfo)
    {
    if (!hasPrivilege('scheduler') && (getProposerID($proposal) != $_SESSION['userid']))
        return;
    $info_ser = dbQueryByID('select availability from proposal where id=?',$proposal);
    if ($info_ser == NULL)
        return;
    $info = unserialize($info_ser['availability']);
    $oldinfo = $info[$daynum];
    $info[$daynum] = $newinfo;
    $info_ser = serialize($info);
    $stmt = dbPrepare('update proposal set availability=? where id=?');
    $stmt->bind_param('si',$info_ser,$proposal);
    $stmt->execute();
    $stmt->close();
    updateProposalLastedit($proposal,$_SESSION['userid']);
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
    $info[$fieldnum] = array($info[$fieldnum][0], $newinfo);
    $info_ser = serialize($info);
    $stmt = dbPrepare('update venue set info=? where id=?');
    $stmt->bind_param('si',$info_ser,$venue);
    $stmt->execute();
    $stmt->close();
    log_message("changed venue $venue field $fieldnum from '$oldinfo' to '$newinfo'");
    }

function setLatLon($venue,$latlon)
    {
    $info_ser = dbQueryByID('select info from venue where id=?',$venue);
    if ($info_ser == NULL)
        return;
    $info = unserialize($info_ser['info']);
    $pos = explode(',',$latlon);
    $count = 0;
    foreach ($info as $data)
        {
        if ($data[0] == 'latitude')
            $data[1] = $pos[0];
        else if ($data[0] == 'longitude')
            $data[1] = $pos[1];
        $info[$count] = $data;
        $count++;
        }
    $info_ser = serialize($info);
    $stmt = dbPrepare('update venue set info=? where id=?');
    $stmt->bind_param('si',$info_ser,$venue);
    $stmt->execute();
    $stmt->close();
    log_message("changed venue $venue latitude/longitude to $latlon");
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

function addVenueInfoField($venue,$fieldname)
    {
    $info_ser = dbQueryByID('select info from venue where id=?',$venue);
    if ($info_ser == NULL)
        return;
    $info = unserialize($info_ser['info']);
    $info[] = array($fieldname,'');
    $info_ser = serialize($info);
    $stmt = dbPrepare('update venue set info=? where id=?');
    $stmt->bind_param('si',$info_ser,$venue);
    $stmt->execute();
    $stmt->close();
    log_message("added field '$fieldname' to venue $venue");
    }

function deleteVenueInfoField($venue,$fieldnum)
    {
    $info_ser = dbQueryByID('select info from venue where id=?',$venue);
    if ($info_ser == NULL)
        return;
    $info = unserialize($info_ser['info']);
    unset($info[$fieldnum]);
    $info_ser = serialize($info);
    $stmt = dbPrepare('update venue set info=? where id=?');
    $stmt->bind_param('si',$info_ser,$venue);
    $stmt->execute();
    $stmt->close();
    log_message("deleted field $fieldnum from venue $venue");
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
    log_message("subscribing $address to $mailinglist");
    if (loggedMail($email, $subject, $body))
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

function batchChangeContact($batchid,$newcontact)
    {
    $proposals = array();
    $stmt = dbPrepare('select proposal.id from proposal join proposalBatch on proposal.id=proposalBatch.proposal_id where proposalBatch.batch_id=?');
    $stmt->bind_param('i',$batchid);
    $stmt->execute();
    $stmt->bind_result($proposal_id);
    while ($stmt->fetch())
        $proposals[] = $proposal_id;
    $stmt->close();
    foreach ($proposals as $pid)
        {
        $stmt = dbPrepare('update proposal set orgcontact=? where id=?');
        $stmt->bind_param('ii',$newcontact,$pid);
        $stmt->execute();
        $stmt->close();
        }
    log_message("changed contact for batch $batchid to $newcontact");
    $_SESSION['adminmessage'] .= '<p>changed contact for <a href="batch.php?id=' . $batchid . '">batch</a></p>';
    }

function addProposalInfoField($proposal,$fieldname)
    {
    $info_ser = dbQueryByID('select info from proposal where id=?',$proposal);
    if ($info_ser == NULL)
        return;
    $info = unserialize($info_ser['info']);
    $info[] = array($fieldname,'');
    $info_ser = serialize($info);
    $stmt = dbPrepare('update proposal set info=? where id=?');
    $stmt->bind_param('si',$info_ser,$proposal);
    $stmt->execute();
    $stmt->close();
    log_message("added field '$fieldname' to proposal $proposal");
    }

function prefsSummaryFields()
    {
    $labels = array();
    if (!isset($_POST['summaryLabel']))
        return;
    foreach ($_POST['summaryLabel'] as $s)
        $labels[] = $s;
    if (!isset($_SESSION['preferences']))
        $_SESSION['preferences'] = array();
    $_SESSION['preferences']['summaryFields'] = $labels;
    savePreferences();
    log_message('changed summaryFields in preferences');
    }

function changeGroupPerformer($showorder,$time,$note,$groupperformerid)
    {
    $stmt = dbPrepare('update groupPerformer set showorder=?,time=?,note=? where id=?');
    $stmt->bind_param('iisi',$showorder,$time,$note,$groupperformerid);
    $stmt->execute();
    $stmt->close();
    log_message("changeGroupPerformer $groupperformerid to $showorder $time '$note'");
    }

function cancelGroupPerformer($groupperformerid)
    {
    $stmt = dbPrepare('update groupPerformer set cancelled=1 where id=?');
    $stmt->bind_param('i',$groupperformerid);
    $stmt->execute();
    $stmt->close();
    log_message('cancelGroupPerformer ' . $groupperformerid);
    }

function uncancelGroupPerformer($groupperformerid)
    {
    $stmt = dbPrepare('update groupPerformer set cancelled=0 where id=?');
    $stmt->bind_param('i',$groupperformerid);
    $stmt->execute();
    $stmt->close();
    log_message('uncancelGroupPerformer ' . $groupperformerid);
    }

function deleteGroupPerformer($groupperformerid)
    {
    $stmt = dbPrepare('delete from groupPerformer where id=?');
    $stmt->bind_param('i',$groupperformerid);
    $stmt->execute();
    $stmt->close();
    log_message('deleteGroupPerformer ' . $groupperformerid);
    }

function newBatchColumn($columnname,$fieldlabel,$defaultvalue,$batchid)
    {
    $festival = getFestivalID();
    if ($batchid == 0)
        {
        $stmt = dbPrepare('select id, info, orgfields from proposal where festival=? and deleted=0');
        $stmt->bind_param('i',$festival);
        }
    else
        {
        $stmt = dbPrepare('select id, info, orgfields from proposal join proposalBatch on proposal.id=proposalBatch.proposal_id where proposalBatch.batch_id=? and festival=? and deleted=0');
        $stmt->bind_param('ii',$batchid,$festival);
        }
    $stmt->execute();
    $stmt->bind_result($id,$info_ser,$orgfields_ser);
    $prop = array();
    while ($stmt->fetch())
        {
        $prop[$id] = array("info"=>$info_ser, "orgfields"=>$orgfields_ser);
        }
    $stmt->close();
    foreach ($prop as $id=>$data)
        {
        $info = unserialize($data["info"]);
        $orgfields = unserialize($data["orgfields"]);
        $columnval = $defaultvalue;
        foreach ($info as $formrow)
            {
            if (stripos($formrow[0],$fieldlabel) !== FALSE)
                {
                $columnval = $formrow[1];
                break;
                }
            }
        $orgfields[$columnname] = $columnval;
        $orgfields_ser = serialize($orgfields);
        $stmt = dbPrepare('update proposal set orgfields=? where id=?');
        $stmt->bind_param('si',$orgfields_ser,$id);
        $stmt->execute();
        $stmt->close();
        }
    log_message("added batch column '$columnname' (field '$fieldlabel', default '$defaultvalue')");
    if (!isset($_SESSION['preferences']))
        $_SESSION['preferences'] = array();
    if (!isset($_SESSION['preferences']['summaryFields']))
        $_SESSION['preferences']['summaryFields'] = array();
    if (!in_array($columnname, $_SESSION['preferences']['summaryFields']))
        {
        $_SESSION['preferences']['summaryFields'][] = $columnname;
        savePreferences();
        }
    global $returnurl;
    $returnurl = 'batch.php?id=' . $batchid;
    }

function autobatch($newbatchid,$fieldlabel,$exactlabel,$value,$exactvalue,$frombatchid)
    {
    $festival = getFestivalID();
    if ($frombatchid == 0)
        {
        $stmt = dbPrepare('select id, info from proposal where festival=? and deleted=0');
        $stmt->bind_param('i',$festival);
        }
    else
        {
        $stmt = dbPrepare('select id, info from proposal join proposalBatch on proposal.id=proposalBatch.proposal_id where proposalBatch.batch_id=? and festival=? and deleted=0');
        $stmt->bind_param('ii',$frombatchid,$festival);
        }
    $stmt->execute();
    $stmt->bind_result($id,$info_ser);
    $prop = array();
    while ($stmt->fetch())
        {
        $info = unserialize($info_ser);
        foreach ($info as $formrow)
            {
            if ((($exactlabel==1) && (strcasecmp($formrow[0],$fieldlabel) == 0)) ||
                (($exactlabel=='') && (stripos($formrow[0],$fieldlabel) !== FALSE)))
                {
                if ((($exactvalue==1) && (strcasecmp($formrow[1],$value) == 0)) ||
                    (($exactvalue=='') && (stripos($formrow[1],$value) !== FALSE)))
                    {
                    $prop[] = $id;
                    break;
                    }
                }
            }
        }
    $stmt->close();
    foreach ($prop as $id)
        {
        addToBatch($id,$newbatchid);
        }
    log_message("autobatch from $frombatchid to $newbatchid (field '$fieldlabel'($exactlabel), value '$value'($exactvalue))");
    global $returnurl;
    $returnurl = 'batch.php?id=' . $newbatchid;
    }

function addNote($entity,$note)
    {
    $noteid = newEntityID('note');
    $creatorid = $_SESSION['userid'];
    $stmt = dbPrepare('insert into `note` (`id`, `creatorid`, `note`) values (?,?,?)');
    $stmt->bind_param('iis',$noteid,$creatorid,$note);
    $stmt->execute();
    $stmt->close();
    $stmt = dbPrepare('insert into `noteLink` (`note_id`, `entity_id`) values (?,?)');
    $stmt->bind_param('ii',$noteid,$entity);
    $stmt->execute();
    $stmt->close();
    log_message("added note $noteid '$note' to $entity");
    }

function changeNote($noteid,$note)
    {
    $stmt = dbPrepare('update `note` set `note`=? where `id`=?');
    $stmt->bind_param('si',$note,$noteid);
    $stmt->execute();
    $stmt->close();
    log_message("changed note $noteid to '$note'");
    }

function linkNote($noteid,$entityid)
    {
    $stmt = dbPrepare('insert into `noteLink` (`note_id`, `entity_id`) values (?,?)');
    $stmt->bind_param('ii',$noteid,$entityid);
    $stmt->execute();
    $stmt->close();
    log_message("linked note $noteid to $entityid");
    }

function unlinkNote($noteid,$entityid)
    {
    $stmt = dbPrepare('delete from `noteLink` where `note_id`=? and `entity_id`=?');
    $stmt->bind_param('ii',$noteid,$entityid);
    $stmt->execute();
    $stmt->close();
    log_message("removed note $noteid from $entityid");
    }

function batchAddInfoField($batchid,$fieldname)
    {
    $stmt = dbPrepare('select proposal_id from proposalBatch where batch_id=?');
    $stmt->bind_param('i',$batchid);
    $stmt->execute();
    $stmt->bind_result($id);
    $props = array();
    while ($stmt->fetch())
        $props[] = $id;
    $stmt->close();
    foreach ($props as $p)
        addProposalInfoField($p,$fieldname);
    }

function grantProposalAccess($proposal,$user,$mode)
    {
    $row = dbQueryByID('select access from proposal where id=?',$proposal);
    $access = unserialize($row['access']);
    if (!$access)
        $access = array();
    if (!isset($access[$user]))
        $access[$user] = array();
    if (!in_array($mode,$access[$user]))
        {
        $access[$user][] = $mode;
        $access_ser = serialize($access);
        $stmt = dbPrepare('update proposal set access=? where id=?');
        $stmt->bind_param('si',$access_ser,$proposal);
        $stmt->execute();
        $stmt->close();
        log_message("granted user $user access '$mode' on proposal $proposal");
        }
    else
        log_message("user $user already has access '$mode' on proposal $proposal");
    }

?>
