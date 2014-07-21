<?php
require_once 'init.php';
connectDB();

bifPageheader('new batch');
?>
<p>
This will create a new batch for grouping shows for internal scheduling purposes.  Batches are only visible to schedulers, not to artists and not on the public website.
</p>
<form method="POST" action="api.php">
<input type="hidden" name="command" value="newBatch" />
<input type="hidden" name="returnurl" value="listBatches.php" />
Name: <input type="text" name="name" size="60">
<br>
Description: <input type="text" name="description" size="60">
<p>
<input type="submit" value="Create">
</form>
<?php
bifPagefooter();
?>
