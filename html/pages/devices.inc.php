<?php

// Set Defaults here

if(!isset($vars['format'])) { $vars['format'] = "list_detail"; }

$sql_param = array();

/// FIXME - new style of searching here

if ($vars['hostname']) { $where .= " AND hostname LIKE ?"; $sql_param[] = "%".$vars['hostname']."%"; }
if ($vars['sysname']) { $where .= " AND sysName LIKE ?"; $sql_param[] = "%".$vars['sysname']."%"; }
if ($vars['os'])       { $where .= " AND os = ?";          $sql_param[] = $vars['os']; }
if ($vars['version'])  { $where .= " AND version = ?";     $sql_param[] = $vars['version']; }
if ($vars['hardware']) { $where .= " AND hardware = ?";    $sql_param[] = $vars['hardware']; }
if ($vars['features']) { $where .= " AND features = ?";    $sql_param[] = $vars['features']; }
if ($vars['type'])     { $where .= " AND type = ?";        $sql_param[] = $vars['type']; }
if (isset($vars['status']))   { $where .= " AND status = ?";      $sql_param[] = $vars['status']; }
if (isset($vars['ignore']))   { $where .= " AND ignore = ?";      $sql_param[] = $vars['ignore']; }
if (!$config['web_show_disabled']) { $where .= " AND disabled = 0"; }
elseif (isset($vars['disabled'])) { $where .= " AND disabled = ?";    $sql_param[] = $vars['disabled']; }


if ($vars['location'] == "Unset") { $location_filter = ''; }
if ($vars['location']) { $location_filter = $vars['location']; }

$pagetitle[] = "Devices";

echo('<div class="well" style="padding: 10px;">');


if($vars['searchbar'] != "hide")
{

?>

<form method="post" action="" style="margin-bottom: 0;">
  <table width="100%">
    <tr>
      <td width="290">
        <div class="input-prepend" style="margin-right: 3px; margin-bottom: 10px;">
          <span class="add-on" style="width: 80px;">Hostname</span>
          <input type="text" name="hostname" id="hostname" class="input" value="<?php echo($vars['hostname']); ?>" />
        </div>

        <div class="input-prepend" style="margin-right: 3px;  margin-bottom: 10px;">
          <span class="add-on" style="width: 80px;">sysName</span>
          <input type="text" name="sysname" id="sysname" class="input" value="<?php echo($vars['sysname']); ?>" />
        </div>

      </td>
      <td width="200">
        <select name='os' id='os'>
          <option value=''>All OSes</option>
          <?php

$where_form = ($config['web_show_disabled']) ? '' : 'AND disabled = 0';
foreach (dbFetch('SELECT `os` FROM `devices` AS D WHERE 1 '.$where_form.' GROUP BY `os` ORDER BY `os`') as $data)
{
  if ($data['os'])
  {
    echo("<option value='".$data['os']."'");
    if ($data['os'] == $vars['os']) { echo(" selected"); }
    echo(">".$config['os'][$data['os']]['text']."</option>");
  }
}
          ?>
        </select>
        <br />
        <select name='version' id='version'>
          <option value=''>All Versions</option>
          <?php

foreach (dbFetch('SELECT `version` FROM `devices` AS D WHERE 1 '.$where_form.' GROUP BY `version` ORDER BY `version`') as $data)
{
  if ($data['version'])
  {
    echo("<option value='".$data['version']."'");
    if ($data['version'] == $vars['version']) { echo(" selected"); }
    echo(">".$data['version']."</option>");
  }
}
          ?>
        </select>
      </td>
      <td width="200">
        <select name="hardware" id="hardware">
          <option value="">All Platforms</option>
          <?php
foreach (dbFetch('SELECT `hardware` FROM `devices` AS D WHERE 1 '.$where_form.' GROUP BY `hardware` ORDER BY `hardware`') as $data)
{
  if ($data['hardware'])
  {
    echo('<option value="'.$data['hardware'].'"');
    if ($data['hardware'] == $vars['hardware']) { echo(" selected"); }
    echo(">".$data['hardware']."</option>");
  }
}
          ?>
        </select>
        <br />
        <select name="features" id="features">
          <option value="">All Featuresets</option>
          <?php

foreach (dbFetch('SELECT `features` FROM `devices` AS D WHERE 1 '.$where_form.' GROUP BY `features` ORDER BY `features`') as $data)
{
  if ($data['features'])
  {
    echo('<option value="'.$data['features'].'"');
    if ($data['features'] == $vars['features']) { echo(" selected"); }
    echo(">".$data['features']."</option>");
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
<br />
        <select name="type" id="type">
          <option value="">All Device Types</option>
          <?php

foreach (dbFetch('SELECT `type` FROM `devices` AS D WHERE 1 '.$where_form.' GROUP BY `type` ORDER BY `type`') as $data)
{
  if ($data['type'])
  {
    echo("<option value='".$data['type']."'");
    if ($data['type'] == $vars['type']) { echo(" selected"); }
    echo(">".ucfirst($data['type'])."</option>");
  }
}
          ?>
        </select>

      </td>
      <td align="center">
        <button type="submit" class="btn btn-large"><i class="icon-search"></i> Search</button>
        <br />
        <a href="<?php echo(generate_url($vars)); ?>" title="Update the browser URL to reflect the search criteria." >Update URL</a> |
        <a href="<?php echo(generate_url(array('page' => 'devices', 'section' => $vars['section'], 'bare' => $vars['bare']))); ?>" title="Reset critera to default." >Reset</a>
      </td>
    </tr>
  </table>
</form>

<hr style="margin: 0px 0px 10px 0px;">

<?php

}

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

$menu_options = array('bits'      => 'Bits',
                      'processor' => 'CPU',
                      'mempool'   => 'Memory',
                      'uptime'    => 'Uptime',
                      'storage'   => 'Storage',
                      'diskio'    => 'Disk I/O'
                      );
$sep = "";
foreach ($menu_options as $option => $text)
{
  echo($sep);
  if ($vars['format'] == 'graph_'.$option)
  {
    echo("<span class='pagemenu-selected'>");
  }
  echo('<a href="' . generate_url($vars, array('format' => 'graph_'.$option)) . '">' . $text . '</a>');
  if ($vars['format'] == 'graph_'.$option)
  {
    echo("</span>");
  }
  $sep = " | ";
}

?>

<div style="float: right;">

<?php

  if ($vars['searchbar'] == "hide")
  {
    echo('<a href="'. generate_url($vars, array('searchbar' => '')).'">Restore Search</a>');
  } else {
    echo('<a href="'. generate_url($vars, array('searchbar' => 'hide')).'">Remove Search</a>');
  }

  echo("  | ");

  if ($vars['bare'] == "yes")
  {
    echo('<a href="'. generate_url($vars, array('bare' => '')).'">Restore Header</a>');
  } else {
    echo('<a href="'. generate_url($vars, array('bare' => 'yes')).'">Remove Header</a>');
  }

?>

  </div>
</div>

<?php

$query = "SELECT * FROM `devices` WHERE 1 ".$where." ORDER BY hostname";

list($format, $subformat) = explode("_", $vars['format']);

$devices = dbFetchRows($query, $sql_param);

if(count($devices)) {

  if(file_exists('pages/devices/'.$format.'.inc.php'))
  {
    include('pages/devices/'.$format.'.inc.php');
  } else {
    echo('<table class="table table-hover table-striped table-bordered table-condensed table-rounded" style="margin-top: 10px;">');
    if ($subformat == "detail")
    {
    echo("  <thead>\n");
    echo("    <tr>\n");
    echo("      <th></th>\n");
    echo("      <th></th>\n");
    echo("      <th>Device</th>\n");
    echo("      <th></th>\n");
    echo("      <th>Platform</th>\n");
    echo("      <th>Operating System</th>\n");
    echo("      <th>Uptime/Location</th>\n");
    echo("    </tr>\n");
    echo("  </thead>\n");
    }

    foreach ($devices as $device)
    {
      if (device_permitted($device['device_id']))
      {
        if (!$location_filter || ((get_dev_attrib($device,'override_sysLocation_bool') && get_dev_attrib($device,'override_sysLocation_string') == $location_filter)
          || $device['location'] == $location_filter))
        {
          if ($subformat == "detail")
          {
            include("includes/hostbox.inc.php");
          } else {
            include("includes/hostbox-basic.inc.php");
          }
        }
      }
    }
    echo("</table>");
  }
} else {

?>
<div class="alert alert-error">
  <h4>No devices found</h4>
  Please try adjusting your search parameters.
</div>

<?php
}

?>
