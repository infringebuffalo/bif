<?php
require_once 'init.php';
require_once 'util.php';
require '../bif.php';
connectDB();

$header = <<<ENDSTRING
<script src="jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
function toggleElement(name)
    {
    $('#' + name).toggle()
    }

$(document).ready(function() {
 });
</script>
<link rel="stylesheet" href="style.css" type="text/css" />
ENDSTRING;

bifPageheader('proposals database login',$header);

if ((array_key_exists('loginError',$_SESSION)) && ($_SESSION['loginError'] != ''))
    {
    echo '<div style="background:#ff8080">' . $_SESSION['loginError'] . '</div>';
    unset($_SESSION['loginError']);
    }
?>
<form method="post" action="login.php">
<table cellspacing="1">
<tr>
<td colspan="2">Log in to edit your proposal:<br/>
(All logins from past years should still work)</td>
</tr>
<tr>
<td>E-mail address</td>
<td><input name="username" type="text" id="username" /></td>
</tr>
<tr>
<td>Password</td>
<td><input name="password" type="password" id="password" /></td>
</tr>
<tr>
<td colspan="2"><input type="submit" name="login" value="Log in" />
</td>
</tr>
</table>
</form>

<p><br/></p>
<a id="forgotPasswordLabel" onclick="toggleElement('forgotPasswordForm');">
Forgot password &gt;&gt;&gt;
</a>
<div id="forgotPasswordForm">
<form method="post" action="forgotpassword.php">
<table cellspacing="1">
<tr>
<td>E-mail address</td>
<td><input name="username" type="text" id="username" /></td>
</tr>
<tr>
<td colspan="2"><input type="submit" name="login" value="Send me a new password" />
</td>
</tr>
</table>
</form>
</div>

<p><br></p>
<?php
if ((array_key_exists('createaccountError',$_SESSION)) && ($_SESSION['createaccountError'] != ''))
    {
    echo '<div style="background:#ff8080">' . $_SESSION['createaccountError'] . '</div>';
    unset($_SESSION['createaccountError']);
    }
?>
<a id="newLoginLabel" onclick="toggleElement('newLoginForm');">
Create a new login &gt;&gt;&gt;
</a>
<div id="newLoginForm">
<form method="post" action="createaccount.php">
<table cellspacing="1">
<tr>
<td>E-mail address</td>
<td><input name="username" type="text" id="c_username" /></td>
</tr>
<tr>
<td>Password</td>
<td><input name="password" type="password" id="c_password" /></td>
</tr>
<tr>
<td>Confirm password</td>
<td><input name="passwordconfirm" type="password" id="c_passwordconfirm" /></td>
</tr>
<tr>
<td colspan="2">&nbsp;&nbsp;&nbsp;Contact info</td>
</tr>
<tr>
<td>Name</td>
<td><input name="name" type="text" id="c_name" /></td>
</tr>
<tr>
<td>Phone #</td>
<td><input name="phone" type="text" id="c_phone" /></td>
</tr>
<tr>
<td>Mailing address</td>
<td><textarea name="snailmail" rows="3" cols="40" id="c_snailmail"></textarea></td>
</tr>
<tr>
<td><input type="submit" name="create" value="Create account" /></td>
</tr>
</table>
</form>
</div>

<p>
<br/><br/>
<em>If you encounter any problems with the proposal forms or logins, contact Dave Pape [depape@buffalo.edu]</em>
</p>

<?php
bifPagefooter();
?>
