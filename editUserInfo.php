<?php
require_once 'init.php';
require_once 'util.php';
requireLogin();
connectDB();
bifPageheader("edit user info");

$id = GETvalue("id",$_SESSION['userid']);

if (!hasPrivilege(array('scheduler','admin')) && ($id != $_SESSION['userid']))
    {
    log_message("tried to access editUserInfo.php for user {ID:$id}");
    header('Location: .');
    }
$data = dbQueryByID('select name,phone,snailmail from user where id=?',$id);
?>
<div class="contact">
<form method="POST" action="api.php">
<input type="hidden" name="command" value="updateUserInfo" />
<input type="hidden" name="returnurl" value="user.php?id=<?php echo $id; ?>" />
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<table cellpadding="3">
<tr>
<th>Name</th>
<td><input type="text" name="name" value="<?php echo htmlentities($data['name'],ENT_COMPAT | ENT_HTML5, "UTF-8") ?>" /></td>
</tr>
<tr>
<th>Phone</th>
<td><input type="text" name="phone" value="<?php echo htmlentities($data['phone'],ENT_COMPAT | ENT_HTML5, "UTF-8") ?>" /></td>
</tr>
<tr>
<th>Mailing address</th>
<td><textarea name="snailmail" rows="3" cols="40"><?php echo str_replace('&NewLine;','',htmlentities($data['snailmail'],ENT_COMPAT | ENT_HTML5, "UTF-8")); ?></textarea></td>
</tr>
</table>
<input type="submit" name="submit" value="Save" />
</form>
</div>

<p>
Note: Because your e-mail address is also your login name, it cannot be changed through this form.  If you really <em>need</em> to change your e-mail address, contact Dave Pape (depape@buffalo.edu).
</p>

<?php
bifPagefooter();
?>
