<?php
require_once 'init.php';
require_once 'util.php';
requireLogin();
connectDB();
bifPageHeader("edit contact info");

$data = dbQueryByID('select name,phone,snailmail from user where id=?',$_SESSION['userid']);
?>
<div class="contact">
<form method="POST" action="api.php">
<input type="hidden" name="command" value="updateContact" />
<input type="hidden" name="returnurl" value="." />
<input type="hidden" name="id" value="<?php echo $_SESSION['userid'] ?>" />
<table cellpadding="3">
<tr>
<th>Name</th>
<td><input type="text" name="name" value="<?php echo $data['name'] ?>" /></td>
</tr>
<tr>
<th>Phone</th>
<td><input type="text" name="phone" value="<?php echo $data['phone'] ?>" /></td>
</tr>
<tr>
<th>Mailing address</th>
<td><textarea name="snailmail" rows="3" cols="40"><?php echo $data['snailmail'] ?></textarea></td>
</tr>
</table>
<input type="submit" name="submit" value="Save" />
</form>
</div>

<p>
Note: Because your e-mail address is also your login name, it cannot be changed through this form.  If you really <em>need</em> to change your e-mail address, contact Dave Pape (depape@buffalo.edu).
</p>

<?php
bifPageFooter();
?>
