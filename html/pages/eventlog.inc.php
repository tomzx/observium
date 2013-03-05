<?php

///FIXME. Mike: should be more checks, at least a confirmation click.
//if ($vars['action'] == "expunge" && $_SESSION['userlevel'] >= '10')
//{
//  dbFetchCell('TRUNCATE TABLE `eventlog`');
//  print_message('Event log truncated');
//}

$pagetitle[] = 'Eventlog';

?>

<div class="row">
<div class="span12">

<div class="well well-shaded">

<form method="post" action="" class="form form-inline">
  <span style="font-weight: bold;">Event log</span> &#187;

  <div class="input-prepend" style="margin-right: 3px;">
    <span class="add-on">Message</span>
    <input type="text" name="message" id="message" class="input" value="<?php echo($vars['message']); ?>" />
  </div>

  <div class="input-prepend" style="margin-right: 3px;">
    <span class="add-on">Type</span>
    <select name="type" id="type">
      <option value="">All Types</option>
      <option value="system" <?php if ($vars['type'] == "system") { echo(" selected"); } ?>>System</option>
      <?php
        $where = ($vars['device']) ? "WHERE `device_id` = " . $vars['device'] : '';
        foreach (dbFetchRows("SELECT `type` FROM `eventlog` " . $where . " GROUP BY `type` ORDER BY `type`") as $data)
        {
          echo("<option value='" . $data['type'] . "'");
          if ($data['type'] == $vars['type']) { echo(" selected"); }
          echo(">" . $data['type'] . "</option>");
        }
      ?>
    </select>
  </div>

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
  <input type="hidden" name="pageno" value="1">
  <button type="submit" class="btn pull-right"><i class="icon-search"></i> Search</button>
</form>

</div>

<?php

// Pagination
$vars['pagination'] = TRUE;
if(!$vars['pagesize']) { $vars['pagesize'] = "100"; }
if(!$vars['pageno']) { $vars['pageno'] = "1"; }

// Print events
print_events($vars);

?>

  </div> <!-- span12 -->

</div> <!-- row -->
