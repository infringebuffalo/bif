<?php
require_once 'init.php';
requireLogin();
connectDB();

if (!isset($_GET['id']))
    die('no proposal selected');
else
    $proposal_id = $_GET['id'];

$stmt = dbPrepare('select `proposerid`,`title` from `proposal` where `proposal`.`id`=?');
$stmt->bind_param('i',$proposal_id);
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

bifPageheader('image upload');
?>
<h2>Upload web icon image for <i><?php echo $title;?></i></h2>

<p>
All show listings on the festival website will include a single image associated with that show.  Please upload your image here.  Note that the image will be scaled to 400 pixels on its largest side.
</p>
<form method="POST" enctype="multipart/form-data" action="imageStore.php">
<input type="hidden" name="proposalid" value="<?php echo $proposal_id ?>" />
<table>
<tr><th>File (JPEGs only):</th><td><input type="file" name="uploadedfile" size="40" /></td>
</table>
<input type="submit" value="Upload">
</form>

<?php
bifPagefooter();
?>
