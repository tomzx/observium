
<div class="row">
<div class="span12">

<div class="well well-shaded">

<form method="post" action="" class="form form-inline">

  <div class="input-prepend" style="margin-right: 3px;">
    <span class="add-on">Device</span>
    <select name="device" id="device">
      <option value="">All Devices</option>
      <?php
        foreach ($cache['devices']['hostname'] as $hostname => $device_id)
        {
          if ($cache['devices']['id'][$device_id]['disabled'] && !$config['web_show_disabled']) { continue; }
          echo("<option value='" . $device_id . "'");
          if ($device_id == $vars['device']) { echo("selected"); }
          echo(">" . $hostname . "</option>");
        }
      ?>
    </select>
  </div>

  <div class="input-prepend" style="margin-right: 3px;">
    <span class="add-on">Interface</span>
    <select name="interface" id="interface">
      <option value="">All Interfaces</option>
      <option value="Loopback%" <?php if ($vars['interface'] == "Loopback%") { echo("selected"); } ?> >Loopbacks</option>
      <option value="Vlan%" <?php if ($vars['interface'] == "Vlan%") { echo("selected"); } ?> >VLANs</option>
    </select>
  </div>

  <div class="input-prepend" style="margin-right: 3px;">
    <span class="add-on">IP Address</span>
    <input type="text" name="address" id="address" class="input" value="<?php echo($vars['address']); ?>" />
  </div>
  
  <input type="hidden" name="pageno" value="1">
  <button type="submit" class="btn pull-right"><i class="icon-search"></i> Search</button>
</form>

</div> <!-- well -->

<?php

// Pagination
$vars['pagination'] = TRUE;
if(!$vars['pagesize']) { $vars['pagesize'] = "100"; }
if(!$vars['pageno']) { $vars['pageno'] = "1"; }

// Print addresses
print_addresses($vars);

$pagetitle[] = "IPv6 Addresses";

?>

  </div> <!-- span12 -->

</div> <!-- row -->
