<?php

if ($_vars['action'] == "expunge" && $_SESSION['userlevel'] >= '10') { dbFetchCell("TRUNCATE TABLE `syslog`"); }

print_optionbar_start();

$pagetitle[] = "Syslog";

?>

<form method="post" action="" class="form-inline">
  <span style="font-weight: bold;">Syslog</span> &#187;

  <div class="input-prepend" style="margin-right: 3px;">
    <span class="add-on">Message</span>
    <input type="text" name="message" id="message" class="input" value="<?php echo($vars['message']); ?>" />
  </div>

    <div class="input-prepend" style="margin-right: 3px;">
    <span class="add-on">Priority</span>
    <select name="priority" id="priority" style="width: 140px;">
      <?php
      $prioritys = syslog_prioritys();
      $string = "      <option value=\"\">All Prioritys</option>";
      for($i = 0; $i <= 7; $i++)
      {
        $string .= '<option value="' . $i . '"';
        $string .= ($vars['priority'] === "$i") ? ' selected>' : '>';
        $string .= "(" . $i . ") " . $prioritys[$i]['name'] . "</option>\n";
      }
      echo $string;
      ?>
    </select>
  </div>

  <div class="input-prepend" style="margin-right: 3px;">
    <span class="add-on">Program</span>
    <select name="program" id="program" style="width: 140px;">
      <option value="">All Programs</option>
      <?php
        $where = ($vars['device']) ? "WHERE `device_id` = " . $vars['device'] : '';
        foreach (dbFetchRows("SELECT `program` FROM `syslog` " . $where . " GROUP BY `program` ORDER BY `program`") as $data)
        {
          $data['program'] = ($data['program'] === "") ? "[[EMPTY]]" : $data['program'];
          echo("<option value='" . $data['program'] . "'");
          if ($data['program'] === $vars['program']) { echo(" selected"); }
          echo(">" . $data['program'] . "</option>");
        }
      ?>
    </select>
  </div>
  
  <div class="input-prepend" style="margin-right: 3px;">
    <span class="add-on">Device</span>
    <select name="device" id="device" style="width: 140px;">
      <option value="">All Devices</option>
      <?php
        $devices = dbFetchRows("SELECT S.`device_id` AS `device`, hostname FROM `syslog` AS S JOIN `devices` AS D ON S.device_id = D.device_id GROUP BY `hostname` ORDER BY `hostname`");
        foreach ($devices as $data)
        {
          echo("<option value='" . $data['device'] . "'");
          if ($data['device'] == $vars['device']) { echo("selected"); }
          echo(">" . $data['hostname'] . "</option>");
        }
      ?>
    </select>
  </div>
  <input type="hidden" name="pageno" value="1">
  <button type="submit" class="btn"><i class="icon-search"></i> Search</button>
</form>

<?php

print_optionbar_end();

// Pagination
$vars['pagination'] = TRUE;
if(!$vars['pagesize']) { $vars['pagesize'] = "100"; }
if(!$vars['pageno']) { $vars['pageno'] = "1"; }

// Print syslog
print_syslogs($vars);

?>
