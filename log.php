<?php
require_once 'init.php';
connectDB();
requirePrivilege('admin');
require '../bif.php';

bifPageheader('log');
?>
<table>
<?php
$stmt = dbPrepare("select time,userid,ip,proposal,is_sql,message,email,title from log left join user on userid=user.id left join proposal on log.proposal=proposal.id order by log.id desc limit 500");
$stmt->execute();
$stmt->bind_result($time,$userid,$ip,$proposal,$is_sql,$message,$username,$title);
$lines = array();
while ($stmt->fetch())
    {
    $s = "<tr>\n";
    $s .= '<td>' . $time . "</td>\n";
    $s .= '<td><a href="user.php?id=' . $userid . '">' . $username . "</a></td>\n";
    $s .= '<td>' . $ip . "</td>\n";
    $s .= '<td><a href="proposal.php?id=' . $proposal . '">' . $title . "</a></td>\n";
    $s .= '<td>' . $message . "</td>\n";
    $s .= "</tr>\n";
    $lines[] = $s;
    }
$stmt->close();
echo implode(array_reverse($lines));
?>
</table>
<?php
bifPagefooter();
?>

</body>
</html>
