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
        $this->schedulerPriv = $schedulerPriv;  
        $this->adminPriv = $adminPriv;
        }
    function call()
        {
        if ($this->adminPriv)
            requirePrivilege('admin');
        if ($this->schedulerPriv)
            requirePrivilege('scheduler');
        log_message('api calling ' . $this->name);
        call_user_func($this->name);
        }
    }

$api = array(new apiFunction('newVenue',1,0),
            new apiFunction('newCard',1,0),
            new apiFunction('newBatch',1,0),
            new apiFunction('newGroupshow',1,0),
            new apiFunction('scheduleEvent',1,0),
            new apiFunction('scheduleGroupPerformer',1,0),
            new apiFunction('updateContact',0,0)
            );

$command = POSTvalue('command');
if ($command == '')
    {
    header('Location: .');
    die();
    }
$called = 0;
foreach ($api as $a)
    {
    if ($a->name == $command)
        {
        $a->call();
        $called = 1;
        break;
        }
    }
if (!$called)
    log_message('unknown api command "' . $command . '"');

$returnurl = POSTvalue('returnurl');
if ($returnurl == '')
    header('location:' . $_SERVER['HTTP_REFERER']);
else
    header('location:' . $returnurl);


function newVenue()
    {
    $venueid = newEntityID('venue');
    $stmt = dbPrepare('insert into `venue` (`id`, `name`, `shortname`, `festival`, `info`) values (?,?,?,?,?)');
    $name = POSTvalue('name');
    $shortname = POSTvalue('shortname');
    $festival = getFestivalID();
    $info = serialize(array());
    $stmt->bind_param('issis',$venueid,$name,$shortname,$festival,$info);
    $stmt->execute();
    $stmt->close();
    log_message('newVenue ' . $venueid . ' : ' . $name);
    }

function newCard()
    {
    $cardid = newEntityID('card');
    $stmt = dbPrepare('insert into `card` (`id`, `userid`, `role`, `email`, `phone`, `snailmail`) values (?,?,?,?,?,?)');
    $userid = POSTvalue('userid');
    $role = POSTvalue('role');
    $email = POSTvalue('email');
    $phone = POSTvalue('phone');
    $snailmail = POSTvalue('snailmail');
    $stmt->bind_param('iissss',$cardid,$userid,$role,$email,$phone,$snailmail);
    $stmt->execute();
    $stmt->close();
    log_message('newCard ' . $cardid . ' : ' . $role . ' / ' . $email);
    }

function newBatch()
    {
    $batchid = newEntityID('batch');
    $stmt = dbPrepare('insert into `batch` (`id`, `name`, `festival`, `description`) values (?,?,?,?)');
    $name = POSTvalue('name');
    $festival = getFestivalID();
    $description = POSTvalue('description');
    $stmt->bind_param('isis',$batchid,$name,$festival,$description);
    $stmt->execute();
    $stmt->close();
    log_message('newBatch ' . $batchid . ' : ' . $name);
    }

function newGroupshow()
    {
    $showid = newEntityID('proposal');
    $stmt = dbPrepare('insert into `proposal` (`id`, `proposerid`, `festival`, `title`, `info`, `isgroupshow`) values (?,?,?,?,?,1)');
    $festival = getFestivalID();
    $proposerid = $_SESSION['userid'];
    $title = POSTvalue('title');
    $info = array('Description'=>POSTvalue('description'), 'batch'=>POSTvalue('batch'));
    $info_ser = serialize($info);
    $description = POSTvalue('description');
    $stmt->bind_param('iiiss',$showid,$proposerid,$festival,$title,$info_ser);
    $stmt->execute();
    $stmt->close();
    log_message('newGroupshow ' . $showid . ' : ' . $title);
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
                die($stmt->error);
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

?>
