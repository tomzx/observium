<?php

$cur_days     = date('d', ($config['time']['now'] - strtotime($datefrom)));
$total_days   = date('d', (strtotime($dateto)));

$used         = format_bytes_billing($total_data);
$average      = format_bytes_billing($total_data / $cur_days);
$estimated    = format_bytes_billing($total_data / $cur_days * $total_days);

if ($bill_data['bill_type'] == "quota") {
  $quota      = $bill_data['bill_quota'];
  $percent    = round(($total_data) / $quota * 100, 2);
  $allowed    = format_si($quota)."bps";
  $overuse    = $total_data - $quota;
  $overuse    = (($overuse <= 0) ? "<span class=\"badge badge-success\">-</span>" : "<span class=\"badge badge-important\">".format_bytes_billing($overuse)."</span>");
  $type       = "Quota";
} elseif ($bill_data['bill_type'] == "cdr") {
  $cdr        = $bill_data['bill_cdr'];
  $percent    = "0";
  $allowed    = "-";
  $overuse    = "<span class=\"badge badge-success\">-</span>";
  $type       = "CDR / 95th percentile";
}

$optional['cust']  = (($isAdmin && !empty($bill_data['bill_custid'])) ? $bill_data['bill_custid'] : "n/a");
$optional['ref']   = (($isAdmin && !empty($bill_data['bill_ref'])) ? $bill_data['bill_ref'] : "n/a");
$optional['notes'] = (!empty($bill_data['bill_notes']) ? $bill_data['bill_notes'] : "n/a");

$lastmonth    = dbFetchCell("SELECT UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 1 MONTH))");
$yesterday    = dbFetchCell("SELECT UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 1 DAY))");
$rightnow     = date(U);

$bi           = "<img src='bandwidth-graph.php?bill_id=" . $bill_id . "&amp;bill_code=" . $_GET['bill_code'];
$bi          .= "&amp;from=" . $unixfrom .  "&amp;to=" . $unixto;
$bi          .= "&amp;type=day&amp;imgbill=1";
$bi          .= "&amp;x=1050&amp;y=275";
$bi          .= "'>";

$li           = "<img src='bandwidth-graph.php?bill_id=" . $bill_id . "&amp;bill_code=" . $_GET['bill_code'];
$li          .= "&amp;from=" . $unix_prev_from .  "&amp;to=" . $unix_prev_to;
$li          .= "&amp;type=day";
$li          .= "&amp;x=1050&amp;y=275";
$li          .= "'>";

$di           = "<img src='bandwidth-graph.php?bill_id=" . $bill_id . "&amp;bill_code=" . $_GET['bill_code'];
$di          .= "&amp;from=" . $config['time']['day'] .  "&amp;to=" . $config['time']['now'];
$di          .= "&amp;type=hour";
$di          .= "&amp;x=1050&amp;y=275";
$di          .= "'>";

$mi           = "<img src='bandwidth-graph.php?bill_id=" . $bill_id . "&amp;bill_code=" . $_GET['bill_code'];
$mi          .= "&amp;from=" . $lastmonth .  "&amp;to=" . $rightnow;
$mi          .= "&amp;type=day";
$mi          .= "&amp;x=1050&amp;y=275";
$mi          .= "'>";

switch(true) {
  case($percent >= 90):
    $perc['BG'] = "danger";
    break;
  case($percent >= 75):
    $perc['BG'] = "warning";
    break;
  case($percent >= 50):
    $perc['BG'] = "success";
    break;
  default:
    $perc['BG'] = "info";
}
$perc['width'] = (($percent <= "100") ? $percent : "100");

// GB Convert (1000 vs 1024)
function gbConvert($data) {
  global $config;
  $count = strlen($data);
  $div   = floor($count / 4);
  $res   = round($data / pow(1000, $div) * pow($config['billing']['base'], $div));
  return $res;
}

function transferOverview($bill_id, $start, $end) {
  $tot       = array();
  $traf      = array();
  foreach (dbFetch("SELECT DISTINCT UNIX_TIMESTAMP(timestamp) as timestamp, SUM(delta) as traf_total, SUM(in_delta) as traf_in, SUM(out_delta) as traf_out FROM bill_data WHERE `bill_id`= ? AND `timestamp` >= FROM_UNIXTIME(?) AND `timestamp` <= FROM_UNIXTIME(?) GROUP BY DATE(timestamp) ORDER BY timestamp ASC", array($bill_id, $start, $end)) as $data) {
    $date        = strftime("%A, %e %B %Y", $data['timestamp']);
    $tot['in']  += gbConvert($data['traf_in']);
    $tot['out'] += gbConvert($data['traf_out']);
    $tot['tot'] += gbConvert($data['traf_total']);
    $traf['in']  = formatStorage(gbConvert($data['traf_in']), "3");
    $traf['out'] = formatStorage(gbConvert($data['traf_out']), "3");
    $traf['tot'] = formatStorage(gbConvert($data['traf_total']), "3");
    $traf['stot'] = formatStorage(gbConvert($tot['tot']), "3");
    $res    .= "        <tr>";
    $res    .= "          <td><i class=\"icon-calendar\"></i> ".$date."</td>";
    $res    .= "          <td style=\"text-align: center;\"><span class=\"badge badge-success\">".$traf['in']."</span></td>";
    $res    .= "          <td style=\"text-align: center;\"><span class=\"badge badge-info\">".$traf['out']."</span></td>";
    $res    .= "          <td style=\"text-align: center;\"><span class=\"badge badge-inverse\">".$traf['tot']."</span></td>";
    $res    .= "          <td style=\"text-align: center;\"><span class=\"badge badge\">".$traf['stot']."</span></td>";
    $res    .= "        </tr>";
  }
  $tot['in'] = formatStorage($tot['in']);
  $tot['out']= formatStorage($tot['out']);
  $tot['tot']= formatStorage($tot['tot']);
  $res      .= "        <tr>";
  $res      .= "          <td><strong>Total of this billing period</strong></td>";
  $res      .= "          <td style=\"text-align: center;\"><span class=\"badge badge-success\"><strong>".$tot['in']."</strong></span></td>";
  $res      .= "          <td style=\"text-align: center;\"><span class=\"badge badge-info\"><strong>".$tot['out']."</strong></span></td>";
  $res      .= "          <td style=\"text-align: center;\"><span class=\"badge badge-inverse\"><strong>".$tot['tot']."</strong></span></td>";
  $res      .= "          <td></td>";
  $res      .= "        </tr>";
  return $res;
}

$detail = transferOverview($bill_id, $unixfrom, $unixto);

?>

<div class="row-fluid">
  <div class="span6 well">
    <h3 class="bill"><i class="icon-tag"></i> Bill summary</h3>
    <table class="table table-striped table-bordered">
      <tr>
        <th style="width: 125px;">Billing period</th>
        <td style="width: 5px; border-left: none;">:</td>
        <td style="border-left: none;"><?php echo($fromtext." to ".$totext); ?></td>
      </tr>
      <tr>
        <th>Type</th>
        <td style="border-left: none;">:</td>
        <td style="border-left: none;"><span class="label label-inverse"><?php echo($type); ?></span></td>
      </tr>
      <tr>
        <th>Allowed</th>
        <td style="border-left: none;">:</td>
        <td style="border-left: none;"><span class="badge badge-success"><?php echo($allowed); ?></span></td>
      </tr>
      <tr>
        <th>Used</th>
        <td style="border-left: none;">:</td>
        <td style="border-left: none;"><span class="badge badge-warning"><?php echo($used); ?></span></td>
      </tr>
      <tr>
        <th>Average</th>
        <td style="border-left: none;">:</td>
        <td style="border-left: none;"><span class="badge"><?php echo($average); ?></span></td>
      </tr>
      <tr>
        <th>Estimated</th>
        <td style="border-left: none;">:</td>
        <td style="border-left: none;"><span class="badge badge-info"><?php echo($estimated); ?></span></td>
      </tr>
      <tr>
        <th>Overusage</th>
        <td style="border-left: none;">:</td>
        <td style="border-left: none;"><?php echo($overuse); ?></td>
      </tr>
      <tr>
        <td colspan="3">
          <div class="progress progress-<?php echo($perc['BG']); ?> progress-striped active" style="margin-bottom: 0px;"><div class="bar" style="text-align: middle; width:<?php echo($perc['width']); ?>%;"><?php echo($percent); ?>%</div></div>
        </td>
    </table>
  </div>
  <div class="span6 well">
    <h3 class="bill"><i class="icon-tags"></i> Optional information</h3>
    <table class="table table-striped table-bordered">
      <tr>
        <th style="width: 175px;"><i class="icon-user"></i> Customer Reference</th>
        <td style="width: 5px; border-left: none;">:</td>
        <td style="border-left: none;"><?php echo($optional['cust']); ?></td>
      </tr>
      <tr>
        <th><i class="icon-info-sign"></i> Billing Reference</th>
        <td style="border-left: none;">:</td>
        <td style="border-left: none;"><?php echo($optional['ref']); ?></td>
      </tr>
      <tr>
        <th><i class="icon-comment"></i> Notes</th>
        <td style="border-left: none;">:</td>
        <td style="border-left: none;"><?php echo($optional['notes']); ?></td>
      </tr>
    </table>
  </div>
</div>

<div class="tabBox">
  <ul class="nav-tabs tabs" id="transferBillTab">
    <li class="active"><a href="#BillingView" data-toggle="tab">Billing view</a></li>
    <li><a href="#24hourView" data-toggle="tab">Rolling 24 Hour view</a></li>
    <li><a href="#monthlyView" data-toggle="tab">Rolling Monthly view</a></li>
    <li><a href="#detailView" data-toggle="tab">Detailed billing view</a></li>
    <li><a href="#previousView" data-toggle="tab">Rolling Previous billing view</a></li>
  </ul>
  <div class="tabcontent tab-content" id="transferBillTabContent">

  <div class="tab-pane fade in active" id="billingView" style="text-align: center;">
    <?php echo($bi."\n"); ?>
  </div>
  <div class="tab-pane fade in" id="24hourView" style="text-align: center;">
    <?php echo($di."\n"); ?>
  </div>
  <div class="tab-pane fade in" id="monthlyView" style="text-align: center;">
    <?php echo($mi."\n"); ?>
  </div>
  <div class="tab-pane fade in" id="previousView" style="text-align: center;">
    <?php echo($li."\n"); ?>
  </div>
  <div class="tab-pane fade in" id="detailView" style="text-align: center;">
    <table class="table table-striped table-bordered" style="margin-bottom: 0px;">
      <thead>
        <tr>
          <th>Period</th>
          <th style="text-align: center;">Inbound</th>
          <th style="text-align: center;">Outbound</th>
          <th style="text-align: center;">Total</th>
          <th style="text-align: center;">Sub Total</th>
        </tr>
      </thead>
      <tbody>
        <?php echo($detail); ?>
      </tbody>
    </table>
  </div>

  </div>
</div>

