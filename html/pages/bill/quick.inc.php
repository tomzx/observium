<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    observium
 * @subpackage webui
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */

// Print tabs and graphs for quick billing view


switch($vars['tab'])
{

  case "24hour":
    $active['24hour']  = "active";
    $graph  = "<img src='graph.php?type=bill_bits&amp;id=" . $bill_id;
    $graph .= "&amp;from=" . $config['time']['day'] .  "&amp;to=" . $config['time']['now'];
    $graph .= "&amp;width=1050&amp;height=300&amp;total=1'>";
    break;

  case "monthly":
    $active['monthly']  = "active";
    $graph  = "<img src='graph.php?type=bill_bits&amp;id=" . $bill_id;
    $graph   .= "&amp;from=" . $lastmonth_unix .  "&amp;to=" . $rightnow_unix;
    $graph   .= "&amp;width=1050&amp;height=300&amp;total=1'>";
    break;

  case "previous":
    $active['previous']  = "active";
    $graph    = "<img src='graph.php?type=bill_bits&amp;id=" . $bill_id;
    $graph   .= "&amp;from=" . $unix_prev_from .  "&amp;to=" . $unix_prev_to;
    $graph   .= "&amp;width=1050&amp;height=300&amp;total=1'>";
    break;

  case "billing":
  default:
    $active['billing']  = "active";
    $graph    = "<img src='graph.php?type=bill_bits&amp;id=" . $bill_id;
    $graph   .= "&amp;from=" . $unixfrom .  "&amp;to=" . $unixto;
    $graph   .= "&amp;width=1050&amp;height=300&amp;total=1'>";
    break;
}

$links['billing']   = generate_url(array('page' => 'bill', 'bill_id' => $bill_id, 'view' => 'quick', 'tab' => 'billing'));
$links['24hour']    = generate_url(array('page' => 'bill', 'bill_id' => $bill_id, 'view' => 'quick', 'tab' => '24hour'));
$links['monthly']   = generate_url(array('page' => 'bill', 'bill_id' => $bill_id, 'view' => 'quick', 'tab' => 'monthly'));
$links['previous']  = generate_url(array('page' => 'bill', 'bill_id' => $bill_id, 'view' => 'quick', 'tab' => 'previous'));

?>

<div class="tabBox">
  <ul class="nav nav-tabs" id="quickBillTab">
    <li class="<?php echo($active['billing']); ?> first"><a href="<?php echo($links['billing']); ?>">Billing view</a></li>
    <li class="<?php echo($active['24hour']); ?>"><a href="<?php echo($links['24hour']); ?>">24 Hour view</a></li>
    <li class="<?php echo($active['monthly']); ?>"><a href="<?php echo($links['monthly']); ?>">Monthly view</a></li>
    <li class="<?php echo($active['previous']); ?>"><a href="<?php echo($links['previous']); ?>">Previous billing view</a></li>
  </ul>
  <div class="tabcontent tab-content" id="transferBillTabContent" style="min-height: 300px;">
    <div class="tab-pane active" id="quickGraph" style="text-align: center;">
      <?php echo($graph."\n"); ?>
    </div>
  </div>
</div>
