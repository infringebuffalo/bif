<?php
require_once 'init.php';
connectDB();
requirePrivilege('scheduler');
require_once 'util.php';

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
$festival = getFestivalID();
$stmt = dbPrepare('select `id`, `name`, `email`, `privs_json` from `user` order by `name`');
$stmt->execute();
$stmt->bind_result($id,$name,$email,$privs_json);
while ($stmt->fetch()) 
    {
    if ($name == '')
        $name = '!!NEEDS A NAME!!';
    echo "<tr><td><a href='user.php?id=$id'>$name</a></td><td>$email</td>";
    if ($amAdmin)
        {
        $userPrivs = json_decode($privs_json,true);
        if (is_array($userPrivs))
            {
            echo "<td>";
            if (array_key_exists(0, $userPrivs))
                foreach ($userPrivs[0] as $priv)
                    echo "[$priv] ";
            if (array_key_exists($festival, $userPrivs))
                foreach ($userPrivs[$festival] as $priv)
                    echo "$priv ";
            echo "</td></tr>\n";
            }
        }
    }
$stmt->close();
?>
</table>
<?php
bifPagefooter();
?>
