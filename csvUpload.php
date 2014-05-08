<?php
require_once 'init.php';
connectDB();

bifPageheader('upload spreadsheet');
?>
<form method="POST" enctype="multipart/form-data" action="csvProcess.php">
CSV file: <input type="file" name="spreadsheet" size="60">
<p>
Type: <select name="formtype">
<option value="music">music</option>
<option value="dance">dance</option>
<option value="theatre">theatre</option>
<option value="film">film</option>
<option value="visualart">visualart</option>
<option value="literary">literary</option>
</select>
<p>
<input type="submit" value="Send">
</form>
<?php
bifPagefooter();
?>
