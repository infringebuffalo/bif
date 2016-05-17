<?php
require_once 'init.php';
connectDB();
requireLogin();
//requirePrivilege(array('scheduler','organizer'));
require_once 'util.php';

$user_id = GETvalue('id',$_SESSION['userid']);

$row = dbQueryByID('select name,email,phone,snailmail,preferences_json from user where id=?',$user_id);
$prefs = json_decode($row['preferences_json'],true);
$canViewAll = ((hasPrivilege(array('scheduler','admin'))) || ($user_id == $_SESSION['userid']));

if ($canViewAll)
    bifPageheader('user: ' . $row['name']);
else
    bifPageheader('user info');

if (hasPrivilege('admin'))
    showPrivilegeButtons($user_id);

echo "<table>\n";
showField('Name','name',$row,$canViewAll,$prefs);
showField('E-mail','email',$row,$canViewAll,$prefs);
showField('Phone','phone',$row,$canViewAll,$prefs);
showField('Address','snailmail',$row,$canViewAll,$prefs);
echo "</table>\n";
if ($canViewAll)
    echo "<p><a href='editUserInfo.php?id=$user_id'>[edit info]</a></p>\n";

showContactRoles($user_id);

if ($canViewAll)
    showProposals($user_id);

bifPagefooter();


function fieldPublic($prefs,$field)
    {
    if (!is_array($prefs))
        return false;
    if (!array_key_exists('public',$prefs))
        return false;
    if (!array_key_exists($field,$prefs['public']))
        return false;
    return (intval($prefs['public'][$field]) == 1);
    }


function showField($label,$field,$row,$canViewAll,$prefs)
    {
    if (($canViewAll) || (fieldPublic($prefs,$field)))
        {
        echo "<tr><th>$label</th><td>$row[$field]</td>";
        if (($canViewAll) && (fieldPublic($prefs,$field)))
            echo "<td>(public)</td>";
        echo "</tr>\n";
        }
    }


function showPrivilegeButtons($user_id)
    {
    $festival = getFestivalID();
    $stmt = dbPrepare('select privs_json from user where id=?');
    $stmt->bind_param('i',$user_id);
    if (!$stmt->execute())
        errorAndQuit("Database error: " . $stmt->error,true);
    $stmt->bind_result($privs_json);
    $stmt->fetch();
    $stmt->close();
    $thisUserPrivs = json_decode($privs_json,true);
    echo "<div style='float:right'>\n";
    foreach (array('scheduler','organizer') as $priv)
        echo privButton($priv,$thisUserPrivs,$user_id,$festival);
    echo privButton('confirmed',$thisUserPrivs,$user_id,0);
    echo "</div>\n";
    }


function privButton($priv,$thisUserPrivs,$user_id,$festival)
    {
    $s = <<<ENDSTRING
<form method="POST" action="api.php">
<input type="hidden" name="privilege" value="$priv" />
<input type="hidden" name="userid" value="$user_id" />
<input type="hidden" name="festival" value="$festival" />
ENDSTRING;
    if (privsArrayIncludes($thisUserPrivs, $priv))
        {
        $s .= <<< ENDSTRING
<input type="hidden" name="command" value="removePrivilege" />
<input type="submit" name="submit" value="Remove $priv privilege" />
ENDSTRING;
        }
    else
        {
        $s .= <<< ENDSTRING
<input type="hidden" name="command" value="addPrivilege" />
<input type="submit" name="submit" value="Grant $priv privilege" />
ENDSTRING;
        }
    $s .= "</form>\n";
    return $s;
    }


function showContactRoles($user_id)
    {
    $stmt = dbPrepare('select id,role,description from contact where userid=?');
    $stmt->bind_param('i',$user_id);
    $stmt->execute();
    $stmt->bind_result($id,$role,$description);
    $roles = array();
    while ($stmt->fetch())
        {
        $role = "<li>$role: $description";
        if ((hasPrivilege(array('scheduler','admin'))) || ($user_id == $_SESSION['userid']));
            $role .= " <a href='editContact.php?id=$id'>[edit]</a>";
        $role .= "</li>";
        $roles[] = $role;
        }
    $stmt->close();
    if (count($roles) > 0)
        {
        echo "<h2>Festival role";
        if (count($roles) > 1) echo "s";
        echo "</h2>\n<ul>";
        echo implode("\n",$roles);
        echo "</ul>\n";
        }
    }

function showProposals($user_id)
    {
    $currentFestival = getFestivalID();
    $stmt = dbPrepare('select proposal.id, title, festival.id, festival.name from proposal join festival on proposal.festival=festival.id where proposerid=? and deleted=0 order by title');
    $stmt->bind_param('i',$user_id);
    $stmt->execute();
    $stmt->bind_result($proposal_id, $title, $festivalid, $festivalname);
    $first = true;
    while ($stmt->fetch())
        {
        if ($first)
            {
            echo "<h2>Proposals</h2>\n";
            echo "<ul>\n";
            $first = false;
            }
        if ($title == '')
            $title = '!!NEEDS A TITLE!!';
        echo "<li><a href=\"proposal.php?id=$proposal_id\">$title</a>";
        if ($festivalid != $currentFestival)
            echo " ($festivalname)";
        echo "</li>\n";
        }
    if (!$first)
        echo "</ul>\n";
    $stmt->close();
    }

?>
