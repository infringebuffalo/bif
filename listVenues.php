<?php
require_once 'init.php';
connectDB();
requirePrivilege('scheduler');
require_once 'util.php';
require '../bif.php';

$header = <<<ENDSTRING
<script src="jquery-1.9.1.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
 });
</script>
ENDSTRING;

bifPageheader('all venues',$header);
?>
<table>
<?php
$stmt = dbPrepare('select `id`, `name` from `venue` where `festival` = ?');
$stmt->bind_param('i',getFestivalID());
$stmt->execute();
$stmt->bind_result($id,$name);
while ($stmt->fetch()) 
    {
    if ($name == '')
        $name = '!!NEEDS A NAME!!';
    echo "<tr><td><a href='venue.php?id=$id'>$name</a></td></tr>\n";
    }
$stmt->close();
?>
</table>
<?php
bifPagefooter();
?>
