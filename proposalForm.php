<?php
require_once 'init.php';
connectDB();
requireLogin();
require_once 'util.php';
require_once 'scheduler.php';
getDatabase();

if (!isset($_GET['id']))
    die('no proposal selected');
else
    $proposal_id = $_GET['id'];

$stmt = dbPrepare('select `proposerid`, `title`, `forminfo`, `user`.`name` from `proposal` join `user` on `proposerid`=`user`.`id` where `proposal`.`id`=?');
$stmt->bind_param('i',$proposal_id);
$stmt->execute();
$stmt->bind_result($proposer_id,$title,$forminfo_ser,$proposer_name);
$stmt->fetch();
$stmt->close();
$forminfo = unserialize($forminfo_ser);

if (!hasPrivilege('scheduler'))
    {
    if ($proposer_id != $_SESSION['userid'])
        {
        header('Location: .');
        die();
        }
    }

bifPageheader('proposal: ' . $title);

echo '<table rules="all" cellpadding="3">';

echo "<tr><th>Title</th><td>" . htmlspecialchars($title) . "</td></tr>\n";

echo "<tr><th>Proposer</th><td><a href='user.php?id=$proposer_id'>$proposer_name</a></td></tr>\n";

$i = 1;
foreach ($forminfo as $k=>$v)
    {
    echo "<tr><th>$k</th><td>" . multiline($v) . "</td></tr>\n";
    $i = $i + 1;
    }
echo '</table>';

bifPagefooter();
?>
