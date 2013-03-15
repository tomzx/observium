<div class="row">
<div class="span12">

<div class="well well-shaded">

<form method="post" action="" class="form form-inline">


  <span style="font-weight: bold;">ARP/NDP Search</span> &#187;
  
  <div class="input-prepend" style="margin-right: 3px;">
    <span class="add-on">Device</span>
    <select name="device_id" id="device_id">
      <option value="">All Devices</option>
<?php

// Select the devices only with ARP/NDP tables
foreach (dbFetchRows('SELECT D.device_id AS device_id, `hostname`
                     FROM `ip_mac` AS M
                     LEFT JOIN `ports` AS P ON M.port_id = P.port_id
                     LEFT JOIN `devices` AS D ON P.device_id = D.device_id
                     GROUP BY `device_id`
                     ORDER BY `hostname`;') as $data)
{
  $device_id = $data['device_id'];
  // Exclude not permited devices
  if (isset($cache['devices']['id'][$device_id]))
  {
    if ($cache['devices']['id'][$device_id]['disabled'] && !$config['web_show_disabled']) { continue; }
    echo('<option value="' . $device_id . '"');
    if ($device_id == $vars['device_id']) { echo('selected'); }
    echo('>' . $data['hostname'] . '</option>');
  }
}
?>
    </select>
  </div>
  
  <div class="input-prepend" style="margin-right: 3px;">
    <span class="add-on">Search By</span>
    <select name="searchby" id="searchby">
      <option value="mac" <?php if ($vars['searchby'] != 'ip') { echo("selected"); } ?> >MAC Address</option>
      <option value="ip" <?php if ($vars['searchby'] == 'ip') { echo("selected"); } ?> >IP Address</option>
    </select>
  </div>

  <div class="input-prepend" style="margin-right: 3px;">
    <span class="add-on">Address</span>
    <input type="text" name="address" id="address" class="input" value="<?php echo($vars['address']); ?>" />
  </div>
  
  <input type="hidden" name="pageno" value="1">
  <button type="submit" class="btn pull-right"><i class="icon-search"></i> Search</button>
</form>

</div> <!-- well -->

<?php

// Pagination
$vars['pagination'] = TRUE;
if(!$vars['pagesize']) { $vars['pagesize'] = 100; }
if(!$vars['pageno']) { $vars['pageno'] = 1; }

print_arptable($vars);

$pagetitle[] = 'ARP/NDP Search';

?>

  </div> <!-- span12 -->

</div> <!-- row -->