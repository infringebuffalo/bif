<?php
require_once 'init.php';
require_once 'util.php';
connectDB();
requirePrivilege('admin',"view log");

bifPageheader('log');
?>
<form method="POST" action="log.php">
User (e-mail): <input type="text" name="user" size="20" value="">
IP address: <input type="text" name="ip" size="20" value="">
Text: <input type="text" name="text" size="20" value="">
Max # of entries: <input type="text" name="limit" size="4" value="500">
<input type="submit" name="submit" value="search">
</form>
<table>
<?php
$limit = POSTvalue('limit',500);
$user = POSTvalue('user');
$ip = POSTvalue('ip');
$messagetext = '%' . POSTvalue('text') . '%';
if (($user == '') && ($ip == ''))
    {
    $stmt = dbPrepare("select time,userid,ip,proposal,is_sql,message,email,title from log left join user on userid=user.id left join proposal on log.proposal=proposal.id where message like ? order by log.id desc limit ?");
    $stmt->bind_param('si',$messagetext,$limit);
    }
else if ($ip == '')
    {
    $userid = getUserID($user);
    $stmt = dbPrepare("select time,userid,ip,proposal,is_sql,message,email,title from log left join user on userid=user.id left join proposal on log.proposal=proposal.id where message like ? and userid=? order by log.id desc limit ?");
    $stmt->bind_param('sii',$messagetext,$userid,$limit);
    }
else if ($user == '')
    {
    $stmt = dbPrepare("select time,userid,ip,proposal,is_sql,message,email,title from log left join user on userid=user.id left join proposal on log.proposal=proposal.id where message like ? and ip=? order by log.id desc limit ?");
    $stmt->bind_param('ssi',$messagetext,$ip,$limit);
    }
else
    {
    $userid = getUserID($user);
    $stmt = dbPrepare("select time,userid,ip,proposal,is_sql,message,email,title from log left join user on userid=user.id left join proposal on log.proposal=proposal.id where message like ? and userid=? and ip=? order by log.id desc limit ?");
    $stmt->bind_param('sisi',$messagetext,$userid,$ip,$limit);
    }
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
    $message = preg_replace('/\{ID:(.*)\}/U','<a href="id.php?id=$1">$1</a>',$message);
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
