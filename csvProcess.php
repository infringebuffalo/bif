<?php
die('disabled');
/****** DO NOT USE THIS AS-IS - the 'info' field has changed structure, this script
  will produce incorrect data ('info' must now be an ordered list of arrays,
  each sub-array containing field name and field value) **************/

require_once 'init.php';
connectDB();
requirePrivilege('admin');
require_once 'util.php';
require '../bif.php';

$orgcontacts = array('music'=>2,
                     'dance'=>141,
                     'theatre'=>84,
                     'film'=>58,
                     'visualart'=>78,
                     'literary'=>43
                    );
bifPageheader('process spreadsheet');
if ($_FILES)
    {
    $formtype = POSTvalue('formtype');
    $festival = getFestivalID();
    $f = $_FILES['spreadsheet'];
    $fp = fopen($f['tmp_name'],'r');
    $headers = fgetcsv($fp);
    $batchid = getBatch($formtype,$festival,"All $formtype acts");
    $orgcontact = $orgcontacts[$formtype];
    while (true)
        {
        $data = fgetcsv($fp);
        if ($data === false)
            break;
        foreach ($data as $k=>$v)
            $data[$k] = filter_var($v, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        if ($formtype == 'music')
            processMusicProposal($data,$festival,$headers,$batchid,$orgcontact);
        else if ($formtype == 'dance')
            processDanceProposal($data,$festival,$headers,$batchid,$orgcontact);
        else if ($formtype == 'theatre')
            processTheatreProposal($data,$festival,$headers,$batchid,$orgcontact);
        else if ($formtype == 'film')
            processFilmProposal($data,$festival,$headers,$batchid,$orgcontact);
        else if ($formtype == 'visualart')
            processVisualartProposal($data,$festival,$headers,$batchid,$orgcontact);
        else if ($formtype == 'literary')
            processLiteraryProposal($data,$festival,$headers,$batchid,$orgcontact);
        }
    fclose($fp);
    }
 else
    echo '<p>nothing uploaded</p>';


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

function processMusicProposal($data,$festival,$headers,$batchid,$orgcontact)
    {
    $proposerid = getUserID($data[3]);
    if ($proposerid == 0)
        $proposerid = createUser($data[3],$data[2],$data[4],$data[5]);
    $title = $data[9];
    $info = array();
    $contact = $data[2] . "\n";
    $contact .= $data[3] . "\n";
    $contact .= $data[4] . "\n";
    $contact .= $data[5] . "\n\n";
    $contact .= $data[6] . "\n";
    $contact .= $data[7] . "\n";
    $contact .= $data[8] . "\n";
    $info['Contact info'] = $contact;
    $info['Type'] = 'music';
    $info['# of band members'] = $data[10];
    $info['Names and roles'] = $data[11];
    $info['Website'] = $data[12];
    $info['Facebook etc'] = $data[13];
    $info['Everyone over 21'] = $data[14];
    $info['Any other proposals'] = $data[44];
    $info['Main genre'] = $data[16];
    $info['Secondary genre'] = $data[17];
    $info['Description'] = $data[15];
    $info['Current/previous groups/projects'] = $data[48];
    $info['Have own PA'] = $data[18];
    $info['Play without amplification'] = $data[19];
    $info['Willing to busk'] = $data[21];
    $info['Share PA'] = $data[20];
    $info['Share drum kit'] = $data[22];
    $info['Have tables &amp; mixer'] = $data[62];
    $info['Describe gear etc'] = multiline($data[63]);
    $info['Equipment to share'] = $data[49];
    $info['How loud'] = $data[23];
    $info['Setup time'] = $data[24] . " (" . ($data[24]*6) . " minutes)";
    $info['How does it infringe'] = $data[26];
    $info['Traveling/housing'] = $data[27];
    $info['Travel/housing details'] = $data[50];
    $info['Previous festivals'] = $data[28];
    $info['Other artists to perform with'] = $data[29];
    $info['Other Buffalo venues'] = $data[30];
    $info['Anti-warped'] = $data[31];
    $info['Other gigs besides Anti-warped'] = $data[65];
    $info['Opening or closing ceremonies'] = $data[32];
    $info['Type of venue'] = $data[51];
    $info['Number of shows'] = $data[66];
    $info['How help BIF'] = multiline($data[52]);
    $info['Will volunteer'] = $data[25];
    $info['Any questions/concerns'] = $data[61];
    $info['Description for web'] = $data[45] . "\n" . $data[47] . "\n";
    $info['Description for brochure'] = $data[46];
    $availability = array();
    for ($d = 0; $d < 11; $d++)
        $availability[$d] = $data[33+$d];
    $orgfields = array();
    insertProposal($info,$availability,$headers,$data,$orgfields,$proposerid,$festival,$title,$orgcontact,$batchid);
    }

function processDanceProposal($data,$festival,$headers,$batchid,$orgcontact)
    {
    $proposerid = getUserID($data[3]);
    if ($proposerid == 0)
        $proposerid = createUser($data[3],$data[8],$data[5],$data[2]);
    $title = $data[7];
    $info = array();
    $contact = $data[8] . "\n";
    $contact .= $data[3] . "\n";
    $contact .= $data[2] . "\n";
    $contact .= $data[5] . "\n\n";
    $contact .= "Best contact method: " . $data[4] . "\n";
    $info['Contact info'] = $contact;
    $info['Type'] = 'dance';
    $info['Website'] = $data[1];
    $info['Group'] = $data[6];
    $info['Description'] = $data[9];
    $info['Names of all performers'] = $data[12];
    $info['Over age 21'] = $data[13];
    $info['Image link'] = $data[14];
    $info['How does it infringe'] = $data[15];
    $info['Venue needs'] = $data[16];
    $info['Can perform in non-traditional space'] = $data[17];
    $info['Willing to perform to live band'] = $data[18];
    $info['Pre-arranged venue'] = $data[19];
    $info['Admission'] = $data[20];
    $info['Other artists you\'d like to be booked with'] = $data[21];
    $info['Other infringement projects'] = $data[23];
    $info['Previous infringement festivals'] = $data[36];
    $info['Out of town / housing'] = $data[37];
    $info['How will you help Infringement'] = $data[38];
    $info['What can you provide to help'] = $data[39];
    $info['Any questions'] = $data[45];
    $info['Description for web'] = $data[10] . "\n" . $data[1] . "\n";
    $info['Description for brochure'] = $data[11];
    $availability = array();
    for ($d = 0; $d < 11; $d++)
        $availability[$d] = $data[25+$d];
    $orgfields = array();
    insertProposal($info,$availability,$headers,$data,$orgfields,$proposerid,$festival,$title,$orgcontact,$batchid);
    }

function processTheatreProposal($data,$festival,$headers,$batchid,$orgcontact)
    {
    $proposerid = getUserID($data[5]);
    if ($proposerid == 0)
        $proposerid = createUser($data[5],$data[4],$data[6],$data[7]);
    $title = $data[1];
    $info = array();
    $contact = $data[4] . "\n";
    $contact .= $data[5] . "\n";
    $contact .= $data[6] . "\n";
    $contact .= $data[7] . "\n";
    $contact .= "Facebook: " . $data[9] . "\n";
    $contact .= "Best contact method: " . $data[8] . "\n\n";
    $contact .= $data[10] . "\n";
    $contact .= $data[11] . "\n";
    $contact .= $data[12] . "\n";
    $info['Contact info'] = $contact;
    $info['Type'] = 'theatre';
    $info['Organization'] = $data[2];
    $info['Website'] = $data[3];
    $info['Description'] = $data[13];
    $info['Image link'] = $data[16];
    $info['Number of performers'] = $data[17];
    $info['Setup time'] = $data[18];
    $info['Length of performance'] = $data[19];
    $info['Strike time'] = $data[20];
    $info['Pre-arranged venue'] = $data[21];
    $info['Street theatre'] = $data[47];
    $info['Interested in non-traditional venue'] = $data[22];
    $info['Description of desired venue'] = $data[23];
    $info['Requested venue features'] = $data[24];
    $info['Number of performances'] = $data[25];
    $info['Previous infringement festivals'] = $data[37];
    $info['How does it infringe'] = $data[38];
    $info['Out of town / housing'] = $data[39];
    $info['How will you help Infringement'] = $data[40];
    $info['What can you provide to help'] = $data[41];
    $info['Any questions'] = $data[46];
    $info['Description for web'] = $data[14] . "\n" . $data[3] . "\n";
    $info['Description for brochure'] = $data[15];
    $availability = array();
    for ($d = 0; $d < 11; $d++)
        $availability[$d] = $data[26+$d];
    $orgfields = array();
    insertProposal($info,$availability,$headers,$data,$orgfields,$proposerid,$festival,$title,$orgcontact,$batchid);
    }

function processFilmProposal($data,$festival,$headers,$batchid,$orgcontact)
    {
    $proposerid = getUserID($data[8]);
    if ($proposerid == 0)
        $proposerid = createUser($data[8],$data[7],$data[3],$data[1]);
    $title = $data[5];
    $info = array();
    $contact = $data[7] . "\n";
    $contact .= $data[8] . "\n";
    $contact .= $data[3] . "\n";
    $contact .= $data[1] . "\n";
    $contact .= "Best contact method: " . $data[2] . "\n";
    $info['Contact info'] = $contact;
    $info['Type'] = 'film';
    $info['Website'] = $data[4];
    $info['Length'] = $data[6];
    $info['Description'] = $data[9];
    $info['Family friendly'] = $data[12];
    $info['Over age 21'] = $data[13];
    $info['Image link'] = $data[14];
    $info['Venue needs'] = $data[15];
    $info['Other infringement projects'] = $data[16];
    $info['Previous infringement festivals'] = $data[28];
    $info['How will you help Infringement'] = $data[29];
    $info['What can you provide to help'] = $data[30];
    $info['Any questions'] = $data[35];
    $info['Description for web'] = $data[10] . "\n" . $data[4] . "\n";
    $info['Description for brochure'] = $data[11];
    $availability = array();
    for ($d = 0; $d < 11; $d++)
        $availability[$d] = $data[17+$d];
    $orgfields = array();
    insertProposal($info,$availability,$headers,$data,$orgfields,$proposerid,$festival,$title,$orgcontact,$batchid);
    }

function processVisualartProposal($data,$festival,$headers,$batchid,$orgcontact)
    {
    $proposerid = getUserID($data[21]);
    if ($proposerid == 0)
        $proposerid = createUser($data[21],$data[1],$data[22],$data[23]);
    $title = $data[2];
    $info = array();
    $contact = $data[1] . "\n";
    $contact .= $data[21] . "\n";
    $contact .= $data[22] . "\n";
    $contact .= $data[23] . "\n";
    $info['Contact info'] = $contact;
    $info['Type'] = 'visualart';
    $info['Description'] = $data[3];
    $info['Medium'] = $data[4];
    $info['Number of pieces'] = $data[5];
    $info['Dimensions of each piece'] = $data[6];
    $info['Dimensions of entire project'] = $data[7];
    $info['Website'] = $data[8];
    $info['Pre-arranged venue'] = $data[9];
    $info['How does it infringe'] = $data[10];
    $info['How will you help Infringement'] = $data[11];
    $info['Out of town / housing'] = $data[12];
    $info['Previous infringement festivals'] = $data[13];
    $info['Other artists you\'d like to show with'] = $data[14];
    $info['Availability (if work involves performance/presentation)'] = $data[15];
    $info['Image link'] = $data[26];
    $info['Other infringement projects'] = $data[16];
    $info['Any questions'] = $data[28];
    $info['Description for web'] = $data[24] . "\n" . $data[8] . "\n";
    $info['Description for brochure'] = $data[25];
    $availability = array();
    $orgfields = array();
    insertProposal($info,$availability,$headers,$data,$orgfields,$proposerid,$festival,$title,$orgcontact,$batchid);
    }

function processLiteraryProposal($data,$festival,$headers,$batchid,$orgcontact)
    {
    $proposerid = getUserID($data[3]);
    if ($proposerid == 0)
        $proposerid = createUser($data[3],$data[2],$data[4],$data[5]);
    $title = $data[1];
    $info = array();
    $contact = $data[2] . "\n";
    $contact .= $data[3] . "\n";
    $contact .= $data[4] . "\n";
    $contact .= $data[5] . "\n";
    $contact .= "Best contact method: " . $data[6] . "\n";
    $info['Contact info'] = $contact;
    $info['Type'] = 'literary';
    $info['Website'] = $data[7];
    $info['Group'] = $data[8];
    $info['Description'] = $data[9];
    $info['Names of all performers'] = $data[12];
    $info['Over age 21'] = $data[13];
    $info['Image link'] = $data[14];
    $info['How does it infringe'] = $data[15];
    $info['Venue needs'] = $data[16];
    $info['Pre-arranged venue'] = $data[17];
    $info['Other artists you\'d like to perform with'] = $data[18];
    $info['Number of performances'] = $data[19];
    $info['Other infringement projects'] = $data[20];
    $info['Previous infringement festivals'] = $data[32];
    $info['Out of town / housing'] = $data[33];
    $info['How will you help Infringement'] = $data[34];
    $info['What can you provide to help'] = $data[35];
    $info['Any questions'] = $data[41];
    $info['Description for web'] = $data[10] . "\n" . $data[7] . "\n";
    $info['Description for brochure'] = $data[11];
    $availability = array();
    for ($d = 0; $d < 11; $d++)
        $availability[$d] = $data[21+$d];
    $orgfields = array();
    insertProposal($info,$availability,$headers,$data,$orgfields,$proposerid,$festival,$title,$orgcontact,$batchid);
    }

function insertProposal($info,$availability,$headers,$data,$orgfields,$proposerid,$festival,$title,$orgcontact,$batchid)
    {
    $info_ser = serialize($info);
    $availability_ser = serialize($availability);
    $forminfo_ser = serialize(array_combine($headers,$data));
    $orgfields_ser = serialize($orgfields);
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
    echo "createUser($email)<br>\n";
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
