<?php
require_once 'init.php';
requireLogin();
connectDB();
require_once 'scheduler.php';

$proposalid=POSTvalue('proposalid',0);
if ($proposalid == 0)
    die('no proposal id given');
$stmt = dbPrepare('select `proposerid`,`title` from `proposal` where `id`=?');
$stmt->bind_param('i',$proposalid);
$stmt->execute();
$stmt->bind_result($proposer_id,$title);
if (!$stmt->fetch())
    {
    $stmt->close();
    die('no such proposal');
    }
$stmt->close();

if (!hasPrivilege('scheduler'))
    {
    if ($proposer_id != $_SESSION['userid'])
        {
        header('Location: .');
        die();
        }
    }

if ($_FILES)
    {
    $f = $_FILES['uploadedfile'];
    if (($f['type'] == 'image/jpeg') || ($f['type'] == 'image/pjpeg'))
      {
      if ($f['size'] > 10000000)
         $error = 'Uploaded file must be 10 megabytes or less.  Please scale your image down to 300 pixels across before uploading.';
      else
        {
        $imageid = newEntityID('image');
        $filename = 'uploads/file' . $imageid . '.jpg';
        exec('convert ' . $f['tmp_name'] . ' -thumbnail 300x300 -unsharp 0x.5 ' . $filename);
        $origFilename = $db->real_escape_string(htmlentities($f['name']));
        $description = "image for show $proposalid ('$title')";
        $stmt = dbPrepare('insert into image (id,filename,origFilename,description) values (?,?,?,?)');
        $stmt->bind_param('isss',$imageid,$filename,$origFilename,$description);
        $stmt->execute();
        $stmt->close();
        setProposalInfo($proposalid, 'icon', $imageid);
        log_message("saved icon $imageid for proposal $proposalid");
        header('Location: proposal.php?id=' . $proposalid);
        die();
        }
      }
    else if ($f['type'] == '')
      $error = 'Your browser did not tell me what type of file this is - please try resaving the image as a JPEG';
    else
      {
      $error = 'Only jpeg images please - according to the info from your browser, the file you tried to upload is '.$f['type'];
      }
    }
 else
    $error = 'Nothing uploaded';

log_message('iconstore failed - error="' . $error . '"');
bifPageheader('image upload error');
?>
<p>
<b>IMAGE UPLOAD ERROR</b>
</p>
<p>
<?php echo $error; ?>
</p>
<p>
<a href="proposal.php?id=<?php echo $proposalid ?>">return to proposal</a>
</p>
<?php
bifPagefooter();
?>
