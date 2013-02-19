<hr />
<form method="post" action="" class="form-inline">
  <div class="input-prepend" style="margin-right: 3px;">
    <span class="add-on">Message</span>
    <input type="text" name="message" id="message" value="<?php echo($vars['message']); ?>" />
  </div>

  <div class="input-prepend" style="margin-right: 3px;">
    <span class="add-on">Type</span>
    <select name="type" id="type">
      <option value="">All Types</option>
      <option value="system" <?php  if ($vars['type'] == "system") { echo(" selected"); } ?>>System</option>
      <?php
        foreach (dbFetchRows("SELECT `type` FROM `eventlog` WHERE host = ? GROUP BY `type` ORDER BY `type`", array($vars['device'])) as $data)
        {
          echo("<option value='".$data['type']."'");
          if ($data['type'] == $vars['type']) { echo(" selected"); }
          echo(">".$data['type']."</option>");
        }
      ?>
    </select>
  </div>
  <input type="hidden" name="pageno" value="1">
  <button type="submit" class="btn"><i class="icon-search"></i> Search</button>
</form>

<?php

print_optionbar_end();

/// Pagination
$vars['pagination'] = TRUE;
if(!$vars['pagesize']) { $vars['pagesize'] = "100"; }
if(!$vars['pageno']) { $vars['pageno'] = "1"; }

print_events($vars);

$pagetitle[] = "Events";

?>
