<?php
require_once 'init.php';
connectDB();
requirePrivilege(array('scheduler'));
require_once 'scheduler.php';

if (!isset($_GET['id']))
    die('no batch id given');
else
    $id = $_GET['id'];

$row = dbQueryByID('select name from `batch` where id=?',$id);
bifPageheader('add info field to batch: ' . $row['name']);
echo beginApiCallHtml("batchAddInfoField", array("batchid"=>"$id", "returnurl"=>"batch.php?id=$id"));
?>
Field label: <input type="text" name="fieldname" />
<br>
<input type="submit" name="submit" value="Add field" />
</form>

<?php
bifPagefooter();
?>
