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
Proposal field label contains the string: <input type="text" name="fieldlabel" size="20">
<br>
Value: <input type="text" name="value" size="20">
<p>
<input type="submit" value="Do it">
</form>
<?php
bifPagefooter();
?>
