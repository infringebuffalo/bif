<?php
require_once 'init.php';
connectDB();
requirePrivilege(array('scheduler','organizer'));
require_once 'util.php';
require '../bif.php';

$type = $_REQUEST['type'];
$field = $_REQUEST['field'];
$label = $_REQUEST['label'];

$header = <<<ENDSTRING
<link rel="stylesheet" href="style.css" type="text/css" />
ENDSTRING;

bifPageheader('make summary field', $header);
    
$neworgfields = array();
$stmt = dbPrepare('select id, title, info, forminfo, orgfields from proposal where deleted=0 order by title');
$stmt->execute();
$stmt->bind_result($id,$title,$info_ser,$forminfo_ser,$orgfields_ser);
echo "<table>\n";
echo "<tr><th>proposal</th><th>value</th><th>existing value</th></tr>\n";
while ($stmt->fetch())
    {
    $info = unserialize($info_ser);
    if ($info[1][1] != $type)
        continue;
    if ($title == '')
        $title = '!!NEEDS A TITLE!!';
    $orgfields = unserialize($orgfields_ser);
    $forminfo = unserialize($forminfo_ser);
    $new = field($forminfo,$field);
    if (is_array($orgfields) && array_key_exists($label,$orgfields))
        $old = $orgfields[$label];
    else
        $old = '';
    echo "<tr><td>$title</td><td>$new</td><td>$old</td></tr>\n";
    $orgfields[$label] = $new;
    $neworgfields[$id] = $orgfields;
    }
echo "</table>\n";
$stmt->close();

foreach ($neworgfields as $id=>$orgfields)
    {
    $orgfields_ser = serialize($orgfields);
    $stmt = dbPrepare('update proposal set orgfields=? where id=?');
    $stmt->bind_param('si',$orgfields_ser,$id);
    $stmt->execute();
    $stmt->close();
    }


bifPagefooter();

function field($form,$n)
    {
    $i = 1;
    foreach ($form as $f)
        {
        if ($i == $n)
            return $f;
        $i = $i + 1;
        }
    }
?>
