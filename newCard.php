<?php
require_once 'init.php';
connectDB();

bifPageheader('new card');
?>
<form method="POST" action="api.php">
<input type="hidden" name="command" value="newCard" />
<input type="hidden" name="returnurl" value="index.php" />
Userid: <input type="text" name="userid" size="60">
<br>
Role: <input type="text" name="role" size="60">
<br>
E-mail: <input type="text" name="email" size="60">
<br>
Phone: <input type="text" name="phone" size="60">
<br>
Snailmail: <input type="text" name="snailmail" size="60">
<p>
<input type="submit" value="Submit">
</form>
<?php
bifPagefooter();
?>
