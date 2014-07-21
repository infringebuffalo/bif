<?php
require_once 'init.php';
connectDB();

bifPageheader('new category');
?>
<form method="POST" action="api.php">
<input type="hidden" name="command" value="newCategory" />
<input type="hidden" name="returnurl" value="listCategories.php" />
Name: <input type="text" name="name" size="20">
<br>
Description: <textarea name="description" rows="3" cols="40"></textarea>
<p>
<input type="submit" value="Create">
</form>
<?php
bifPagefooter();
?>
