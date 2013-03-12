<div class="row">
<div class="span12">

<div class="well well-shaded">

<form method="post" action="" class="form form-inline">


  <span style="font-weight: bold;">ARP Search</span> &#187;
  
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

?>

  </div> <!-- span12 -->

</div> <!-- row -->