<?php
 require('bif.php');
 bifPageheader('');
?>

<table>

<tr>
<td><img src='http://infringebuffalo.org/2013_frontpage_poster.jpg' alt='' title='' /></td>
<td>

<h2 style="margin-bottom: 0">Art Under the Radar</h2>
<p style="margin-top: 0; margin-bottom:2em">Every summer, the streets of Buffalo come alive with scores of events by local and visiting theatre and dance companies, puppeteers, media artists, poets, comics, musicians, cabaret acts, digital designers, and miscellaneous insurrectionists. The annual <strong>Buffalo Infringement Festival</strong> provides artists and audiences of all backgrounds the chance to come together, take chances, push boundaries, and explore uncharted territory because <strong>exciting art can happen anywhere, anytime, without a blockbuster budget</strong>. (Or any budget at all, for that matter.) 
</p>

<p>
The 2013 Buffalo Infringement Festival will run from July 25 through August 4.
</p>
</td>
</tr>
</table>

<br clear="all"/>
<br clear="all"/>

<!--
<div>
<table>
<colgroup span="3" width="30%">
<tbody>
<tr>
<?php
require 'dbconfig.php';

$db = new mysqli($dbhost, $dbusername, $dbpassword, $dbdatabase);
if ($db->connect_errno) die('Database error: ' . $db->connect_error);

$stmt = $db->prepare('select id,secret,server,farm from flickrphotos order by rand() limit 3');
$stmt->execute();
$stmt->bind_result($photoid,$secret,$server,$farm);
while ($stmt->fetch())
    {
    $imageurl = 'http://farm' . $farm . '.static.flickr.com/' . $server . '/' . $photoid . '_' . $secret . '_m.jpg';
    $pageurl = 'http://www.flickr.com/photos/dpape/' . $photoid;
    echo '<td style="text-align:center"><a href="' . $pageurl . '"><img src="' . $imageurl . '" height="160"/></a></td> ';
    }
$stmt->close();
?>
</tr>
</tbody>
</table>
</div>
-->

<?php
 bifPagefooter();
?>
