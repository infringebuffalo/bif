<?php
require_once 'init.php';
require '../bif.php';
connectDB();
requirePrivilege(array('scheduler','organizer'));

bifPageheader('add person to mailing list');
?>
<form method="POST" action="api.php">
<input type="hidden" name="command" value="subscribe" />
<input type="hidden" name="returnurl" value="." />
Address: <input type="text" name="address" size="60">
<br>
List:&nbsp;<select name="mailinglist">
<?php
#$lists = array('org', 'shows', 'dance', 'film', 'lit', 'music', 'theatre', 'visualart');
$lists = array('org');
foreach ($lists as $list)
    echo "<option value=\"bif14-$list\">bif14-$list</option>\n";
?>
</select>
<p>
<input type="submit" value="Subscribe">
</form>
<?php
bifPagefooter();
?>
