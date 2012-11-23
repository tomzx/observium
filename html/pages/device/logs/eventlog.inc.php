<hr />

  <form method="post" action="" class="form-inline">
  <label><strong>Search</strong>
    <input type="text" name="message" id="message" value="<?php echo($vars['message']); ?>" />
  </label>
  <label>
    <strong>Type</strong>
    <select name="type" id="type">
      <option value="">All Types</option>
      <option value="system" <?php  if ($vars['type'] == "system") { echo(" selected"); } ?>>System</option>
      <?php
        foreach (dbFetchRows("SELECT `type` FROM `eventlog` WHERE device_id = ? GROUP BY `type` ORDER BY `type`", array($device['device_id'])) as $data)
        {
          echo("<option value='".$data['type']."'");
          if ($data['type'] == $vars['type']) { echo(" selected"); }
          echo(">".$data['type']."</option>");
        }
      ?>
    </select>
  </label>
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

$sql = "SELECT *,DATE_FORMAT(datetime, '%D %b %Y %T') as humandate  FROM `eventlog` ".$where." ORDER BY `datetime` DESC LIMIT 0,250";
$entries = dbFetchRows($sql, $param);

echo("<table class=\"table table-bordered table-striped table-condensed\" style=\"margin-top: 10px;\">\n");
echo("  <thead>\n");
echo("    <tr>\n");
echo("      <th>Date</th>\n");
echo("      <th>Type</th>\n");
echo("      <th>Message</th>\n");
echo("    </tr>\n");
echo("  </thead>\n");
echo("  <tbody>\n");
foreach ($entries as $entry) { include("includes/print-event.inc.php"); }
echo("  </tbody>\n");
echo("</table>\n");

$pagetitle[] = "Events";

?>
