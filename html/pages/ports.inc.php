<?php

$pagetitle[] = 'Ports';

// Set Defaults here

if(!isset($vars['format'])) { $vars['format'] = 'list'; }

echo('<div class="well" style="padding: 10px;">');

if($vars['searchbar'] != 'hide')
{

?>
<form method="post" action="" class="form form-inline" style="margin-bottom: 0;" id="ports-form">
<table style="width: 100%;" cellpadding="5" class="table-transparent">
 <tbody>
  <tr>
    <td>
      <select name="device_id" id="device_id" class="selectpicker">
        <option value="">All Devices</option>
<?php

foreach (dbFetchRows('SELECT `device_id`, `hostname` FROM `devices` GROUP BY `hostname` ORDER BY `hostname`') as $data)
{
  if(device_permitted($data['device_id']))
  {
    echo('        <option value="'.$data['device_id'].'"');
    if ($data['device_id'] == $vars['device_id']) { echo('selected'); }
    echo('>'.$data['hostname'].'</option>');
  }
}
?>
      </select>
    </td>
    <td>
      <select name="state" id="state" class="selectpicker">
        <option value="">All States</option>
        <option value="up" <?php if ($vars['state'] == "up") { echo("selected"); } ?>>Up</option>
        <option value="down"<?php if ($vars['state'] == "down") { echo("selected"); } ?>>Down</option>
        <option value="admindown" <?php if ($vars['state'] == "admindown") { echo("selected"); } ?>>Shutdown</option>
      </select>
    </td>
    <td>
      <select name="ifType" id="ifType" class="selectpicker">
        <option value="">All Media</option>
<?php
foreach (dbFetchRows("SELECT `ifType` FROM `ports` GROUP BY `ifType` ORDER BY `ifType`") as $data)
{
  if ($data['ifType'])
  {
    echo('        <option value="'.$data['ifType'].'"');
    if ($data['ifType'] == $vars['ifType']) { echo("selected"); }
    echo(">".$data['ifType']."</option>");
  }
}
?>
       </select>
       </td>
       <td>
        <input placeholder="Port Description" title="Port Description" type="text" name="ifAlias" id="ifAlias" <?php if (strlen($vars['ifAlias'])) {echo('value="'.$vars['ifAlias'].'"');} ?> />
      </td>
<?php
/**
      <td width=80 rowspan=2>
        <label for="ignore">
        <input type=checkbox id="ignore" name="ignore" value=1 <?php if ($vars['ignore']) { echo("checked"); } ?> ></input> Ignored
        </label>
        <label for="disable">
        <input type=checkbox id="disable" name="disable" value=1 <?php if ($vars['disable']) { echo("checked"); } ?> > Disabled</input>
        </label>
        <label for="deleted">
        <input type=checkbox id="deleted" name="deleted" value=1 <?php if ($vars['deleted']) { echo("checked"); } ?> > Deleted</input>
        </label>
        </td>
**/
?>
        <td>

        </td>
  </tr>
  <tr>
    <td>
      <input placeholder="Hostname" type="text" name="hostname" id="hostname" title="Hostname" <?php if (strlen($vars['hostname'])) {echo('value="'.$vars['hostname'].'"');} ?> />
    </td>
    <td>
      <select name="ifSpeed" id="ifSpeed" class="selectpicker">
      <option value="">All Speeds</option>
<?php


foreach (dbFetchRows("SELECT `ifSpeed` FROM `ports` GROUP BY `ifSpeed` ORDER BY `ifSpeed`") as $data)
{
  if ($data['ifSpeed'])
  {
    echo("<option value='".$data['ifSpeed']."'");
    if ($data['ifSpeed'] == $vars['ifSpeed']) { echo("selected"); }
    echo(">".humanspeed($data['ifSpeed'])."</option>");
  }
}
?>
       </select>

    </td>
    <td>
      <select name="port_descr_type" id="port_descr_type" class="selectpicker">
        <option value="">All Port Types</option>
<?php
$ports = dbFetchRows("SELECT `port_descr_type` FROM `ports` GROUP BY `port_descr_type` ORDER BY `port_descr_type`");
$total = count($ports);
echo("Total: $total");
foreach ($ports as $data)
{
  if ($data['port_descr_type'])
  {
    echo('        <option value="'.$data['port_descr_type'].'"');
    if ($data['port_descr_type'] == $vars['port_descr_type']) { echo("selected"); }
    echo(">".ucfirst($data['port_descr_type'])."</option>");
  }
}
?>
         </select>

    </td>
    <td>
        <select name="location" id="location" class="selectpicker">
          <option value="">All Locations</option>
          <?php
           // fix me function?

           foreach (getlocations() as $location) // FIXME function name sucks maybe get_locations ?
           {
             if ($location)
             {
               echo('<option value="'.$location.'"');
               if ($location == $vars['location']) { echo(" selected"); }
               echo(">".$location."</option>");
             }
           }
         ?>
        </select>
    </td>

        <td>

        <select name="sort" id="sort" class="selectpicker" title="Sort Order" style="width: 150px;" data-width="150px">
<?php
$sorts = array('device' => 'Device',
              'port' => 'Port',
              'speed' => 'Speed',
              'traffic' => 'Traffic In+Out',
              'traffic_in' => 'Traffic In',
              'traffic_out' => 'Traffic Out',
              'traffic_perc' => 'Traffic Percentage In+Out',
              'traffic_perc_in' => 'Traffic Percentage In',
              'traffic_perc_out' => 'Traffic Percentage Out',
              'packets' => 'Packets In+Out',
              'packets_in' => 'Packets In',
              'packets_out' => 'Packets Out',
              'errors' => 'Errors',
              'media' => 'Media',
              'descr' => 'Description');

foreach ($sorts as $sort => $sort_text)
{
  echo('<option value="'.$sort.'" ');
  if ($vars['sort'] == $sort)  { echo("selected"); }
  echo('>'.$sort_text.'</option>');
}
?>

        </select>


        <button type="submit" onClick="submitURL()" class="btn"><i class="icon-search"></i> Search</button>
      </td>


  </tr>
 </tbody>
</table>
</form>

<script>

// This code updates the FORM URL

function submitURL() {
  var url = '/ports/';

    var partFields = document.getElementById("ports-form").elements;

    for(var el, i = 0, n = partFields.length; i < n; i++) {
      el = partFields[i];
      if(el.value != '') {
        if (el.checked || el.type !== "checkbox") {
            url += encodeURIComponent(el.name) + "=" +
                   encodeURIComponent(el.value) + "/"
            ;
        }
      }
    }

   $('#ports-form').attr('action', url);

}

</script>

</div>

<?php }


$navbar = array('brand' => "Ports", 'class' => "navbar-narrow");

$navbar['options']['basic']['text']   = 'Basic';
// There is no detailed view for this yet.
//$navbar['options']['detail']['text']  = 'Details';

$navbar['options']['graphs']     = array('text' => 'Graphs');

foreach ($navbar['options'] as $option => $array)
{
  if ($vars['format'] == 'list' && !isset($vars['view'])) { $vars['view'] = 'basic'; }
  if ($vars['format'] == 'list' && $vars['view'] == $option) { $navbar['options'][$option]['class'] .= " active"; }
  $navbar['options'][$option]['url'] = generate_url($vars,array('format' => 'list', 'view' => $option));
}

foreach (array('graphs') as $type)
{
  foreach ($config['graph_types']['port'] as $option => $data)
  {
    if ($vars['format'] == $type && $vars['graph'] == $option)
    {
      $navbar['options'][$type]['suboptions'][$option]['class'] = 'active';
      $navbar['options'][$type]['text'] .= " (".$data['name'].')';
    }
    $navbar['options'][$type]['suboptions'][$option]['text'] = $data['name'];
    $navbar['options'][$type]['suboptions'][$option]['url'] = generate_url($vars, array('view' => NULL, 'format' => $type, 'graph' => $option));
  }
}

  if ($vars['searchbar'] == "hide")
  {
    $navbar['options_right']['searchbar']     = array('text' => 'Show Search', 'url' => generate_url($vars, array('searchbar' => NULL)));
  } else {
    $navbar['options_right']['searchbar']     = array('text' => 'Hide Search' , 'url' => generate_url($vars, array('searchbar' => 'hide')));
  }

  if ($vars['bare'] == "yes")
  {
    $navbar['options_right']['header']     = array('text' => 'Show Header', 'url' => generate_url($vars, array('bare' => NULL)));
  } else {
    $navbar['options_right']['header']     = array('text' => 'Hide Header', 'url' => generate_url($vars, array('bare' => 'yes')));
  }

  $navbar['options_right']['reset']        = array('text' => 'Reset', 'url' => generate_url(array('page' => 'ports', 'section' => $vars['section'], 'bare' => $vars['bare'])));

print_navbar($navbar);
unset($navbar);

if($debug) { print_vars($vars); }


$param = array();

if(!isset($vars['ignore']))   { $vars['ignore'] = "0"; }
if(!isset($vars['disabled'])) { $vars['disabled'] = "0"; }
if(!isset($vars['deleted']))  { $vars['deleted'] = "0"; }

$select = "`ports`.`port_id` AS `port_id`, `devices`.`device_id` AS `device_id`";
$where = " WHERE 1 ";

include("includes/port-sort-select.inc.php");

foreach ($vars as $var => $value)
{
  if ($value != "")
  {
    switch ($var)
    {
      case 'hostname':
      case 'location':
        $where .= " AND `$var` LIKE ?";
        $param[] = "%".$value."%";
      case 'device_id':
      case 'deleted':
      case 'ignore':
      case 'disable':
      case 'ifSpeed':
        if (is_numeric($value))
        {
          $where .= " AND `ports`.`$var` = ?";
          $param[] = $value;
        }
        break;
      case 'ifType':
        $where .= " AND `$var` = ?";
        $param[] = $value;
        break;
      case 'ifAlias':
        foreach (explode(",", $value) as $val)
        {
          $param[] = "%".$val."%";
          $cond[] = "`$var` LIKE ?";
        }
        $where .= "AND (";
        $where .= implode(" OR ", $cond);
        $where .= ")";
        break;
      case 'port_descr_type':
        foreach (explode(",", $value) as $val)
        {
          $param[] = $val;
          $cond[] = "`$var` LIKE ?";
        }
        $where .= "AND (";
        $where .= implode(" OR ", $cond);
        $where .= ")";
        break;
      case 'errors':
        if ($value == 1 || $value == "yes")
        {
          $where .= " AND (`ifInErrors_delta` > '0' OR `ifOutErrors_delta` > '0')";
        }
        break;
      case 'alerted':
        if ($value == "yes")
        {
          $where .= "AND `ifAdminStatus` = ? AND ( `ifOperStatus` = ? OR `ifOperStatus` = ? )";
          $param[] = "up";
          $param[] = "LowerLayerDown";
          $param[] = "down";
        }
      case 'state':
        if ($value == "down")
        {
          $where .= "AND `ifAdminStatus` = ? AND `ifOperStatus` = ?";
          $param[] = "up";
          $param[] = "down";
        } elseif($value == "up") {
          $where .= "AND `ifAdminStatus` = ? AND `ifOperStatus` = ?";
          $param[] = "up";
          $param[] = "up";
        } elseif($value == "admindown") {
          $where .= "AND `ifAdminStatus` = ?";
          $param[] = "down";
        }
      break;
    }
  }
}

$sql  = "SELECT " . $select;
$sql .= " FROM  `ports`";
$sql .= " INNER JOIN `devices` ON `ports`.`device_id` = `devices`.`device_id`";
$sql .= " LEFT JOIN `ports-state` ON `ports`.`port_id` = `ports-state`.`port_id`";
$sql .= " ".$where;

$row = 1;

$ports = dbFetchRows($sql, $param);

port_permitted_array($ports);

include("includes/port-sort.inc.php");

if(file_exists('pages/ports/'.$vars['format'].'.inc.php'))
{
  include('pages/ports/'.$vars['format'].'.inc.php');
} else {
  print_error("Wrong list format.");
}

?>
