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

?>

<form method="post" action="" class="form-inline">
  <span style="font-weight: bold;">Event log</span> &#187;

  <div class="input-prepend" style="margin-right: 3px;">
    <span class="add-on">Message</span>
    <input type="text" name="message" id="message" class="input" value="<?php echo($vars['message']); ?>" />
  </div>

  <div class="input-prepend" style="margin-right: 3px;">
    <span class="add-on">Type</span>
    <select name="type" id="type">
      <option value="">All Types</option>
      <option value="system" <?php  if ($vars['type'] == "system") { echo(" selected"); } ?>>System</option>
      <?php
        foreach (dbFetchRows("SELECT `type` FROM `eventlog` GROUP BY `type` ORDER BY `type`") as $data)
        {
          echo("<option value='".$data['type']."'");
          if ($data['type'] == $vars['type']) { echo(" selected"); }
          echo(">".$data['type']."</option>");
        }
      ?>
    </select>
  </div>

  <div class="input-prepend" style="margin-right: 3px;">
    <span class="add-on">Device</span>
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
  </div>

  <button type="submit" class="btn"><i class="icon-search"></i> Search</button>
</form>

<?php

print_optionbar_end();

$param = array();
$where = " WHERE 1 ";

foreach ($vars as $var => $value)
{
  if ($value != "")
  {
    switch ($var)
    {
      case 'device':
        $where .= " AND `host` = ?";
        $param[] = $value;
        break;
      case 'type':
        $where .= " AND `$var` = ?";
        $param[] = $value;
        break;
      case 'message':
        foreach(explode(",", $value) as $val)
        {
          $param[] = "%".$val."%";
          $cond[] = "`$var` LIKE ?";
        }
        $where .= "AND (";
        $where .= implode(" OR ", $cond);
        $where .= ")";
        break;
    }
  }
}

#$sql = "SELECT *,DATE_FORMAT(datetime, '%D %b %Y %T') as humandate  FROM `eventlog` ".$where." ORDER BY `datetime` DESC LIMIT 0,250";

if ($_SESSION['userlevel'] >= '5')
{
  $query = "SELECT *,DATE_FORMAT(datetime, '%D %b %Y %T') as humandate  FROM `eventlog` AS E ".$where." ORDER BY `datetime` DESC LIMIT $start,$numresults";
} else {
  $query = "SELECT *,DATE_FORMAT(datetime, '%D %b %Y %T') as humandate  FROM `eventlog` AS E, devices_perms AS P ".$where." AND E.host = P.device_id AND P.user_id = ? ORDER BY `datetime` DESC LIMIT $start,$numresults";
  $param[] = $_SESSION['user_id'];
}
$entries = dbFetchRows($query, $param);

if(!$vars['pagesize']) { $vars['page_size'] = "100"; }

echo pagination($vars, count($entries));

if($vars['pageno'])
{
  $entries = array_chunk($entries, $vars['pagesize']);
  $entries = $entries[$vars['pageno']-1];
}

#echo('<table cellspacing="0" cellpadding="1" width="100%">');
echo("<table class=\"table table-striped table-condensed\" style=\"margin-top: 10px;\">\n");
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
foreach ($entries as $entry)
{
  include("includes/print-event.inc.php");
}
echo('</tbody>');
echo("</table>");

?>
