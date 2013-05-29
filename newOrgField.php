<?php
require_once 'init.php';
require '../bif.php';
connectDB();

bifPageheader('new summary field');
?>
<form method="POST" action="makeOrgField.php">
Proposal type: <input type="text" name="type" size="20">
<br>
Field number: <input type="text" name="field" size="20">
<br>
Label: <input type="text" name="label" size="20">
<p>
<input type="submit" value="Create">
</form>
<?php
bifPagefooter();
?>
