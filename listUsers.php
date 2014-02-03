<?php
require_once 'init.php';
connectDB();
requirePrivilege('scheduler');
require_once 'util.php';
require '../bif.php';

$header = <<<ENDSTRING
<script src="jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
 });
</script>
ENDSTRING;

bifPageheader('all users',$header);
?>
<table>
<tr><th>name</th><th>e-mail</th></tr>
<?php
$amAdmin = hasPrivilege('admin');
$stmt = dbPrepare('select `id`, `name`, `email`, `privs` from `user` order by `name`');
$stmt->execute();
$stmt->bind_result($id,$name,$email,$privs);
while ($stmt->fetch()) 
    {
    if ($name == '')
        $name = '!!NEEDS A NAME!!';
    echo "<tr><td><a href='user.php?id=$id'>$name</a></td><td>$email</td>";
    if ($amAdmin)
        echo "<td>" . str_replace('/',' ',$privs) . "</td></tr>\n";
    }
$stmt->close();
?>
</table>
<?php
bifPagefooter();
?>
