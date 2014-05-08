<?php
require_once 'init.php';
connectDB();
requirePrivilege(array('scheduler'));
require_once 'util.php';

if (!isset($_GET['id']))
    die('no batch id given');
else
    $id = $_GET['id'];

$row = dbQueryByID('select name from `batch` where id=?',$id);
bifPageheader('change contact for batch: ' . $row['name']);
?>
<p>Select new contact person for this batch:
<br>(note: only people with public contact info are listed)
</p>
<form method="POST" action="api.php">
<input type="hidden" name="command" value="batchChangeContact" />
<input type="hidden" name="returnurl" value="." />
<input type="hidden" name="batchid" value="<?php echo $id; ?>" />
<select name="newcontact">
<?php
$stmt = dbPrepare('select user.id,name,card.email from user join card where card.userid = user.id order by name');
if (!$stmt->execute())
    die($stmt->error);
$stmt->bind_result($contactid,$name,$email);
while ($stmt->fetch())
    echo "<option value=\"$contactid\">$name ($email)</option>\n";
$stmt->close();
?>
</select>
<br>
<input type="submit" name="submit" value="Change contact" />
</form>

<?php
bifPagefooter();
?>
