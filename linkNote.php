<?php
require_once 'init.php';
connectDB();
requirePrivilege('scheduler');
require_once 'scheduler.php';
require_once 'util.php';

$header = <<<ENDSTRING
<script src="jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
 });
</script>
ENDSTRING;

bifPageheader('link note',$header);

$id = GETvalue('id');

$note = dbQueryByID('select note,user.name from note join user on creatorid=user.id where note.id=?', $id);
echo "<p><span class='noteauthor'>" . $note['name'] . "</span>: " . $note['note'] . "</p>";

$links = array();
$stmt = dbPrepare('select entity_id,tablename from noteLink join entity on entity_id=entity.id where noteLink.note_id=?');
$stmt->bind_param('i',$id);
$stmt->execute();
$stmt->bind_result($entity,$tablename);
while ($stmt->fetch())
    {
    $links[] = array($entity,$tablename);
    }
$stmt->close();

echo "<p>Currently linked to:</p>\n<ul>\n";
foreach ($links as $link)
    {
    if ($link[1] == 'proposal')
        $info = dbQueryByID('select title as name from proposal where id=?',$link[0]);
    else if ($link[1] == 'venue')
        $info = dbQueryByID('select name from venue where id=?',$link[0]);
    else
        $info = array('name'=>"$link[0]");
    echo "<li>$link[1] <a href='id.php?id=$link[0]'>$info[name]</a></li>\n";
    }
echo "</ul>\n";

echo beginApiCallHtml('linkNote',array('noteid'=>$id));
echo "Link to proposal: ";
echo showMenu('entityid');
echo "<input type='submit' name='submit' value='link' />\n</form>\n";

echo beginApiCallHtml('linkNote',array('noteid'=>$id));
echo "Link to venue: ";
echo venueMenu('entityid');
echo "<input type='submit' name='submit' value='link' />\n</form>\n";

bifPagefooter();
?>
