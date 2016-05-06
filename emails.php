<?php
require_once 'init.php';
connectDB();
requirePrivilege(array('scheduler','organizer'));
require_once 'util.php';
require_once 'scheduler.php';

$type = GETvalue('type','');
if ($type === '')
    errorPage();
else
    {
    $commas = GETvalue('commas',0);
    getDatabase();
    if ($type == 'batch')
        $result = getBatchEmails();
    else if ($type == 'proposal')
        $result = getProposalEmails();
    bifPageheader($result['header']);
    echo $result['start'] . "\n";
    $output = "";
    $emails = removeDuplicates($result['emails']);
    foreach ($emails as $a)
        {
        if (($commas) && (strlen($output) > 0))
            $output .= ", ";
        $output .= $a;
        if (!$commas)
            $output .= "<br>\n";
        }
    echo $output;
    $uri = $_SERVER['REQUEST_URI'];
    if ($commas)
        {
        $uri = str_replace("commas=1","commas=0",$uri);
        echo "<p><a href='$uri'>(list without commas)</a></p>";
        }
    else
        {
        if (strpos($uri,"commas=0") === FALSE)
            $uri = $uri . "&commas=1";
        else
            $uri = str_replace("commas=0","commas=1",$uri);
        echo "<p><a href='$uri'>(list with commas)</a></p>";
        }
    bifPagefooter();
    }

function removeDuplicates($emails)
    {
    sort($emails,SORT_STRING|SORT_FLAG_CASE);
    $result = array();
    $prev = '';
    foreach ($emails as $e)
        {
        $e = trim($e);
        if (strcasecmp($e,$prev) != 0)
            {
            $result[] = $e;
            $prev = $e;
            }
        }
    return $result;
    }

function errorPage()
    {
    bifPageheader("Error");
    echo "<p>You have reached this page in error - the required argument was no given</p>";
    bifPagefooter();
    }

function getProposalEmails()
    {
    return array('header' => 'unimplemented', 'start'=>'<p>not implemented yet</p>', 'emails' => array());
    }

function getBatchEmails()
    {
    if (GETvalue('allcontacts',0) == 1)
        return getBatchEmailsAllContacts();
    else
        return getBatchEmailsJustProposers();
    }

function getBatchEmailsAllContacts()
    {
    $result = array();
    
    $id = GETvalue('id',0);
    if ($id == 0)
        $result['header'] = 'email for all proposals';
    else
        {
        $row = dbQueryByID('select name from `batch` where id=?',$id);
        $result['header'] = 'email for batch: ' . $row['name'];
        }
    
    $result['start'] = "<p>E-mail addresses for proposers, primary contacts, and secondary contacts (if different):</p>\n";
    
    $result['emails'] = array();
    
    if ($id == 0)
        {
        $festival = GETvalue('festival',getFestivalID());
        $stmt = dbPrepare('select user.email,proposal.info_json from user join proposal on proposerid=user.id where proposal.festival=? and deleted=0 order by title');
        $stmt->bind_param('i',$festival);
        }
    else
        {
        $stmt = dbPrepare('select user.email,proposal.info_json from user join proposal on proposerid=user.id join proposalBatch on proposal.id=proposalBatch.proposal_id where proposalBatch.batch_id=? and deleted=0 order by title');
        $stmt->bind_param('i',$id);
        }
    $stmt->execute();
    $stmt->bind_result($email,$info_json);
    while ($stmt->fetch())
        {
        $result['emails'][] = $email;
        $contacts = findContactEmails($info_json);
        foreach ($contacts as $c)
            $result['emails'][] = $c;
        }
    $stmt->close();
    
    return $result;
    }

function getBatchEmailsJustProposers()
    {
    $result = array();
    
    $id = GETvalue('id',0);
    if ($id == 0)
        $result['header'] = 'email for all proposals';
    else
        {
        $row = dbQueryByID('select name from `batch` where id=?',$id);
        $result['header'] = 'email for batch: ' . $row['name'];
        }
    
    $result['start'] = "<p>E-mail addresses for proposers only (not any other contacts):</p>\n";
    
    $result['emails'] = array();
    
    if ($id == 0)
        {
        $festival = GETvalue('festival',getFestivalID());
        $stmt = dbPrepare('select user.email from user join proposal on proposerid=user.id where proposal.festival=? and deleted=0 order by title');
        $stmt->bind_param('i',$festival);
        }
    else
        {
        $stmt = dbPrepare('select user.email from user join proposal on proposerid=user.id join proposalBatch on proposal.id=proposalBatch.proposal_id where proposalBatch.batch_id=? and deleted=0 order by title');
        $stmt->bind_param('i',$id);
        }
    $stmt->execute();
    $stmt->bind_result($email);
    while ($stmt->fetch())
        {
        $result['emails'][] = $email;
        }
    $stmt->close();
    
    return $result;
    }

function findContactEmails($info_json)
    {
    $emails = array();
    $info = json_decode($info_json);
    foreach ($info as $i)
        {
        if (($i[0] == 'Contact info') || ($i[0] == 'Secondary contact info'))
            {
            preg_match('/^E-mail: (.*)$/simU',$i[1],$matches);
            if (array_key_exists(1,$matches))
                $emails[] = $matches[1];
            }
        }
    return $emails;
    }

?>
