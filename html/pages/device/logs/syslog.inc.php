
<hr />
<form method="post" action="" class="form-inline">

  <div class="input-prepend" style="margin-right: 3px;">
    <span class="add-on">Message</span>
    <input type="text" name="message" id="message" class="input" value="<?php echo($vars['message']); ?>" />
  </div>

  <div class="input-prepend" style="margin-right: 3px;">
    <span class="add-on">Priority</span>
    <select name="priority" id="priority">
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
    <select name="program" id="program">
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

$pagetitle[] = "Syslog";

?>
