<?php
require_once 'init.php';
connectDB();
requirePrivilege('admin','create new festival');

bifPageheader('new festival');
?>
<form method="POST" action="api.php">
<input type="hidden" name="command" value="newFestival" />
<input type="hidden" name="returnurl" value="." />
Name: <input type="text" name="name" size="60">
<br>
Description: <input type="text" name="description" size="60">
<br>
Start date (in ISO format, eg "2016-07-28"): <input type="text" name="startDate" size="20">
<br>
Number of days: <input type="text" name="numberOfDays" size="20" value="11">
<p>
<input type="submit" value="Submit">
</form>
<?php
bifPagefooter();
?>
