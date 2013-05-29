<?php
require_once 'init.php';
connectDB();
requirePrivilege('scheduler');
require_once 'util.php';
require '../bif.php';

$orgcontacts = array('music'=>2,
                     'dance'=>141,
                     'theatre'=>84,
                     'film'=>58,
                     'visualart'=>78,
                     'literary'=>43
                    );
bifPageheader('new proposal');
$formtype = POSTvalue('formtype');
$festival = getFestivalID();
$batchid = getBatch($formtype,$festival,"All $formtype acts");
$orgcontact = $orgcontacts[$formtype];
$title = POSTvalue('title');
$proposer = POSTvalue('proposer');
$proposerName = POSTvalue('proposername');
log_message("createProposal \"$title\", type \"$formtype\"");

if ($formtype == 'music')
    createMusicProposal($title,$proposer,$proposerName,$festival,$batchid,$orgcontact);
else if ($formtype == 'dance')
    createDanceProposal($title,$proposer,$proposerName,$festival,$batchid,$orgcontact);
else if ($formtype == 'theatre')
    createTheatreProposal($title,$proposer,$proposerName,$festival,$batchid,$orgcontact);
else if ($formtype == 'film')
    createFilmProposal($title,$proposer,$proposerName,$festival,$batchid,$orgcontact);
else if ($formtype == 'visualart')
    createVisualartProposal($title,$proposer,$proposerName,$festival,$batchid,$orgcontact);
else if ($formtype == 'literary')
    createLiteraryProposal($title,$proposer,$proposerName,$festival,$batchid,$orgcontact);


function getBatch($name,$festival,$desc)
    {
    $stmt = dbPrepare('select id from batch where name=?');
    $stmt->bind_param('s',$name);
    $stmt->execute();
    $stmt->bind_result($id);
    if ($stmt->fetch())
        {
        $stmt->close();
        return $id;
        }
    else
        {
        $stmt->close();
        return createBatch($name,$festival,$desc);
        }
    }

function createBatch($name,$festival,$desc)
    {
    $id = newEntityID('batch');
    $stmt = dbPrepare('insert into batch (id,name,festival,description) values (?,?,?,?)');
    $stmt->bind_param('isis',$id,$name,$festival,$desc);
    $stmt->execute();
    return $id;
    }

function createMusicProposal($title,$proposer,$proposerName,$festival,$batchid,$orgcontact)
    {
    $proposerid = getUserID($proposer);
    if ($proposerid == 0)
        $proposerid = createUser($proposer,$proposerName,'','');
    $info = array();
    addInfo($info,'Contact info', "$proposerName\n$proposer\nPhone:\nAddress:\n\nSecond contact:");
    addInfo($info,'Type', 'music');
    addInfo($info,'# of band members','');
    addInfo($info,'Names and roles','');
    addInfo($info,'Website','');
    addInfo($info,'Facebook etc','');
    addInfo($info,'Everyone over 21','');
    addInfo($info,'Any other proposals','');
    addInfo($info,'Main genre','');
    addInfo($info,'Secondary genre','');
    addInfo($info,'Description','');
    addInfo($info,'Current/previous groups/projects','');
    addInfo($info,'Have own PA','');
    addInfo($info,'Play without amplification','');
    addInfo($info,'Willing to busk','');
    addInfo($info,'Share PA','');
    addInfo($info,'Share drum kit','');
    addInfo($info,'Have tables &amp; mixer','');
    addInfo($info,'Describe gear etc','');
    addInfo($info,'Equipment to share','');
    addInfo($info,'How loud','');
    addInfo($info,'Setup time','');
    addInfo($info,'How does it infringe','');
    addInfo($info,'Traveling/housing','');
    addInfo($info,'Travel/housing details','');
    addInfo($info,'Previous festivals','');
    addInfo($info,'Other artists to perform with','');
    addInfo($info,'Other Buffalo venues','');
    addInfo($info,'Anti-warped','');
    addInfo($info,'Other gigs besides Anti-warped','');
    addInfo($info,'Opening or closing ceremonies','');
    addInfo($info,'Type of venue','');
    addInfo($info,'Number of shows','');
    addInfo($info,'How help BIF','');
    addInfo($info,'Will volunteer','');
    addInfo($info,'Any questions/concerns','');
    addInfo($info,'Description for web','');
    addInfo($info,'Description for brochure','');
    $availability = array();
    for ($d = 0; $d < 11; $d++)
        $availability[$d] = 'yes';
    insertProposal($info,$availability,$proposerid,$festival,$title,$orgcontact,$batchid);
    }

function createDanceProposal($title,$proposer,$proposerName,$festival,$batchid,$orgcontact)
    {
    $proposerid = getUserID($proposer);
    if ($proposerid == 0)
        $proposerid = createUser($proposer,$proposerName,'','');
    $info = array();
    addInfo($info,'Contact info', "$proposerName\n$proposer\nPhone:\nAddress:\nBest contact method:");
    addInfo($info,'Type', 'dance');
    addInfo($info,'Website','');
    addInfo($info,'Group','');
    addInfo($info,'Description','');
    addInfo($info,'Names of all performers','');
    addInfo($info,'Over age 21','');
    addInfo($info,'Image link','');
    addInfo($info,'How does it infringe','');
    addInfo($info,'Venue needs','');
    addInfo($info,'Can perform in non-traditional space','');
    addInfo($info,'Willing to perform to live band','');
    addInfo($info,'Pre-arranged venue','');
    addInfo($info,'Admission','');
    addInfo($info,'Other artists you\'d like to be booked with','');
    addInfo($info,'Other infringement projects','');
    addInfo($info,'Previous infringement festivals','');
    addInfo($info,'Out of town / housing','');
    addInfo($info,'How will you help Infringement','');
    addInfo($info,'What can you provide to help','');
    addInfo($info,'Any questions','');
    addInfo($info,'Description for web','');
    addInfo($info,'Description for brochure','');
    $availability = array();
    for ($d = 0; $d < 11; $d++)
        $availability[$d] = "yes";
    insertProposal($info,$availability,$proposerid,$festival,$title,$orgcontact,$batchid);
    }

function createTheatreProposal($title,$proposer,$proposerName,$festival,$batchid,$orgcontact)
    {
    $proposerid = getUserID($proposer);
    if ($proposerid == 0)
        $proposerid = createUser($proposer,$proposerName,'','');
    $info = array();
    addInfo($info,'Contact info', "$proposerName\n$proposer\nPhone:\nAddress:\nFacebook:\nBest contact method:");
    addInfo($info,'Type', 'theatre');
    addInfo($info,'Organization', '');
    addInfo($info,'Website', '');
    addInfo($info,'Description', '');
    addInfo($info,'Image link', '');
    addInfo($info,'Number of performers', '');
    addInfo($info,'Setup time', '');
    addInfo($info,'Length of performance', '');
    addInfo($info,'Strike time', '');
    addInfo($info,'Pre-arranged venue', '');
    addInfo($info,'Street theatre', '');
    addInfo($info,'Interested in non-traditional venue', '');
    addInfo($info,'Description of desired venue', '');
    addInfo($info,'Requested venue features', '');
    addInfo($info,'Number of performances', '');
    addInfo($info,'Previous infringement festivals', '');
    addInfo($info,'How does it infringe', '');
    addInfo($info,'Out of town / housing', '');
    addInfo($info,'How will you help Infringement', '');
    addInfo($info,'What can you provide to help', '');
    addInfo($info,'Any questions', '');
    addInfo($info,'Description for web', '');
    addInfo($info,'Description for brochure', '');
    $availability = array();
    for ($d = 0; $d < 11; $d++)
        $availability[$d] = "yes";
    insertProposal($info,$availability,$proposerid,$festival,$title,$orgcontact,$batchid);
    }

function createFilmProposal($title,$proposer,$proposerName,$festival,$batchid,$orgcontact)
    {
    $proposerid = getUserID($proposer);
    if ($proposerid == 0)
        $proposerid = createUser($proposer,$proposerName,'','');
    $info = array();
    addInfo($info,'Contact info', "$proposerName\n$proposer\nPhone:\nAddress:\nFacebook:\nBest contact method:");
    addInfo($info,'Type','film');
    addInfo($info,'Website','');
    addInfo($info,'Length','');
    addInfo($info,'Description','');
    addInfo($info,'Family friendly','');
    addInfo($info,'Over age 21','');
    addInfo($info,'Image link','');
    addInfo($info,'Venue needs','');
    addInfo($info,'Other infringement projects','');
    addInfo($info,'Previous infringement festivals','');
    addInfo($info,'How will you help Infringement','');
    addInfo($info,'What can you provide to help','');
    addInfo($info,'Any questions','');
    addInfo($info,'Description for web','');
    addInfo($info,'Description for brochure','');
    $availability = array();
    for ($d = 0; $d < 11; $d++)
        $availability[$d] = "yes";
    insertProposal($info,$availability,$proposerid,$festival,$title,$orgcontact,$batchid);
    }

function createVisualartProposal($title,$proposer,$proposerName,$festival,$batchid,$orgcontact)
    {
    $proposerid = getUserID($proposer);
    if ($proposerid == 0)
        $proposerid = createUser($proposer,$proposerName,'','');
    $info = array();
    addInfo($info,'Contact info', "$proposerName\n$proposer\nPhone:\nAddress:\nBest contact method:");
    addInfo($info,'Type','visualart');
    addInfo($info,'Description','');
    addInfo($info,'Medium','');
    addInfo($info,'Number of pieces','');
    addInfo($info,'Dimensions of each piece','');
    addInfo($info,'Dimensions of entire project','');
    addInfo($info,'Website','');
    addInfo($info,'Pre-arranged venue','');
    addInfo($info,'How does it infringe','');
    addInfo($info,'How will you help Infringement','');
    addInfo($info,'Out of town / housing','');
    addInfo($info,'Previous infringement festivals','');
    addInfo($info,'Other artists you\'d like to show with','');
    addInfo($info,'Availability (if work involves performance/presentation)','');
    addInfo($info,'Image link','');
    addInfo($info,'Other infringement projects','');
    addInfo($info,'Any questions','');
    addInfo($info,'Description for web','');
    addInfo($info,'Description for brochure','');
    $availability = array();
    insertProposal($info,$availability,$proposerid,$festival,$title,$orgcontact,$batchid);
    }

function createLiteraryProposal($title,$proposer,$proposerName,$festival,$batchid,$orgcontact)
    {
    $proposerid = getUserID($proposer);
    if ($proposerid == 0)
        $proposerid = createUser($proposer,$proposerName,'','');
    $info = array();
    addInfo($info,'Contact info', "$proposerName\n$proposer\nPhone:\nAddress:\nBest contact method:");
    addInfo($info,'Type', 'literary');
    addInfo($info,'Website', '');
    addInfo($info,'Group', '');
    addInfo($info,'Description', '');
    addInfo($info,'Names of all performers', '');
    addInfo($info,'Over age 21', '');
    addInfo($info,'Image link', '');
    addInfo($info,'How does it infringe', '');
    addInfo($info,'Venue needs', '');
    addInfo($info,'Pre-arranged venue', '');
    addInfo($info,'Other artists you\'d like to perform with', '');
    addInfo($info,'Number of performances', '');
    addInfo($info,'Other infringement projects', '');
    addInfo($info,'Previous infringement festivals', '');
    addInfo($info,'Out of town / housing', '');
    addInfo($info,'How will you help Infringement', '');
    addInfo($info,'What can you provide to help', '');
    addInfo($info,'Any questions', '');
    addInfo($info,'Description for web', '');
    addInfo($info,'Description for brochure', '');
    $availability = array();
    for ($d = 0; $d < 11; $d++)
        $availability[$d] = "yes";
    insertProposal($info,$availability,$proposerid,$festival,$title,$orgcontact,$batchid);
    }

function addInfo(&$info,$label,$value)
    {
    $info[] = array($label,$value);
    }

function insertProposal($info,$availability,$proposerid,$festival,$title,$orgcontact,$batchid)
    {
    $info_ser = serialize($info);
    $availability_ser = serialize($availability);
    $forminfo_ser = serialize(array());
    $orgfields_ser = serialize(array());
    $proposalid = newEntityID('proposal');
    $stmt = dbPrepare('insert into `proposal` (`id`, `proposerid`, `festival`, `title`, `info`, `availability`, `forminfo`, `orgcontact`, `orgfields`) values (?,?,?,?,?,?,?,?,?)');
    $stmt->bind_param('iiissssis',$proposalid,$proposerid,$festival,$title,$info_ser,$availability_ser,$forminfo_ser,$orgcontact,$orgfields_ser);
    $stmt->execute();
    $stmt->close();
    if ($batchid != 0)
        {
        $stmt = dbPrepare('insert into `proposalBatch` (`proposal_id`, `batch_id`) values (?,?)');
        $stmt->bind_param('ii',$proposalid,$batchid);
        $stmt->execute();
        $stmt->close();
        }
    echo "<a href=proposal.php?id=$proposalid>$title</a><br>\n";
    flush();
    }

function createUser($email,$name,$phone,$snailmail)
    {
    echo "creating user $email<br>\n";
    $password = md5('biffy');
    $userid = newEntityID('user');
    $stmt = dbPrepare('insert into `user` (`id`, `email`, `name`, `password`, `phone`, `snailmail`) values (?,?,?,?,?,?)');
    $stmt->bind_param('isssss',$userid,$email,$name,$password,$phone,$snailmail);
    $stmt->execute();
    $stmt->close();
    return $userid;
    }

bifPagefooter();
?>
