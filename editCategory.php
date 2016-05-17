<?php
require_once 'init.php';
connectDB();
requirePrivilege('scheduler');
require_once 'util.php';


if (!isset($_GET['id']))
    errorAndQuit('editCategory: no category id given');
else
    $id = $_GET['id'];

$row = dbQueryByID('select name,description from `category` where id=?',$id);
$categoryName = $row['name'];
$categoryDescription = $row['description'];

bifPageheader('category: ' . $categoryName);
?>
<div>
<form method="POST" action="api.php">
<input type="hidden" name="command" value="changeCategoryDescription" />
<input type="hidden" name="returnurl" value="category.php?id=<?php echo $id; ?>" />
<input type="hidden" name="id" value="<?php echo $id; ?>" />
Name: <input type="text" name="name" value="<?php echo $categoryName; ?>" />
<br>
Description: <textarea name="description">
<?php echo $categoryDescription; ?>
</textarea>
<br>
<input type="submit" name="submit" value="Save" />
</form>
</div>

<?php
bifPagefooter();
?>
