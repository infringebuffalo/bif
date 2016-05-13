<?php
require_once 'init.php';
connectDB();
requirePrivilege(array('scheduler'));
require_once 'util.php';
require_once 'scheduler.php';

if (!isset($_GET['id']))
    die('no batch id given');
else
    $id = $_GET['id'];

$row = dbQueryByID('select name from `batch` where id=?',$id);
bifPageheader('change contact for batch: ' . $row['name']);
?>
<p>Select new contact person (scheduler) for this batch:
</p>
<form method="POST" action="api.php">
<input type="hidden" name="command" value="batchChangeContact" />
<input type="hidden" name="returnurl" value="." />
<input type="hidden" name="batchid" value="<?php echo $id; ?>" />
<?php echo userMenu('newcontact','scheduler'); ?>
</select>
<br>
<input type="submit" name="submit" value="Change contact" />
</form>

<?php
bifPagefooter();
?>
