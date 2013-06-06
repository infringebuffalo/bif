<?php
require_once 'init.php';
require '../bif.php';
connectDB();

bifPageheader('new summary field');
?>
<p>Add a new 'summary field' (displayed on the batch page) to all proposals of a certain type.</p>
<p>To create just a new field filled with the default value, enter 0 for the "field number".</p>
<p>To create something using data from the original forms, enter the "Field number" of the particular
form field to take the data from.  The numbers are listed on the "[original form]" view of a proposal.  For example, from <a href="http://bif/db2/proposalForm.php?id=1038">this proposal</a> one can find that the "Main Genre" on the music form is field # 17. Be aware that this doesn't work 100% perfectly - the spreadsheets from google for a couple proposals apparently have their fields messed up, so you should double-check the results after using this script. </p>
<p>If a summary field with the given name already exists, it will be replaced by the new information.</p>
<form method="POST" action="makeOrgField.php">
Name for new field: <input type="text" name="label" size="20">
<br>
Proposal type: <select name="type">
<option value="music">music</option>
<option value="dance">dance</option>
<option value="theatre">theatre</option>
<option value="film">film</option>
<option value="visualart">visualart</option>
<option value="literary">literary</option>
</select>
<br>
Field number in original proposals: <input type="text" name="field" size="20">
<br>
or
<br>
Default value: <input type="text" name="default" size="20">
<p>
<input type="submit" value="Create">
</form>
<?php
bifPagefooter();
?>
