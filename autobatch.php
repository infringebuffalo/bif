<?php
require_once 'init.php';
connectDB();
require_once 'scheduler.php';

$batchid = GETvalue('id',0);

bifPageheader('autobatch');
?>
<p>
Automatically put proposals in a batch, based on data in the form.
</p>
<form method="POST" action="api.php">
<input type="hidden" name="command" value="autobatch">
Process proposals from batch: <?php echo batchMenu("frombatchid",true); ?>
<br>
Add them to: <?php echo batchMenu("newbatchid",false,$batchid); ?>
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
