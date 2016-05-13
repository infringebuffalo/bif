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
$data = dbQueryByID('select name,email,phone,snailmail,preferences_json from user where id=?',$id);
$prefs = json_decode($data['preferences_json'],true);
$namePublic = FALSE;
$emailPublic = FALSE;
$phonePublic = FALSE;
$snailmailPublic = FALSE;
if (array_key_exists('public',$prefs))
    {
    $namePublic = $prefs['public']['name'];
    $emailPublic = $prefs['public']['email'];
    $phonePublic = $prefs['public']['phone'];
    $snailmailPublic = $prefs['public']['snailmail'];
    }
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
<td><input type='checkbox' name='namePublic' value='1' <?php if ($namePublic) echo "checked"; ?> /> publicly visible</td>
</tr>
<tr>
<th>E-mail</th>
<td><?php echo htmlentities($data['email'],ENT_COMPAT | ENT_HTML5, "UTF-8"); ?></td>
<td><input type='checkbox' name='emailPublic' value='1' <?php if ($emailPublic) echo "checked"; ?> /> publicly visible</td>
</tr>
<tr>
<th>Phone</th>
<td><input type="text" name="phone" value="<?php echo htmlentities($data['phone'],ENT_COMPAT | ENT_HTML5, "UTF-8") ?>" /></td>
<td><input type='checkbox' name='phonePublic' value='1' <?php if ($phonePublic) echo "checked"; ?> /> publicly visible</td>
</tr>
<tr>
<th>Mailing address</th>
<td><textarea name="snailmail" rows="3" cols="40"><?php echo str_replace('&NewLine;','',htmlentities($data['snailmail'],ENT_COMPAT | ENT_HTML5, "UTF-8")); ?></textarea></td>
<td><input type='checkbox' name='snailmailPublic' value='1' <?php if ($snailmailPublic) echo "checked"; ?> /> publicly visible</td>
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
