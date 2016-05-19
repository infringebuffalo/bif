<?php
require_once 'init.php';
connectDB();
requirePrivilege('admin');
require_once 'util.php';

log_message('uploaded spreadsheet of venue info');
if ($_FILES)
    {
    bifPageheader('process venue info spreadsheet');
    $idfieldLetter = POSTvalue('idfield','A');
    $namefieldLetter = POSTvalue('namefield','B');
    $shortnamefieldLetter = POSTvalue('shortnamefield','B');
    $idfield = letterToNumber($idfieldLetter);
    $namefield = letterToNumber($namefieldLetter);
    $shortnamefield = letterToNumber($shortnamefieldLetter);
    $f = $_FILES['spreadsheet'];
    $fp = fopen($f['tmp_name'],'r');
    $headers = fgetcsv($fp);
    echo "<p>\n";
    while (true)
        {
        $data = fgetcsv($fp);
        if ($data === false)
            break;
        foreach ($data as $k=>$v)
            $data[$k] = filter_var($v, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        $id = intval($data[$idfield]);
        $name = $data[$namefield];
        $shortname = $data[$shortnamefield];
        $info = array();
        foreach ($data as $key=>$val)
            {
            if (($key != $idfield) && ($key != $namefield) && ($key != $shortnamefield))
                $info[] = array($headers[$key], $val);
            }
        $info_json = json_encode($info);
        $stmt = dbPrepare('update venue set name=?, shortname=?, info_json=? where id=?');
        $stmt->bind_param('sssi',$name,$shortname,$info_json,$id);
        if (!$stmt->execute())
            echo "error for <a href='venue.php?id=$id'>$name</a>: " . $stmt->error . "<br>\n";
        else
            echo "updated <a href='venue.php?id=$id'>$name</a><br>\n";
        $stmt->close();
        }
    echo "</p>\n";
    fclose($fp);
    log_message('finished processing venue spreadsheet');
    }
 else
    echo '<p>nothing uploaded</p>';

function letterToNumber($l)
    {
    return ord(strtoupper($l)) - ord('A');
    }

bifPagefooter();
?>
