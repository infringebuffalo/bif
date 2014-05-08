<?php
require_once 'init.php';
connectDB();
requirePrivilege('scheduler');
require_once 'util.php';

$proposal_id = GETvalue('id',0);
if ($proposal_id == 0)
    {
    $_SESSION['adminmessage'] = 'ERROR: no proposal id given for changeOwner';
    header('location:.');
    die();
    }
$row = dbQueryByID('select title,name,email from proposal join user on proposerid=user.id where proposal.id=?',$proposal_id);

$header = <<<ENDSTRING
<script src="jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
 });
</script>
ENDSTRING;

bifPageheader('change proposal owner',$header);

echo "<p>Change ownership of <a href=\"proposal.php?id=$proposal_id\">$row[title]</a> (current owner is $row[name] - $row[email])</p>\n";
echo "<p>Select new owner:</p>\n";
?>
<table>
<tr><th>name</th><th>e-mail</th></tr>
<?php
$stmt = dbPrepare('select `id`, `name`, `email` from `user` order by `name`');
$stmt->execute();
$stmt->bind_result($id,$name,$email);
while ($stmt->fetch()) 
    {
    if ($name == '')
        $name = '!!NEEDS A NAME!!';
    $linktag =  "<a href='newOwner.php?proposalid=$proposal_id&userid=$id'>";
    echo "<tr><td>$linktag$name</a></td><td>$linktag$email</a></td>";
    }
$stmt->close();
?>
</table>
<?php
bifPagefooter();
?>
