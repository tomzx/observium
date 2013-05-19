<?php

$pagetitle[] = 'Ports';

// Set Defaults here

if(!isset($vars['format'])) { $vars['format'] = 'list_basic'; }

echo('<div class="well" style="padding: 10px;">');

if($vars['searchbar'] != 'hide')
{

?>
<form method="post" action="" style="margin-bottom: none;">
<table style="width: 100%;" class="table-transparent">
 <tbody>
  <tr>
    <td>
      <select name="device_id" id="device_id">
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
      <select name="state" id="state">
        <option value="">All States</option>
        <option value="up" <?php if ($vars['state'] == "up") { echo("selected"); } ?>>Up</option>
        <option value="down"<?php if ($vars['state'] == "down") { echo("selected"); } ?>>Down</option>
        <option value="admindown" <?php if ($vars['state'] == "admindown") { echo("selected"); } ?>>Shutdown</option>
      </select>
    </td>
    <td>
      <select name="ifType" id="ifType">
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
        <input title="Port Description" type="text" name="ifAlias" id="ifAlias" <?php if (strlen($vars['ifAlias'])) {echo('value="'.$vars['ifAlias'].'"');} ?> />
      </td>

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
        <td rowspan=2>
        <select name="sort" id="sort" style="width: 110px;">
<?php
$sorts = array('device' => 'Device',
              'port' => 'Port',
              'speed' => 'Speed',
              'traffic' => 'Traffic In+Out',
              'traffic_in' => 'Traffic In',
              'traffic_out' => 'Traffic Out',
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
        </td>
        <td style="text-align: center;" rowspan=2>
        <button type="submit" class="btn btn-large"><i class="icon-search"></i> Search</button>
        <br />
        <a href="<?php echo(generate_url(array('page' => 'ports', 'section' => $vars['section'], 'bare' => $vars['bare']))); ?>" title="Reset critera to default." >Reset</a>
      </td>
  </tr>
  <tr>
    <td>
      <input type="text" name="hostname" id="hostname" title="Hostname" <?php if (strlen($vars['hostname'])) {echo('value="'.$vars['hostname'].'"');} ?> />
    </td>
    <td>
      <select name="ifSpeed" id="ifSpeed">
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
      <select name="port_descr_type" id="port_descr_type">
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
        <select name="location" id="location">
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
  </tr>
 </tbody>
</table>
</form>

<hr />

<?php }

echo('<span style="font-weight: bold;">Lists</span> &#187; ');

$menu_options = array('basic'      => 'Basic',
                      'detail'     => 'Detail');

$sep = "";
foreach ($menu_options as $option => $text)
{
  echo($sep);
  if ($vars['format'] == "list_".$option)
  {
    echo("<span class='pagemenu-selected'>");
  }
  echo('<a href="' . generate_url($vars, array('format' => "list_".$option)) . '">' . $text . '</a>');
  if ($vars['format'] == "list_".$option)
  {
    echo("</span>");
  }
  $sep = " | ";
}
?>

 |

<span style="font-weight: bold;">Graphs</span> &#187;

<?php

$menu_options = array('bits' => 'Bits',
                      'upkts' => 'Unicast Packets',
                      'nupkts' => 'Non-Unicast Packets',
                      'errors' => 'Errors');

$sep = "";
foreach ($menu_options as $option => $text)
{
  echo($sep);
  if ($vars['format'] == 'graph_'.$option)
  {
    echo('<span class="pagemenu-selected">');
  }
  echo('<a href="' . generate_url($vars, array('format' => 'graph_'.$option)) . '">' . $text . '</a>');
  if ($vars['format'] == 'graph_'.$option)
  {
    echo("</span>");
  }
  $sep = " | ";
}

echo('<div style="float: right;">');
?>

  <a href="<?php echo(generate_url($vars)); ?>" title="Update the browser URL to reflect the search criteria." >Update URL</a> |

<?php
  if ($vars['searchbar'] == "hide")
  {
    echo('<a href="'. generate_url($vars, array('searchbar' => '')).'">Search</a>');
  } else {
    echo('<a href="'. generate_url($vars, array('searchbar' => 'hide')).'">Search</a>');
  }

  echo("  | ");

  if ($vars['bare'] == "yes")
  {
    echo('<a href="'. generate_url($vars, array('bare' => '')).'">Header</a>');
  } else {
    echo('<a href="'. generate_url($vars, array('bare' => 'yes')).'">Header</a>');
  }

echo('</div>
  </div>');

$param = array();

if(!isset($vars['ignore']))   { $vars['ignore'] = "0"; }
if(!isset($vars['disabled'])) { $vars['disabled'] = "0"; }
if(!isset($vars['deleted']))  { $vars['deleted'] = "0"; }

$where = " WHERE 1 ";

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

$sql  = "SELECT *, `ports`.`port_id` as `port_id`";
$sql .= " FROM  `ports`";
$sql .= " JOIN `devices` ON  `ports`.`device_id` =  `devices`.`device_id`";
$sql .= " LEFT JOIN `ports-state` ON  `ports`.`port_id` =  `ports-state`.`port_id`";
$sql .= " ".$where;

$row = 1;

list($format, $subformat) = explode("_", $vars['format']);
$ports = dbFetchRows($sql, $param);

include("includes/port-sort.inc.php");

if(file_exists('pages/ports/'.$format.'.inc.php'))
{
  include('pages/ports/'.$format.'.inc.php');
} else {
  echo("Invalid Format");
}

?>
