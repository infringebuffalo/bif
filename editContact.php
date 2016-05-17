<?php
require_once 'init.php';
connectDB();
requirePrivilege(array('scheduler','admin'));
require_once 'util.php';
require_once 'scheduler.php';

$id = REQUESTvalue('id',0);
if ($id == 0)
    errorAndQuit('No contact id given');

bifPageheader('edit festival contact');

$data = dbQueryByID('select name,userid,role,description from contact join user on contact.userid=user.id where contact.id=?',$id);
$name = htmlentities($data['name'],ENT_COMPAT | ENT_HTML5, "UTF-8");
$role = htmlentities($data['role'],ENT_COMPAT | ENT_HTML5, "UTF-8");
$description = htmlentities($data['description'],ENT_COMPAT | ENT_HTML5, "UTF-8");
?>
<form method="POST" action="api.php">
<input type="hidden" name="command" value="updateFestivalContact" />
<input type="hidden" name="returnurl" value="index.php" />
<input type="hidden" name="id" value="<?php echo $id;?>" />
Name: <?php echo $name; ?>
<br>
Role: <input type="text" name="role" size="60" value="<?php echo $role; ?>">
<br>
Description: <input type="text" name="description" size="60" value="<?php echo $description; ?>">
<p>
<input type="submit" value="Submit">
</form>
<p>
OR
</p>
<form method="POST" action="api.php" onsubmit="return confirm('Really delete it?');">
<input type="hidden" name="command" value="deleteFestivalContact" />
<input type="hidden" name="returnurl" value="index.php" />
<input type="hidden" name="id" value="<?php echo $id;?>" />
<input type="submit" value="Delete contact">
</form>

<?php
bifPagefooter();
?>
