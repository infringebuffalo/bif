<?php
require_once 'init.php';
connectDB();
require_once 'util.php';
require_once 'scheduler.php';
requirePrivilege(array('admin'),'undumping proposal');

bifPageheader('proposal submitted');

$id = GETvalue('id',0);
$stmt = dbPrepare('select data from dump where id=?');
$stmt->bind_param('i',$id);
if (!$stmt->execute())
    errorAndQuit("Database error: " . $stmt->error,true);
$stmt->bind_result($data);
if (!$stmt->fetch())
    errorAndQuit("Database error: " . $stmt->error,true);
$stmt->close();

$d = json_decode($data,true);
foreach (array_keys($d) as $i)
    $_POST[$i] = $d[$i];

$proposaltype = POSTvalue('Proposal_Type');
$festival = getFestivalID();
$batchid = getBatch($proposaltype,$festival);
$orgcontact = orgContact($proposaltype);
$title = POSTvalue('title');
if (trim($title) == '') $title = 'NEEDS A TITLE';
$proposerid = $_SESSION['userid'];
log_message("submitProposal \"$title\", type \"$proposaltype\"");

createUniversalProposal($title,$proposerid,$festival,$batchid,$orgcontact);
?>
<p>
Congratulations!<br>
You have successfully submitted a proposal for the 2017 Buffalo
Infringement Festival that runs from July 27 - August 6, 2017!<br>
Be sure to check out your proposal which should now be displayed prominently on the home page!
<br>
Remember - all proposals are accepted.  As soon as you hear back from
your genre organizer, you're in!<br>
You can alter your proposal at any time by logging into our site.<br>
Please check your email often and respond to messages from organizers
promptly (please check your spam folder, in case our messages end up
there).<br>
Expect an influx of correspondence after May 1.
</p>
<p>
Be sure to <a href="http://infringebuffalo.org/forum/" target="_blank">join our Infringement Forum!</a>  It's a great place to ask questions, collaborate with other artist and get more involved with Infringement in general.
</p>

<p>
If you have any questions, ask away:
</p>
<ul>
<li>General/PR: pr@infringebuffalo.org / info@infringebuffalo.org
<li>Music: Curt, steelcrazybooking@gmail.com
<li>Theater: Jessica, jessicaknoerl@gmail.com
<li>Poetry/Literary: Marek, b00bflo@gmail.com
<li>Dance: Leslie, danceundertheradar@gmail.com
<li>Film: Tom, tms@kitefishlabs.com
<li>Street performance: David, dga8787@aol.com
<li>Visual Arts: Cat/Amy, visualinfringement@live.com
</ul>

<p>
Facebook: <a href="https://www.facebook.com/groups/22033482171/">https://www.facebook.com/groups/22033482171/</a> <a href="https://www.facebook.com/InfringeEveryDay">https://www.facebook.com/InfringeEveryDay</a><br>
Twitter: <a href="https://twitter.com/InfringeBuffalo">https://twitter.com/InfringeBuffalo</a>
</p>

<p>
Infringe Everyday!
</p>

<?php
bifPagefooter();


function userInfo($email)
    {
    return dbQueryByString('select id,email,name,phone from user where email=?', $email);
    }

function orgContact($proposaltype)
    {
    if ($proposaltype == 'Music')
        return userInfo('steelcrazybooking@gmail.com');
    else if ($proposaltype == 'Dance')
        return userInfo('danceundertheradar@gmail.com');
    else if ($proposaltype == 'Theatre')
        return userInfo('jessicaknoerl@gmail.com');
    else if ($proposaltype == 'Film/Video')
        return userInfo('tms@kitefishlabs.com');
    else if ($proposaltype == 'Visual_Art')
        return userInfo('visualinfringement@live.com');
    else if ($proposaltype == 'Literary')
        return userInfo('marekp@roadrunner.com');
    else 
        return userInfo('depape@buffalo.edu');
    }

function contactInfo()
    {
    $contactname = POSTvalue('contactname');
    $contactemail = POSTvalue('contactemail');
    $contactphone = POSTvalue('contactphone');
    $contactaddress = POSTvalue('contactaddress');
    $contactfacebook = POSTvalue('contactfacebook');
    $contactmethod = POSTvalue('bestcontactmethod');
    return "$contactname\nE-mail: $contactemail\nPhone: $contactphone\nAddress: $contactaddress\nFacebook: $contactfacebook\nBest contact method: $contactmethod";
    }

function secondContactInfo()
    {
    $contact2name = POSTvalue('secondcontactname');
    $contact2email = POSTvalue('secondcontactemail');
    $contact2phone = POSTvalue('secondcontactphone');
    $contact2address = POSTvalue('secondcontactaddress');
    return "$contact2name\nE-mail: $contact2email\nPhone: $contact2phone\nAddress: $contact2address";
    }

function createUniversalProposal($title,$proposerid,$festival,$batchid,$orgcontact)
    {
    $info = array();
    addInfo($info,'Contact info',contactInfo());
    addInfo($info,'Secondary contact info', secondContactInfo());
	foreach ($_POST as $param_name => $param_val)
        {
	    if ($param_name !== 'contactname' || $param_name !== 'contactemail' || $param_name !== 'contactphone' || $param_name !== 'contactaddress' || $param_name !== 'contactfacebook' || $param_name !== 'bestcontactmethod' || $param_name !== 'secondcontactname' || $param_name !== 'secondcontactphone' || $param_name !== 'secondcontactaddress' || $param_name !== 'secondcontactemail')
            {
	        addInfo($info,$param_name, $param_val);
	        }
	    }
    insertProposal($info,$proposerid,$festival,$title,$orgcontact,$batchid);
    }

function addInfo(&$info,$label,$value)
    {
    $info[] = array($label,$value);
    }

function insertProposal($info,$proposerid,$festival,$title,$orgcontact,$batchid)
    {
    $info_json = json_encode($info);
    $orgfields_json = json_encode(array());
    $proposalid = newEntityID('proposal');
    $orgcontactid = $orgcontact['id'];
    $formtext = createFormText($info, $title);
    $forminfo = array(POSTvalue("formtype")=>$formtext);
    $forminfo_json = json_encode($forminfo);
    $stmt = dbPrepare('insert into `proposal` (`id`, `proposerid`, `festival`, `title`, `info_json`, `forminfo_json`, `orgcontact`, `orgfields_json`) values (?,?,?,?,?,?,?,?)');
    $stmt->bind_param('iiisssis',$proposalid,$proposerid,$festival,$title,$info_json,$forminfo_json,$orgcontactid,$orgfields_json);
    $stmt->execute();
    $stmt->close();
    if ($batchid != 0)
        {
        $stmt = dbPrepare('insert into `proposalBatch` (`proposal_id`, `batch_id`) values (?,?)');
        $stmt->bind_param('ii',$proposalid,$batchid);
        $stmt->execute();
        $stmt->close();
        }
    emailProposal($formtext,$proposerid,$orgcontact);
    echo "<a href=proposal.php?id=$proposalid>$title</a><br>\n";
    }

function createFormText($info,$title)
    {
    $text = "Title:\n$title\n\n";
    foreach ($info as $i)
        {
        $text .= $i[0] . ":\n";
        $text .= $i[1] . "\n\n";
        }
    return $text;
    }

function emailProposal($formtext,$proposerid,$orgcontact)
    {
    $body = "The following proposal has been submitted for the Buffalo Infringement Festival:\r\n\r\n" . $formtext;
    $row = dbQueryByID("select email from user where id=?",$proposerid);
    $addr = $row['email'];
    $orgaddr = $orgcontact['email'];
    $subject = "Buffalo Infringement proposal";
    loggedMail($addr, $subject, $body);
    loggedMail($orgaddr, $subject, "(Copy of mail sent to $addr)\r\n\r\n" . $body);
    }

?>
