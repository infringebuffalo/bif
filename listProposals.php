<?php
require_once 'init.php';
connectDB();
requirePrivilege('scheduler');
require '../bif.php';

$header = <<<ENDSTRING
<script src="jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
 });
</script>
ENDSTRING;

bifPageheader('all proposals',$header);
?>
<table>
<tr><th>title</th><th>proposer</th></tr>
<?php
$stmt = dbPrepare('select `proposal`.`id`, `proposerid`, `name`, `title`, `orgfields` from `proposal` join `user` on `proposerid`=`user`.`id` where `deleted` = 0 order by `title`');
$stmt->execute();
$stmt->bind_result($id,$proposer_id,$proposer_name,$title,$orgfields_ser);
while ($stmt->fetch()) 
    {
    if ($title == '')
        $title = '!!NEEDS A TITLE!!';
    $orgfields = unserialize($orgfields_ser);
    echo "<tr><td><a href='proposal.php?id=$id'>$title</a></td><td><a href='user.php?id=$proposer_id'>$proposer_name</a></td></tr>\n";
    }
$stmt->close();
?>
</table>
<?php
bifPagefooter();
?>
