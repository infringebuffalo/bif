<?php
require_once 'init.php';
require_once 'scheduler.php';
require '../bif.php';
connectDB();

bifPageheader('new group show');
?>
<form method="POST" action="api.php">
<input type="hidden" name="command" value="newGroupshow" />
<input type="hidden" name="returnurl" value="index.php" />
Title: <input type="text" name="title" size="60">
<br>
Description: <input type="text" name="description" size="60">
<br>
Batch for performers: <?php echo batchMenu('batch'); ?>
<p>
<input type="submit" value="Submit">
</form>
<?php
bifPagefooter();
?>
