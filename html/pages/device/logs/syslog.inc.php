
  <hr />

  <form method="post" action="" class="form-inline">
  <label><strong>Search</strong>
    <input type="text" name="string" id="string" value="<?php echo($_POST['string']); ?>" />
  </label>
  <label>
    <strong>Program</strong>
    <select name="program" id="program">
      <option value="">All Programs</option>
      <?php
        $datas = dbFetchRows("SELECT `program` FROM `syslog` WHERE device_id = ? GROUP BY `program` ORDER BY `program`", array($device['device_id']));
        foreach ($datas as $data) {
          echo("<option value='".$data['program']."'");
          if ($data['program'] == $_POST['program']) { echo("selected"); }
          echo(">".$data['program']."</option>");
        }
      ?>
    </select>
  </label>
  <button type="submit" class="btn"><i class="icon-search"></i> Search</button>
</form>

<?php

print_optionbar_end();

$param = array($device['device_id']);

if ($_POST['string'])
{
  $where   = " AND msg LIKE ?";
  $param[] = "%".$_POST['string']."%";
}

if ($_POST['program'])
{
  $where  .= " AND program = ?";
  $param[] = $_POST['program'];
}

$sql =  "SELECT *, DATE_FORMAT(timestamp, '%Y-%m-%d %T') AS date from syslog WHERE device_id = ? $where";
$sql .= " ORDER BY timestamp DESC LIMIT 1000";

echo("<table class=\"table table-bordered table-striped\" style=\"margin-top: 10px;\">\n");
echo("  <thead>\n");
echo("    <tr>\n");
echo("      <td></td>\n");
echo("      <th>Date</th>\n");
echo("      <th>Device</th>\n");
echo("      <th>Message</th>\n");
echo("    </tr>\n");
echo("  </thead>\n");
echo("  <tbody>\n");
foreach (dbFetchRows($sql, $param) as $entry) { include("includes/print-syslog.inc.php"); }
echo("  </tbody>\n");
echo("</table>");

$pagetitle[] = "Syslog";

?>
