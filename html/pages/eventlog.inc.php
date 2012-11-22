<?php

$param = array();

if ($vars['action'] == "expunge" && $_SESSION['userlevel'] >= '10')
{
  mysql_query("TRUNCATE TABLE `eventlog`");
  print_message("Event log truncated");
}

$numresults = 250;

$pagetitle[] = "Eventlog";

print_optionbar_start();

if (is_numeric($vars['page']))
{
  $start = $vars['page'] * $numresults;
} else
{
  $start = 0;
}

$where = "1";

if (is_numeric($_POST['device']))
{
  $where .= ' AND E.host = ?';
  $param[] = $_POST['device'];
}

if ($_POST['string'])
{
  $where .= " AND E.message LIKE ?";
  $param[] = "%".$_POST['string']."%";
}

?>

<form method="post" action="" class="form-inline">
  <span style="font-weight: bold;">Event log</span> &#187;
  <label><strong>Search</strong>
    <input type="text" name="string" id="string" value="<?php echo($_POST['string']); ?>" />
  </label>
  <label>
  <label>
    <strong>Device</strong>
    <select name="device" id="device">
      <option value="">All Devices</option>
      <?php
        foreach (get_all_devices() as $hostname)
        {
          echo("<option value='".getidbyname($hostname)."'");

          if (getidbyname($hostname) == $_POST['device']) { echo("selected"); }

          echo(">".$hostname."</option>");
        }
      ?>
    </select>
  </label>
  <button type="submit" class="btn"><i class="icon-search"></i> Search</button>
</form>

<?php

print_optionbar_end();

if ($_SESSION['userlevel'] >= '5')
{
  $query = "SELECT *,DATE_FORMAT(datetime, '%D %b %Y %T') as humandate  FROM `eventlog` AS E WHERE $where ORDER BY `datetime` DESC LIMIT $start,$numresults";
} else {
  $query = "SELECT *,DATE_FORMAT(datetime, '%D %b %Y %T') as humandate  FROM `eventlog` AS E, devices_perms AS P WHERE $where AND E.host = P.device_id AND P.user_id = ? ORDER BY `datetime` DESC LIMIT $start,$numresults";
  $param[] = $_SESSION['user_id'];
}

#echo('<table cellspacing="0" cellpadding="1" width="100%">');
echo("<table class=\"table table-bordered table-striped table-condensed\" style=\"margin-top: 10px;\">\n");
echo("  <thead>\n");
echo("    <tr>\n");
echo("      <th>Date</th>\n");
if (!isset($vars['device']) || empty($vars['device'])) {
  echo("      <th>Host</th>\n");
}
echo("      <th>Type</th>\n");
echo("      <th>Message</th>\n");
echo("    </tr>\n");
echo("  </thead>\n");

echo('<tbody>');
foreach (dbFetchRows($query, $param) as $entry)
{
  include("includes/print-event.inc.php");
}
echo('</tbody>');
echo("</table>");

?>
