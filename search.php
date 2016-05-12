<?php
require_once 'init.php';
require_once 'util.php';
connectDB();
requirePrivilege(array('admin','scheduler','organizer'),"search proposals");

bifPageheader('search proposals');
$target = POSTvalue('target');
?>
<form method="POST" action="search.php">
Text to search for: <input type="text" name="target" size="20" value="<?php echo $target; ?>">
<input type="submit" name="submit" value="search">
</form>
<ul>
<?php
if ($target != '')
    {
    $festival = getFestivalID();
    $stmt = dbPrepare('select id, title, info_json, deleted from proposal where festival=?');
    $stmt->bind_param('i',$festival);
    $stmt->execute();
    $stmt->bind_result($id,$title,$info_json,$deleted);
    $output = "";
    while ($stmt->fetch())
        {
        if ($title == '')
            $title = '!!NEEDS A TITLE!!';
        if (proposalMatches($target, $title, $info_json))
            {
            $output .= "<li>";
            if ($deleted)
                $output .= "[DELETED] ";
            $output .= "<a href='proposal.php?id=$id'>$title</a>";
            $output .= "</li>\n";
            }
        }
    $stmt->close();
    echo $output;
    }

function proposalMatches($target, $title, $info_json)
    {
    if (stripos($title,$target) !== FALSE)
        return TRUE;
    $info = json_decode($info_json,true);
    if (is_array($info))
        {
        foreach ($info as $i)
            {
            if ((array_key_exists(1,$i)) && (stripos($i[1],$target) !== FALSE))
                return TRUE;
            }
        }
    return FALSE;
    }
?>
</ul>
<?php
bifPagefooter();
?>

</body>
</html>
