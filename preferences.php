<?php
require_once 'init.php';
connectDB();
requireLogin();
require_once 'util.php';
require '../bif.php';


$header = <<<ENDSTRING
<script src="jquery-1.9.1.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
 });
</script>
<link rel="stylesheet" href="style.css" type="text/css" />
ENDSTRING;

bifPageheader('change preferences');

$summaryLabels = array();
$stmt = dbPrepare('select orgfields from proposal where deleted=0');
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
    echo '<li>' . $s . '<input type="checkbox" name="summaryLabel[]" value="' . htmlspecialchars($s) . '"';
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
