<?php
require 'db2/init.php';
connectDB();

$typelabel = array('theatre'=>'Theatre/misc performance', 'dance'=>'Dance', 'literary'=>'Poetry/literary', 'music'=>'Music', 'film'=>'Film &amp; video', 'visualart'=>'Visual art', 'group'=>'Group');

$out = array();
foreach ($typelabel as $type => $heading)
    {
    $out[$type] = '<tr><th><br/>' . $heading . ' shows</th></tr>';
    }

$stmt = dbPrepare('select id,title,info from proposal where deleted=0 order by title');
$stmt->execute();
$stmt->bind_result($id,$title,$info_ser);
while ($stmt->fetch())
    {
    $info = unserialize($info_ser);
    $title = stripslashes($title);
    $brochure_description = stripslashes(getInfo($info,"Description for brochure"));
    $type = getInfo($info,"Type");
    $href = '<a href="show.php?id=' . $id . '">';
    $out[$type] .= '<tr><td>' . $href . $title . '</a><br/>&nbsp;&nbsp;' . $brochure_description . ' ' . $href . '(more)</a></td></tr>';
    }
$stmt->close();


require 'bif.php';
bifPageheader('All shows');

echo '<table class="colorized">';
echo implode($out);
echo '</table>';

bifPagefooter();

function getInfo($info,$field)
    {
    foreach ($info as $i)
        if (is_array($i) && array_key_exists(0,$i) && ($i[0] == $field))
            return $i[1];
    return '';
    }
?>
