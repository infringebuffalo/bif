<?php
require_once 'init.php';
connectDB();
requirePrivilege('admin');

bifPageheader('upload venue info spreadsheet');
?>
<form method="POST" enctype="multipart/form-data" action="venuesCsvProcess.php">
CSV file: <input type="file" name="spreadsheet" size="60">
<br>
ID is field: <input type="text" name="idfield" value="A">
<br>
Name is field: <input type="text" name="namefield" value="B">
<br>
Short name is field: <input type="text" name="shortnamefield" value="C">
<p>
<input type="submit" value="Send">
</form>
<?php
bifPagefooter();
?>
