<?php
require_once 'init.php';
require '../bif.php';
connectDB();

bifPageheader('new summary field');
?>
<form method="POST" action="makeOrgField.php">
Proposal type: <select name="type">
<option value="music">music</option>
<option value="dance">dance</option>
<option value="theatre">theatre</option>
<option value="film">film</option>
<option value="visualart">visualart</option>
<option value="literary">literary</option>
</select>
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
