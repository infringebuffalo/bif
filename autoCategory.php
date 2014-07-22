<?php
require_once 'init.php';
connectDB();
require_once 'scheduler.php';

$categoryid = GETvalue('id',0);

bifPageheader('autocategory');
?>
<p>
Automatically put proposals in a category, based on data in the form.
</p>
<form method="POST" action="api.php">
<input type="hidden" name="command" value="autoCategory">
Process proposals from batch: <?php echo batchMenu("frombatchid",true); ?>
<br>
Add them to category: <?php echo categoryMenu("newcategoryid",$categoryid); ?>
<br>
<input type='checkbox' name='addall' value='1'>add everything from batch to category
<br>
OR
<br>
Proposal field label: <input type="text" name="fieldlabel" size="20">
<input type='checkbox' name='exactlabel' value='1'>exact match
<br>
Value: <input type="text" name="value" size="20">
<input type='checkbox' name='exactvalue' value='1'>exact match
<p>
<input type="submit" value="Do it">
</form>
<?php
bifPagefooter();
?>
