<?php
require_once 'init.php';
connectDB();
requirePrivilege('scheduler');
require_once 'util.php';
require '../bif.php';


if (!isset($_GET['id']))
    die('no batch id given');
else
    $id = $_GET['id'];

$header = <<<ENDSTRING
<script src="jquery-1.9.1.min.js" type="text/javascript"></script>
<script type="text/javascript">
function showClass(name)
    {
    $('.allproposals').hide()
    $('.'+name).show()
    }
 
$(document).ready(function() {
 });
</script>
<link rel="stylesheet" href="style.css" type="text/css" />
ENDSTRING;


$row = dbQueryByID('select name,description from `batch` where id=?',$id);
$batchName = $row['name'];
$batchDescription = $row['description'];


$proposal = array();
$stmt = dbPrepare('select proposal.id, title from proposal where deleted=0 order by title');
$stmt->execute();
$stmt->bind_result($proposalid,$title);
echo "<table>\n";
while ($stmt->fetch())
    {
    if ($title == '')
        $title = '!!NEEDS A TITLE!!';
    $proposal[$proposalid] = array('id'=>$proposalid, 'title'=>substr($title,0,32), 'class'=>'allproposals','checked'=>0);
    }
$stmt->close();

$batch = array(array('allproposals','&lt;all&gt;'));
$stmt = dbPrepare('select id,name from batch where festival=?');
$festival = getFestivalID();
$stmt->bind_param('i',$festival);
$stmt->execute();
$stmt->bind_result($batchid,$batchname);
while ($stmt->fetch())
    $batch[] = array("batch$batchid",$batchname);
$stmt->close();

$stmt = dbPrepare('select proposal_id,batch_id from proposalBatch');
$stmt->execute();
$stmt->bind_result($proposal_id,$batch_id);
while ($stmt->fetch())
    {
    if (array_key_exists($proposal_id,$proposal))
        {
        $proposal[$proposal_id]['class'] .= " batch$batch_id";
        if ($batch_id == $id)
            $proposal[$proposal_id]['checked'] = 1;
        }
    }
$stmt->close();

bifPageheader('batch: ' . $batchName,$header);
?>
<div>
<form method="POST" action="api.php">
<input type="hidden" name="command" value="changeBatchDescription" />
<input type="hidden" name="returnurl" value="batch.php?id=<?php echo $id; ?>" />
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<textarea name="description">
<?php echo $batchDescription; ?>
</textarea>
<input type="submit" name="submit" value="Update description" />
</form>
</div>

<h2>Change contents</h2>
Show batch <select onchange="showClass(this.options[selectedIndex].value)">
<?php
foreach ($batch as $b)
    echo "<option value='$b[0]'>$b[1]</option>\n";
?>
</select>
<form method="POST" action="api.php">
<input type="hidden" name="command" value="changeBatchMembers" />
<input type="hidden" name="returnurl" value="batch.php?id=<?php echo $id; ?>" />
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<table>
<?php
foreach ($proposal as $p)
    {
    echo "<tr class='$p[class]'><td><a href='proposal.php?id=$p[id]'>$p[title]</a></td><td><input type='checkbox' name='proposal[]' value='$p[id]'";
    if ($p['checked'])
        echo " checked";
    echo " /></td></tr>\n";
    }
?>
</table>
<input type="submit" name="submit" value="Update" />
</form>

<?php
bifPagefooter();
?>
