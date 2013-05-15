<?php
require_once 'init.php';
require '../bif.php';
connectDB();

bifPageheader('new venue');
?>
<form method="POST" action="api.php">
<input type="hidden" name="command" value="newVenue" />
<input type="hidden" name="returnurl" value="listVenues.php" />
Name: <input type="text" name="name" size="60">
<br>
Short name: <input type="text" name="shortname" size="60">
<p>
<input type="submit" value="Submit">
</form>
<?php
bifPagefooter();
?>
