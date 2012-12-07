
<?php print_optionbar_start('25'); ?>

<form method="post" action="" class="form-inline">
  <div class="input-prepend" style="margin-right: 3px;">
    <span class="add-on">Desc</span>
    <input type="text" name="string" id="prependedInput" class="input-medium" value="<?php echo($_POST['string']); ?>" />
  </div>

  <div class="input-prepend" style="margin-right: 3px;">
    <span class="add-on">Part #</span>
    <select name="part" id="part" class="span2">
      <option value="">All Parts</option>
      <?php
        foreach (dbFetchRows("SELECT `entPhysicalModelName` FROM `entPhysical` GROUP BY `entPhysicalModelName` ORDER BY `entPhysicalModelName`") as $data)
        {
          echo("<option value='".$data['entPhysicalModelName']."'");
          if ($data['entPhysicalModelName'] == $_POST['part']) { echo("selected"); }
          echo(">".$data['entPhysicalModelName']."</option>");
        }
      ?>
    </select>
  </div>

  <div class="input-prepend" style="margin-right: 3px;">
    <span class="add-on">Serial #</span>
    <input type="text" name="serial" id="serial" class="input-medium" value="<?php echo($_POST['serial']); ?>" />
  </label>
  </div>

  <div class="input-prepend" style="margin-right: 3px;">
    <span class="add-on">Device</span>
    <input type="text" class="input-medium add-on" name="device_string" id="device_string" value="<?php if ($_POST['device_string']) { echo($_POST['device_string']); } ?>" />
    <select name="device" id="device" class="span2">
      <option value="">All Devices</option>
      <?php
        foreach (dbFetchRows("SELECT * FROM `devices` ORDER BY `hostname`") as $data)
        {
          echo("<option value='".$data['device_id']."'");

          if ($data['device_id'] == $_POST['device']) { echo("selected"); }

          echo(">".$data['hostname']."</option>");
        }
      ?>
    </select>
  </div>

  <button type="submit" class="btn"><i class="icon-search"></i> Search</button>
  </form>

<?php

$pagetitle[] = "Inventory";

print_optionbar_end();

$param = array();

if ($_SESSION['userlevel'] >= '5')
{
  $sql = "SELECT * from entPhysical AS E, devices AS D WHERE D.device_id = E.device_id";
} else {
  $sql = "SELECT * from entPhysical AS E, devices AS D, devices_perms AS P WHERE D.device_id = E.device_id AND P.device_id = D.device_id AND P.user_id = ?";
  $param[] = $_SESSION['user_id'];
}

if (isset($_POST['string']) && strlen($_POST['string']))
{
  $sql  .= " AND E.entPhysicalDescr LIKE ?";
  $param[] = "%".$_POST['string']."%";
}

if (isset($_POST['device_string']) && strlen($_POST['device_string']))
{
  $sql .= " AND D.hostname LIKE ?";
  $param[] = "%".$_POST['device_string']."%";
}

if (isset($_POST['part']) && strlen($_POST['part']))
{
  $sql .= " AND E.entPhysicalModelName = ?";
  $param[] = $_POST['part'];
}

if (isset($_POST['serial']) && strlen($_POST['serial']))
{
  $sql .= " AND E.entPhysicalSerialNum LIKE ?";
  $param[] = "%".$_POST['serial']."%";
}

if (isset($_POST['device']) && is_numeric($_POST['device']))
{
  $sql .= " AND D.device_id = ?";
  $param[] = $_POST['device'];
}

echo("<table class=\"table table-striped table-condensed\" style=\"margin-top: 10px;\">\n");
echo("  <thead>\n");
echo("    <tr>\n");
echo("      <th>Device</th>\n");
echo("      <th>Descr</th>\n");
echo("      <th>Name</th>\n");
echo("      <th>Part #</th>\n");
echo("      <th>Serial #</th>\n");
echo("    </tr>\n");
echo("  </thead>\n");
echo('<tbody>');

foreach (dbFetchRows($sql, $param) as $entry)
{
  echo('<tr class="inventory"><td>' . generate_device_link($entry, shortHost($entry['hostname'])) . '</td><td>' . $entry['entPhysicalDescr']  .
     '</td><td>' . $entry['entPhysicalName']  . '</td><td>' . $entry['entPhysicalModelName']  . '</td><td>' . $entry['entPhysicalSerialNum'] . '</td></tr>');
}
echo("</table>");

?>
</table>
