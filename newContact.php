<?php
require_once 'init.php';
connectDB();
requirePrivilege('admin');
require_once 'util.php';
require_once 'scheduler.php';

bifPageheader('new contact');
$festival = getFestivalID();
$festivalname = dbQueryByID('select name from festival where id=?',$festival);
echo "<p>Create a contact position for " . $festivalname['name'] . "</p>\n";
?>
<form method="POST" action="api.php">
<input type="hidden" name="command" value="newContact" />
<input type="hidden" name="returnurl" value="index.php" />
<input type="hidden" name="festival" value="<?php echo $festival;?>" />
Userid: <?php echo userMenu('userid',array('admin','scheduler','organizer')); ?>
<br>
Role: <input type="text" name="role" size="60">
<br>
Description: <input type="text" name="description" size="60">
<p>
<input type="submit" value="Submit">
</form>
<?php
bifPagefooter();
?>
