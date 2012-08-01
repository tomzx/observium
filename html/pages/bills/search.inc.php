<?php

$data = "";

if (isset($_POST['billsearch'])) {
  $type['cdr']    = (($_POST['billingtype'] == "cdr") ? " selected" : "");
  $type['quota']  = (($_POST['billingtype'] == "quota") ? " selected" : "");
  $state['under'] = (($_POST['billingstate'] == "under") ? " selected" : "");
  $state['over']  = (($_POST['billingstate'] == "over") ? " selected" : "");
}

if ($isAdmin) {
  $data .= "<option value=\"\">All Customers</option>";
  $data .= "<optgroup label=\"Customer:\">";
  foreach(dbFetchRows("SELECT * FROM `bill_perms` GROUP BY `user_id` ORDER BY `user_id` ") as $customers) {
    if (bill_permitted($customers['bill_id'])) {
      $customer = dbFetchRow("SELECT * FROM `users` WHERE `user_id` = ? ORDER BY `user_id`", array($customers['user_id']));
      $name     = (empty($customer['realname']) ? $customer['username'] : $customer['realname']);
      $select   = (($_POST['billinguser'] == $customer['user_id']) ? " selected" : "");
      $data    .= "<option value=\"".$customer['user_id']."\"".$select.">".$name."</option>";
    }
  }
  $data .= "</optgroup>";
} else {
  $data .= "<optgroup label=\"Customer:\">";
  $data .= "<option value=\"".$_SESSION['user_id']."\" selected>".$_SESSION['username']."</option>";
  $data .= "</optgroup>";
}

?>

<form class="well form-search" method="post" action="" style="padding-bottom: 10px;">
  <fieldset>
    <strong>Search:</strong>
    <input type="hidden" name="billsearch" value="true" />
    <input class="span4" type="text" name="billingname" id="billingname" value="<?php echo($_POST['billingname']); ?>" />
    <select class="span2" name="billingtype" id="billingtype">
      <option value="">All Types</option>
      <optgroup label="Type:">
        <option value="cdr"<?php echo($type['cdr']); ?>>CDR 95th</option>
        <option value="quota"<?php echo($type['quota']); ?>>Quota</option>
        <!-- <option value="avg"<?php echo($type['avg']); ?>>Average</option> //-->
      </optgroup>
    </select>
    <!--
    <select class="span2" name="billingstate" id="billingstate">
      <option value="">All Usages</option>
      <optgroup label="Usage:">
        <option value="under"<?php echo($state['under']); ?>>Under</option>
        <option value="over"<?php echo($state['over']); ?>>Over</option>
      </optgroup>
    </select>
    //-->
    <select class="span3" name="billinguser" id="billinguser">
      <?php echo($data); ?>
    </select>
    <button type="submit" class="btn"><i class="icon-search"></i> Search</button>
  </fieldset>
</form>
