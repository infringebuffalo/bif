<?php
$STARTTIME = microtime(TRUE);
require_once 'init.php';
connectDB();
requirePrivilege(array('scheduler','organizer'));
require_once 'util.php';
require_once 'scheduler.php';

$batchid = GETvalue('id',0);

$header = <<<ENDSTRING
<script src="jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
function ajaxReturn(data)
    {
    window.location.reload();
    }

function ajaxUpload(id)
    {
    var f = document.forms["upload"+id];
    var url = f["url"].value;
    $.post("api.php", {command:"getIconFromURL", proposal:id, url:url}, ajaxReturn);
    return false;
    }

$(document).ready(function() {
 });
</script>
ENDSTRING;

bifPageheader("icons for proposals",$header);
?>
<table class="colorized">
<thead>
<tr>
<th style="width:10em">show</th>
<th>upload</th>
<th>icon</th>
<th>image link</th>
</tr>
</thead>
<tbody>
<?php
if ($batchid != 0)
    {
    $stmt = dbPrepare('select proposal.id, title, info from proposal join proposalBatch on proposal.id=proposalBatch.proposal_id where proposalBatch.batch_id=? and deleted=0 order by title');
    $stmt->bind_param('i',$batchid);
    }
else
    {
    $festival = GETvalue('festival',getFestivalID());
    $stmt = dbPrepare('select proposal.id, title, info from proposal where deleted=0 and festival=? order by title');
    $stmt->bind_param('i',$festival);
    }

$stmt->execute();
$stmt->bind_result($id,$title,$info_ser);
while ($stmt->fetch())
    {
    if ($title == '')
        $title = '!!NEEDS A TITLE!!';
    $info = unserialize($info_ser);
    $icon = getInfo($info,'icon');
    $iconlink = '';
    if ($icon != '')
        {
        $iconurl = "uploads/file$icon.jpg";
        $iconlink = "<img src='$iconurl' width='100'><br>$iconurl";
        }
    $imageurl = getInfo($info,'image link');
    $imagelink = '';
    if ($imageurl != '')
        {
        $imageurl = completeURL($imageurl);
        $imagelink = "<a href='$imageurl'>$imageurl</a>";
        }
    echo "<tr>\n<td><a href='proposal.php?id=$id'>$title</a></td>\n";
    echo "<td>" . beginApiCallHtml('getIconFromURL',array('proposal'=>$id),false,"upload$id") . "<input type='text' name='url' value='$imageurl'>\n<input type='button' id='submit' value='Upload' onclick='return ajaxUpload($id)'/>\n</form>\n";
    echo "</td>\n";
    echo "<td><p>$iconlink</p></td>\n<td>$imagelink</td>\n";
    echo "</tr>\n";
    }
$stmt->close();
?>
</tbody>
</table>

<?php
bifPagefooter();
?>
