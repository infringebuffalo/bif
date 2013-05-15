<div><?php
 if ((array_key_exists('loginError',$_SESSION)) && ($_SESSION['loginError'] != ''))
    {
    echo '<div style="float:right; background:#ff8080">' . $_SESSION['loginError'] . '</div>';
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
</div>

<!--
<p></p>
<div>
If you do not already have a login, please <a href="register.php">register here</a>.
</div>

<p><br/></p>
<div>
<font size=-1>
<form method="post" action="forgotpassword.php">
<table cellspacing="1">
<tr>
<td colspan="2">Forgot your password?</td>
</tr>
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
</font>
<p>
<br/><br/>
<em>If you encounter any problems with the proposal forms or logins, contact Dave Pape [depape@buffalo.edu]</em>
</p>
</div>
-->
