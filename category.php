<?php
$STARTTIME = microtime(TRUE);
require_once 'init.php';
connectDB();
requirePrivilege(array('scheduler','organizer'));
require_once 'util.php';

$id = GETvalue('id',0);

if ($id != 0)
    {
    $stmt = dbPrepare('select name,description from `category` where id=?');
    $stmt->bind_param('i',$id);
    $stmt->execute();
    $stmt->bind_result($name,$description);
    $stmt->fetch();
    $stmt->close();
    $pageTitle = 'category: ' . $name;
    $pageDescription = "<p>$description</p>\n";
    $pageDescription .= "<p>";
    if (hasPrivilege('scheduler'))
        {
        $pageDescription .= "&nbsp;&nbsp;<a href='editCategory.php?id=$id'>[edit category name/description]</a>\n";
        $pageDescription .= "&nbsp;&nbsp;<a href='autoCategory.php?id=$id'>[auto-add to this category]</a>\n";
        }
    $pageDescription .= "</p>\n";
    }
else
    {
    $pageTitle = 'all proposals';
    $pageDescription = '';
    }

bifPageheader($pageTitle);
echo $pageDescription;


$festival = GETvalue('festival',getFestivalID());
if ($id != 0)
    {
    $stmt = dbPrepare('select proposal.id, title from proposal join user on proposerid=user.id join proposalCategory on proposal.id=proposalCategory.proposal_id where proposalCategory.category_id=? and deleted=0 and festival=? order by title');
    $stmt->bind_param('ii',$id,$festival);
    }
else
    {
    $stmt = dbPrepare('select `proposal`.`id`, `title` from `proposal` join `user` on `proposerid`=`user`.`id` where `deleted` = 0 and `festival` = ? order by `title`');
    $stmt->bind_param('i',$festival);
    }

$rows = array();
$stmt->execute();
$stmt->bind_result($id,$title);
while ($stmt->fetch())
    {
    if ($title == '')
        $title = '!!NEEDS A TITLE!!';
    echo "<li><a href='proposal.php?id=$id'>$title</a></li>\n";
    }
$stmt->close();

bifPagefooter();
?>
