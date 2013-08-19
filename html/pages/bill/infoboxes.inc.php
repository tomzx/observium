<?php

$active['billing']  = (($vars['tab'] == "billing") ? "active" : "");
$active['24hour']   = (($vars['tab'] == "24hour") ? "active" : "");
$active['monthly']  = (($vars['tab'] == "monthly") ? "active" : "");
$active['previous'] = (($vars['tab'] == "previous") ? "active" : "");
$active['detail']   = (($vars['tab'] == "detail") ? "active" : "");

if (empty($active['billing']) && empty($active['24hour']) && empty($active['monthly']) && empty($active['detail']) && empty($active['previous'])) { $active['billing'] = "active"; }
$graph              = "";

$cur_days     = date('d', ($config['time']['now'] - strtotime($datefrom)));
$total_days   = date('d', (strtotime($dateto)));

//$used         = format_bytes_billing($total_data);
//$average      = format_bytes_billing($total_data / $cur_days);
//$estimated    = format_bytes_billing($total_data / $cur_days * $total_days);

if ($bill_data['bill_type'] == "quota") {
  $quota      = $bill_data['bill_quota'];
  $percent    = round(($total_data) / $quota * 100, 2);
  $used       = format_bytes_billing($total_data);
  $allowed    = format_si($quota)."B";
  $overuse    = $total_data - $quota;
  $overuse    = (($overuse <= 0) ? "<span class=\"badge badge-success\">-</span>" : "<span class=\"badge badge-important\">".format_bytes_billing($overuse)."</span>");
  $type       = "Quota";
  $imgtype    = "&amp;ave=yes";
  $current    = array(
                  'in' => format_bytes_billing($bill_data['total_data_in']),
                  'out' => format_bytes_billing($bill_data['total_data_out']),
                  'tot' => format_bytes_billing($bill_data['total_data'])
                );
  $average    = array(
                  'in' => format_bytes_billing($bill_data['total_data_in'] / $cur_days),
                  'out' => format_bytes_billing($bill_data['total_data_out'] / $cur_days),
                  'tot' => format_bytes_billing($bill_data['total_data'] / $cur_days)
                );
  $estimated  = array(
                  'in' => format_bytes_billing($bill_data['total_data_in'] / $cur_days * $total_days),
                  'out' => format_bytes_billing($bill_data['total_data_out'] / $cur_days * $total_days),
                  'tot' => format_bytes_billing($bill_data['total_data'] / $cur_days * $total_days)
                );
} elseif ($bill_data['bill_type'] == "cdr") {
  $cdr        = $bill_data['bill_cdr'];
  $percent    = round(($rate_95th) / $cdr * 100, 2);
  $used       = format_si($rate_95th)."bps";
  $allowed    = format_si($cdr)."bps";
  $overuse    = $rate_95th - $cdr;
  $overuse    = (($overuse <= 0) ? "<span class=\"badge badge-success\">-</span>" : "<span class=\"badge badge-important\">".format_si($overuse)."bps</span>");
  $type       = "CDR / 95th percentile";
  $imgtype    = "&amp;95th=yes";
  $current    = array(
                  'in' => format_si($bill_data['rate_95th_in'])."bps",
                  'out' => format_si($bill_data['rate_95th_out'])."bps",
                  'tot' => format_si($bill_data['rate_95th'])."bps"
                );
  $average    = array(
                  /* 'in' => format_si($bill_data['rate_average_in'])."bps", */
                  /* 'out' => format_si($bill_data['rate_average_out'])."bps", */
                  'in' => "n/a",
                  'out' => "n/a",
                  'tot' => format_si($bill_data['rate_average'])."bps"
                );
  $estimated  = array(
                  'in' => "n/a",
                  'out' => "n/a",
                  'tot' => "n/a"
                );
}

$optional['cust']  = (($isAdmin && !empty($bill_data['bill_custid'])) ? $bill_data['bill_custid'] : "n/a");
$optional['ref']   = (($isAdmin && !empty($bill_data['bill_ref'])) ? $bill_data['bill_ref'] : "n/a");
$optional['notes'] = (!empty($bill_data['bill_notes']) ? $bill_data['bill_notes'] : "n/a");
$optional['poll']  = (!empty($bill_data['bill_polled']) ? $bill_data['bill_polled'] : "n/a");
$optional['calc']  = (!empty($bill_data['bill_last_calc']) ? $bill_data['bill_last_calc'] : "n/a");


$lastmonth    = dbFetchCell("SELECT UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 1 MONTH))");
$yesterday    = dbFetchCell("SELECT UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 1 DAY))");
$rightnow     = date(U);

$bi           = "<img src='billing-graph.php?bill_id=" . $bill_id . "&amp;bill_code=" . $_GET['bill_code'];
$bi          .= "&amp;from=" . $unixfrom .  "&amp;to=" . $unixto;
$bi          .= "&amp;x=1050&amp;y=300";
$bi          .= "$imgtype'>";

$li           = "<img src='billing-graph.php?bill_id=" . $bill_id . "&amp;bill_code=" . $_GET['bill_code'];
$li          .= "&amp;from=" . $unix_prev_from .  "&amp;to=" . $unix_prev_to;
$li          .= "&amp;x=1050&amp;y=300";
$li          .= "$imgtype'>";

$di           = "<img src='billing-graph.php?bill_id=" . $bill_id . "&amp;bill_code=" . $_GET['bill_code'];
$di          .= "&amp;from=" . $config['time']['day'] .  "&amp;to=" . $config['time']['now'];
$di          .= "&amp;x=1050&amp;y=300";
$di          .= "$imgtype'>";

$mi           = "<img src='billing-graph.php?bill_id=" . $bill_id . "&amp;bill_code=" . $_GET['bill_code'];
$mi          .= "&amp;from=" . $lastmonth .  "&amp;to=" . $rightnow;
$mi          .= "&amp;x=1050&amp;y=300";
$mi          .= "$imgtype'>";

if ($active['billing'] == "active") { $graph = $bi; }
elseif ($active['24hour'] == "active") { $graph = $di; }
elseif ($active['monthly'] == "active") { $graph = $mi; }
elseif ($active['previous'] == "active") { $graph = $li; }

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

?>

<div class="row-fluid" style="margin-bottom: 15px;">
  <div class="span6">
    <div class="well info_box">
      <div class="title"><i class="oicon-information"></i> Bill Summary</div>
      <div class="content">
        <table class="table table-striped table-bordered table-condensed table-rounded">
          <tr>
            <th style="width: 125px;">Billing period</th>

            <td><?php echo($fromtext." to ".$totext); ?></td>
          </tr>
          <tr>
            <th>Type</th>

            <td><span class="label label-inverse"><?php echo($type); ?></span></td>
          </tr>
          <tr>
            <th>Allowed</th>

            <td><span class="badge badge-success"><?php echo($allowed); ?></span></td>
          </tr>
          <tr>
            <th>Used</th>

            <td><span class="badge badge-warning"><?php echo($used); ?></span></td>
          </tr>
          <tr>
            <th>Overusage</th>

            <td><?php echo($overuse); ?></td>
          </tr>
          <tr>
            <td colspan="3">
              <?php $background = get_percentage_colours($percent); ?>
              <?php echo(print_percentage_bar (400, 20, $percent, $percent.'%', "ffffff", $background['left'], 100-$percent."%" , "ffffff", $background['right']));  ?>
            </td>
          </tr>
        </table>
      </div>
    </div>
    <div class="well info_box">
      <div class="title"><i class="oicon-network-ethernet"></i> Usage Summary</div>
      <div class="content">
        <table class="table table-striped table-bordered table-condensed table-rounded">
          <thead>
            <tr>
              <th>Billing period</th>
              <th>Inbound</th>
              <th>Outbound</th>
              <th>Used</th>
            </tr>
          </thead>
          <tr>
<?php if ($bill_data['bill_type'] == "cdr") { ?>
            <th style="width: 125px;">95th percentile</th>
<?php } else { ?>
            <th style="width: 125px;">Total</th>
<?php } ?>

            <td><span class="badge badge-success"><?php echo($current['in']); ?></span></td>
            <td><span class="badge badge-info"><?php echo($current['out']); ?></span></td>
            <td><span class="badge"><?php echo($current['tot']); ?></span></td>
          </tr>
          <tr>
            <th>Average</th>

            <td><span class="badge badge-success"><?php echo($average['in']); ?></span></td>
            <td><span class="badge badge-info"><?php echo($average['out']); ?></span></td>
            <td><span class="badge"><?php echo($average['tot']); ?></span></td>
          </tr>
<?php if ($bill_data['bill_type'] == "quota") { ?>
          <tr>
            <th>Estimated</th>

            <td><span class="badge badge-success"><?php echo($estimated['in']); ?></span></td>
            <td><span class="badge badge-info"><?php echo($estimated['out']); ?></span></td>
            <td><span class="badge"><?php echo($estimated['tot']); ?></span></td>
          </tr>
<?php } ?>
        </table>
      </div>
    </div>
  </div>
  <div class="span6">
    <div class="well info_box">
      <div class="title"><i class="oicon-information-button"></i> Optional Information</div>
      <div class="content">
        <table class="table table-striped table-bordered table-condensed table-rounded">
          <tr>
            <th style="width: 175px;"><i class="icon-user"></i> Customer Reference</th>

            <td><?php echo($optional['cust']); ?></td>
          </tr>
          <tr>
            <th><i class="icon-info-sign"></i> Billing Reference</th>

            <td><?php echo($optional['ref']); ?></td>
          </tr>
          <tr>
            <th><i class="icon-comment"></i> Notes</th>

            <td><?php echo($optional['notes']); ?></td>
          </tr>
        </table>
      </div>
    </div>
    <div class="well info_box">
      <div class="title"><i class="oicon-clipboard-report-bar"></i> Polling Information</div>
      <div class="content">
        <table class="table table-striped table-bordered table-condensed table-rounded">
          <tr>
            <th style="width: 175px;"><i class="icon-time"></i> Last polled</th>

            <td><?php echo(strftime('%A, %e %B %Y @ %H:%M:%S', $optional['poll'])); ?></td>
          </tr>
          <tr>
            <th><i class="icon-time"></i> Last calculated</th>

            <td><?php echo(strftime('%A, %e %B %Y @ %H:%M:%S', time($optional['calc']))); ?></td>
          </tr>
        </table>
      </div>
    </div>
    <div class="well info_box">
      <div class="title"><i class="oicon-network-ethernet"></i> Ports Information</div>
      <div class="content">
        <table class="table table-striped table-bordered table-condensed table-rounded">
          <tr>
            <th style="width: 175px;"><i class="icon-random"></i> Number of ports</th>

            <td><?php echo($ports_info['ports']); ?></td>
          </tr>
          <tr>
            <th><i class="icon-random"></i> Total capacity</th>

            <td><?php echo(format_si($ports_info['capacity'])); ?>bps</td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
