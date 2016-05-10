<?php
require_once 'init.php';
connectDB();
requireLogin();
require_once 'util.php';

$header = <<<ENDSTRING
<script src="jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
function unselectCheckboxes()
    {
    var checkers = document.getElementsByClassName('checker');
    for (var i=0; i<checkers.length; i++)
        checkers[i].checked = false;
    }
function selectCheckboxes()
    {
    var checkers = document.getElementsByClassName('checker');
    for (var i=0; i<checkers.length; i++)
        checkers[i].checked = true;
    }
</script>
ENDSTRING;

bifPageheader('change preferences',$header);

$summaryLabels = array();
$festival = getFestivalID();
$stmt = dbPrepare('select orgfields_json from proposal where festival=? and deleted=0');
$stmt->bind_param('i',$festival);
$stmt->execute();
$stmt->bind_result($orgfields_json);
while ($stmt->fetch())
    {
    $orgfields = json_decode($orgfields_json,true);
    if (is_array($orgfields))
        {
        foreach (array_keys($orgfields) as $k)
            if (!in_array($k,$summaryLabels))
                $summaryLabels[] = $k;
        }
    }
$stmt->close();
sort($summaryLabels);

if (isset($_SESSION['preferences']['summaryFields']))
    $currentLabels = $_SESSION['preferences']['summaryFields'];
else
    $currentLabels = 'all';
?>
<h2>Which summary fields to view</h2>
<form method="POST" action="api.php">
<input type="hidden" name="command" value="prefsSummaryFields" />
<input type="hidden" name="returnurl" value="." />
<a onclick="unselectCheckboxes()" style="background:yellow">Unselect&nbsp;all</a>
<a onclick="selectCheckboxes()" style="background:lightblue">Select&nbsp;all</a>
<ul>
<?php
foreach ($summaryLabels as $s)
    {
    echo '<li>' . $s . '<input class="checker" type="checkbox" name="summaryLabel[]" value="' . htmlspecialchars($s,ENT_COMPAT | ENT_HTML5, "UTF-8") . '"';
    if (($currentLabels==='all') || (in_array($s,$currentLabels)))
        echo " checked";
    echo " /></li>\n";
    }
?>
</ul>
<input type="submit" name="submit" value="Update" />
</form>

<?php
bifPagefooter();
?>
