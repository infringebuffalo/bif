<?php
require_once 'init.php';
connectDB();
require_once 'util.php';
require_once 'scheduler.php';
require_once 'apiFunctions.php';

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
            requirePrivilege('admin',"for api call '$this->name'");
        if ($this->schedulerPriv)
            requirePrivilege('scheduler',"for api call '$this->name'");
        $args = array();
        foreach ($this->params as $param)
            $args[] = REQUESTvalue($param->name);
        call_user_func_array($this->name,$args);
        }
    }

$api = array(new apiFunction('newVenue',1,0),
            new apiFunction('copyVenue',1,0),
            new apiFunction('newCard',1,0),
            new apiFunction('newBatch',1,0),
            new apiFunction('newCategory',1,0),
            new apiFunction('newGroupshow',1,0),
            new apiFunction('scheduleEvent',1,0),
            new apiFunction('scheduleGroupPerformer',1,0),
            new apiFunction('updateUserContact',0,0),
            new apiFunction('updateUserInfo',0,0),
            new apiFunction('updatePassword',0,0),
            new apiFunction('changeBatchDescription',1,0),
            new apiFunction('changeBatchMembers',1,0),
            new apiFunction('changeCategoryDescription',1,0),
            new apiFunction('addToBatch',1,0),
            new apiFunction('addToCategory',1,0),
            new apiFunction('removeFromBatch',1,0),
            new apiFunction('removeFromCategory',1,0),
            new apiFunction('changeProposalTitle',0,0),
            new apiFunction('changeProposalInfo',0,0),
            new apiFunction('changeProposalOrgfield',0,0),
            new apiFunction('deleteProposal',1,0),
            new apiFunction('undeleteProposal',1,0),
            new apiFunction('deleteVenue',1,0),
            new apiFunction('undeleteVenue',1,0),
            new apiFunction('changeVenueInfo',1,0),
            new apiFunction('changeVenueName',1,0),
            new apiFunction('changeVenueShortname',1,0),
            new apiFunction('addVenueInfoField',1,0),
            new apiFunction('deleteVenueInfoField',1,0),
            new apiFunction('changeListing',1,0),
            new apiFunction('cancelListing',1,0),
            new apiFunction('uncancelListing',1,0),
            new apiFunction('deleteListing',1,0),
            new apiFunction('subscribe',1,0),
            new apiFunction('addPrivilege',0,1),
            new apiFunction('removePrivilege',0,1),
            new apiFunction('batchChangeContact',1,0),
            new apiFunction('addProposalInfoField',1,0),
            new apiFunction('prefsSummaryFields',0,0),
            new apiFunction('changeGroupPerformer',1,0),
            new apiFunction('uncancelGroupPerformer',1,0),
            new apiFunction('cancelGroupPerformer',1,0),
            new apiFunction('deleteGroupPerformer',1,0),
            new apiFunction('newBatchColumn',1,0),
            new apiFunction('autobatch',1,0),
            new apiFunction('autoCategory',1,0),
            new apiFunction('addNote',1,0),
            new apiFunction('changeNote',1,0),
            new apiFunction('linkNote',1,0),
            new apiFunction('unlinkNote',1,0),
            new apiFunction('batchAddInfoField',1,0),
            new apiFunction('grantProposalAccess',1,0),
            new apiFunction('revokeProposalAccess',1,0),
            new apiFunction('setVenueLatLon',1,0),
            new apiFunction('getIconFromURL',1,0),
            new apiFunction('deleteBatch',1,0),
            new apiFunction('newContact',0,1),
            new apiFunction('updateFestivalContact',1,0),
            new apiFunction('deleteFestivalContact',0,1)
            );

$command = REQUESTvalue('command');
if ($command == '')
    errorAndQuit("api.php called with no command",true);

$returnurl = REQUESTvalue('returnurl');

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

?>
