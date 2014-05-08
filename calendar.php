<?php
require_once 'init.php';
requireLogin();
connectDB();
require_once 'scheduler.php';
getDatabase();

$header = <<<ENDSTRING
<script src="jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
 });
</script>
<link rel="stylesheet" href="style.css" type="text/css" />
ENDSTRING;

bifPageheader('calendar',$header);

?>
<table rules=all><tr><th>S<th>M<th>T<th>W<th>T<th>F<th>S</tr><tr><td colspan=4>
<td><a href='calendar.php?day=day0'><?php echo dayToDateDay(0); ?></a>
<td><a href='calendar.php?day=day1'><?php echo dayToDateDay(1); ?></a>
<td><a href='calendar.php?day=day2'><?php echo dayToDateDay(2); ?></a>
<tr><td><a href='calendar.php?day=day3'><?php echo dayToDateDay(3); ?></a>

<td><a href='calendar.php?day=day4'><?php echo dayToDateDay(4); ?></a>
<td><a href='calendar.php?day=day5'><?php echo dayToDateDay(5); ?></a>
<td><a href='calendar.php?day=day6'><?php echo dayToDateDay(6); ?></a>
<td><a href='calendar.php?day=day7'><?php echo dayToDateDay(7); ?></a>
<td><a href='calendar.php?day=day8'><?php echo dayToDateDay(8); ?></a>
<td><a href='calendar.php?day=day9'><?php echo dayToDateDay(9); ?></a>
<tr><td><a href='calendar.php?day=day10'><?php echo dayToDateDay(10); ?></a>
<td colspan=6></table>

<p>
(or <a href="venuecalendar.php">calendar ordered by venue</a>)
</p>

<?php
function date_performances($date)
    {
    global $listingList;
    $list = array();
    foreach ($listingList as $l)
        {
        if ((!$l->installation) && ($l->date == $date))
            {
            $s = sortingKey($l->starttime . $l->endtime . $l->venue->name) . listingRow($l->id,false,true,true,true,true);
            $list[] = $s;
            }
        }
    sort($list);
    echo '<table class="colorized" cellpadding=3>' . implode("\n",$list) . '</table>';
    }

function date_installations($date)
    {
    $stmt = dbPrepare('select listing.*,proposal.title,venue.name as shortname from listing join proposal on listing.proposal=proposal.id join venue on listing.venue=venue.id where listing.date=? and proposal.deleted=0 and installation=1 order by starttime');
    $stmt->bind_param('s',$date);
    $stmt->execute();
    $data = array();
    $params = array();
    $meta = $stmt->result_metadata();
    while ($field = $meta->fetch_field())
        $params[] = &$data[$field->name];
    call_user_func_array(array($stmt,'bind_result'),$params);
    echo "<table rules='all' cellpadding=3>\n";
    while ($stmt->fetch())
        {
        echo "<tr>";
        echo "<td><a href='proposal.php?id=$data[proposal]'>" . stripslashes($data['title']) . "</a>";
        echo "<td><a href='venue.php?id=$data[venue]'>" . stripslashes($data['shortname']) . "</a>";
        echo "</tr>\n";
        }
    echo "</table>\n";
    $stmt->close();
    }

if ((isset($_GET['day'])) && ($_GET['day']))
    {
    $date = dayToDate($_GET['day']);
    echo "<h2>" . dateToString($date) . "</h2>\n";
    echo "<h3>Performances</h3>\n";
    date_performances($date);
    echo "<h3>Installations</h3>\n";
    date_installations($date);
    }

else
    {
    for ($i = 0; $i < $festivalNumberOfDays; $i++)
        {
        $date = dayToDate('day' . $i);
        echo "<h2>" . dateToString($date) . "</h2>\n";
        date_performances($date);
        }
    echo "<h2>Installations</h2>\n";
    echo "<table cellpadding=3>\n";
    $instlist = array();

    $stmt = dbPrepare('select listing.*,proposal.title,venue.name as shortname from listing join proposal on listing.proposal=proposal.id join venue on listing.venue=venue.id where proposal.deleted=0 and installation=1 order by title');
    $stmt->execute();
    $instinfo = array();
    $params = array();
    $meta = $stmt->result_metadata();
    while ($field = $meta->fetch_field())
        $params[] = &$instinfo[$field->name];
    call_user_func_array(array($stmt,'bind_result'),$params);

    while ($stmt->fetch())
        {
        $id = $instinfo['proposal'];
        if (!array_key_exists($id,$instlist))
            {
            $instlist[$id] = array();
            $instlist[$id]['id'] = $id;
            $instlist[$id]['title'] = stripslashes($instinfo['title']);
            $instlist[$id]['venueid'] = $instinfo['venue'];
            $instlist[$id]['venue'] = stripslashes($instinfo['shortname']);
            }
        $instlist[$id][$instinfo['date']] = 1;
        }
    $stmt->close();

    $odd = true;
    foreach ($instlist as $inst)
        {
        if ($odd) echo '<tr class="oddrow">';
        else echo '<tr class="evenrow">';
        $odd = ! $odd;
        echo '<td><a href="proposal.php?id=' . $inst['id'] . '">' . $inst['title'] . '</a>';
        echo '<td><a href="venue.php?id=' . $inst['venueid'] . '">' . $inst['venue'] . '</a>';
        for ($d=0; $d < $festivalNumberOfDays; $d++)
            {
            if (array_key_exists(dayToDate($d),$inst) && $inst[dayToDate($d)])
                echo '<td>' . dayToDateDay($d) . '</td>';
            else echo '<td></td>';
            }
        echo "</tr>\n";
        }
    echo "</table>\n";
    }

bifPagefooter();
?>
