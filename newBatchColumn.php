<?php
require_once 'init.php';
connectDB();
require_once 'scheduler.php';

$batchid = GETvalue('id',0);

bifPageheader('new batch column');
?>
<p>
Add a new column that will be displayed on the batch page to all proposals.
</p>
<p>
To create something using data from the original forms, enter an appropriate part of the label from a field (for example "over 21") in the "Proposal field label..." box.  The value from that field will be used; if no field matches that label, the value will be the "Default value".
</p>
<p>
To create just a new column filled with the default value, leave the "Proposal field label..." line blank.
</p>
<p>
If a column with the given name already exists, it will be replaced by the new information.
</p>
<form method="POST" action="api.php">
<input type="hidden" name="command" value="newBatchColumn">
Name for new column: <input type="text" name="columnname" size="20">
<br>
Proposal field label contains the string: <input type="text" name="fieldlabel" size="20">
<br>
Default value: <input type="text" name="defaultvalue" size="20">
<br>
Apply to batch: <?php echo batchMenu("batchid",true,$batchid); ?>
<p>
<input type="submit" value="Create">
</form>
<?php
bifPagefooter();
?>
