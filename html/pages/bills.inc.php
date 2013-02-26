<?php

if(!is_file("includes/jpgraph/src/jpgraph.php"))
{
  ?>

<div class="alert alert-error">
  <h4>No Jpgraph installed</h4>
  <i>Jpgraph has been removed from the Observium repositories and must now be installed separately</i> <br />
  * Please download from <a href="http://jpgraph.net/download/">http://jpgraph.net/download/</a> and unpack to html/includes/jpgraph.<br />
  * Remove the theme definition from the bottom of html/includes/jpgraph/src/jpg-config.inc.php
</div>

  <?php
}


$isAdmin    = (($_SESSION['userlevel'] == "10") ? true : false);
$isUser     = bill_permitted($bill_id);

if ($_POST['addbill'] == "yes")
{
  $updated = '1';

  if (isset($_POST['bill_quota']) or isset($_POST['bill_cdr'])) {
    if ($_POST['bill_type'] == "quota") {
      if (isset($_POST['bill_quota_type'])) {
        if ($_POST['bill_quota_type'] == "MB") { $multiplier = 1 * $config['billing']['base']; }
        if ($_POST['bill_quota_type'] == "GB") { $multiplier = 1 * $config['billing']['base'] * $config['billing']['base']; }
        if ($_POST['bill_quota_type'] == "TB") { $multiplier = 1 * $config['billing']['base'] * $config['billing']['base'] * $config['billing']['base']; }
        $bill_quota = (is_numeric($_POST['bill_quota']) ? $_POST['bill_quota'] * $config['billing']['base'] * $multiplier : 0);
        $bill_cdr = 0;
      }
    }
    if ($_POST['bill_type'] == "cdr") {
      if (isset($_POST['bill_cdr_type'])) {
        if ($_POST['bill_cdr_type'] == "Kbps") { $multiplier = 1 * $config['billing']['base']; }
        if ($_POST['bill_cdr_type'] == "Mbps") { $multiplier = 1 * $config['billing']['base'] * $config['billing']['base']; }
        if ($_POST['bill_cdr_type'] == "Gbps") { $multiplier = 1 * $config['billing']['base'] * $config['billing']['base'] * $config['billing']['base']; }
        $bill_cdr = (is_numeric($_POST['bill_cdr']) ? $_POST['bill_cdr'] * $multiplier : 0);
        $bill_quota = 0;
      }
    }
  }

  $insert = array('bill_name' => $_POST['bill_name'], 'bill_type' => $_POST['bill_type'], 'bill_cdr' => $bill_cdr, 'bill_day' => $_POST['bill_day'], 'bill_quota' => $bill_quota,
                  'bill_custid' => $_POST['bill_custid'], 'bill_ref' => $_POST['bill_ref'], 'bill_notes' => $_POST['bill_notes']);

  $bill_id = dbInsert($insert, 'bills');

  $message .= $message_break . "Bill ".mres($_POST['bill_name'])." (".$bill_id.") added!";
  $message_break .= "<br />";

  if (is_numeric($bill_id) && is_numeric($_POST['port']))
  {
    dbInsert(array('bill_id' => $bill_id, 'port_id' => $_POST['port']), 'bill_ports');
    $message .= $message_break . "Port ".mres($_POST['port'])." added!";
    $message_break .= "<br />";
  }
}

$pagetitle[] = "Billing";

switch($vars["view"]) {
  case "history":
    echo("<meta http-equiv=\"refresh\" content=\"360\">\n");
    echo("<h2 style=\"margin-bottom: 10px;\">Customer billing: Previous period</h2>\n");
    include("pages/bills/search.inc.php");
    include("pages/bills/pmonth.inc.php");
    break;
  case "add":
    echo("<h2 style=\"margin-bottom: 10px;\">Customer billing: Add bill</h2>\n");
    include("pages/bill/navbar.inc.php");
    include("pages/bills/add.inc.php");
    break;
  default:
    echo("<meta http-equiv=\"refresh\" content=\"360\">\n");
    echo("<h2 style=\"margin-bottom: 10px;\">Customer billing: Current period</h2>\n");
    include("pages/bills/search.inc.php");
    include("pages/bills/cmonth.inc.php");
}

echo("<script src=\"".$config['base_url']."js/bootstrap-tooltip.js\"></script>\n");
echo("<script src=\"".$config['base_url']."js/bootstrap-tab.js\"></script>\n");
echo("<script src=\"".$config['base_url']."js/billing.js\"></script>\n");

?>
