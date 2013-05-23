<?php

$links['billing']   = generate_url(array('page' => 'bill', 'bill_id' => $bill_id, 'view' => 'quick', 'tab' => 'billing'));
$links['24hour']    = generate_url(array('page' => 'bill', 'bill_id' => $bill_id, 'view' => 'quick', 'tab' => '24hour'));
$links['monthly']   = generate_url(array('page' => 'bill', 'bill_id' => $bill_id, 'view' => 'quick', 'tab' => 'monthly'));
$links['previous']  = generate_url(array('page' => 'bill', 'bill_id' => $bill_id, 'view' => 'quick', 'tab' => 'previous'));

$bi           = "<img src='graph.php?type=bill_bits&amp;id=" . $bill_id;
$bi          .= "&amp;from=" . $unixfrom .  "&amp;to=" . $unixto;
$bi          .= "&amp;width=1050&amp;height=300&amp;total=1'>";

$li           = "<img src='graph.php?type=bill_bits&amp;id=" . $bill_id;
$li          .= "&amp;from=" . $unix_prev_from .  "&amp;to=" . $unix_prev_to;
$li          .= "&amp;width=1050&amp;height=300&amp;total=1'>";

$di           = "<img src='graph.php?type=bill_bits&amp;id=" . $bill_id;
$di          .= "&amp;from=" . $config['time']['day'] .  "&amp;to=" . $config['time']['now'];
$di          .= "&amp;width=1050&amp;height=300&amp;total=1'>";

$mi           = "<img src='graph.php?type=bill_bits&amp;id=" . $bill_id;
$mi          .= "&amp;from=" . $lastmonth .  "&amp;to=" . $rightnow;
$mi          .= "&amp;width=1050&amp;height=300&amp;total=1'>";

if ($active['billing'] == "active") { $graph = $bi; }
elseif ($active['24hour'] == "active") { $graph = $di; }
elseif ($active['monthly'] == "active") { $graph = $mi; }
elseif ($active['previous'] == "active") { $graph = $li; }

?>

  <ul class="nav nav-tabs" id="quickBillTab">
    <li class="<?php echo($active['billing']); ?> first"><a href="<?php echo($links['billing']); ?>">Billing view</a></li>
    <li class="<?php echo($active['24hour']); ?>"><a href="<?php echo($links['24hour']); ?>">24 Hour view</a></li>
    <li class="<?php echo($active['monthly']); ?>"><a href="<?php echo($links['monthly']); ?>">Monthly view</a></li>
    <li class="<?php echo($active['previous']); ?>"><a href="<?php echo($links['previous']); ?>">Previous billing view</a></li>
  </ul>
  <div class="tabcontent tab-content" id="transferBillTabContent" style="min-height: 300px;">
    <div class="tab-pane fade active in" id="quickGraph" style="text-align: center;">
      <?php echo($graph."\n"); ?>
    </div>
  </div>

