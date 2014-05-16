<?php
require_once 'init.php';
connectDB();
requireLogin();
require_once 'util.php';

bifPageheader('change preferences');

$summaryLabels = array();
$festival = getFestivalID();
$stmt = dbPrepare('select orgfields from proposal where festival=? and deleted=0');
$stmt->bind_param('i',$festival);
$stmt->execute();
$stmt->bind_result($orgfields_ser);
while ($stmt->fetch())
    {
    $orgfields = unserialize($orgfields_ser);
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
<ul>
<?php
foreach ($summaryLabels as $s)
    {
    echo '<li>' . $s . '<input type="checkbox" name="summaryLabel[]" value="' . htmlspecialchars($s,ENT_COMPAT | ENT_HTML5, "UTF-8") . '"';
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
