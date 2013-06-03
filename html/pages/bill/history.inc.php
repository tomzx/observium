<?php

  $pagetitle[] = "Historical Usage";
  $i           = 0;

  $img['his']  = "<img src=\"bandwidth-graph.php?bill_id=".$bill_id;
  $img['his'] .= "&amp;type=historical";
  $img['his'] .= "&amp;x=1050&amp;y=250";
  $img['his'] .= "\" style=\"margin: 15px 5px 25px 5px;\" />";

?>

 <div class="well info_box">
  <div id="title"><i class="oicon-information"></i> Historical Usage Overview</div>
  <div id="content">

      <table class="table table-striped table-bordered">
        <tr>
          <td style="background: #fff; text-align: center;">
  <?php echo($img['his']); ?>
          </td>
        </tr>
      </table>

<?php
  function showDetails($bill_id, $imgtype, $from, $to, $bittype = "Quota")
  {
    if ($imgtype == "bitrate") {
      $res  = "<img src=\"billing-graph.php?bill_id=".$bill_id;
      if ($bittype == "Quota")
      {
        $res .= "&amp;ave=yes";
      }
      elseif ($bittype == "CDR") {
        $res .= "&amp;95th=yes";
      }
    } else
    {
      $res  = "<img src=\"bandwidth-graph.php?bill_id=".$bill_id;
    }
    //$res .= "&amp;type=".$type;
    $res .= "&amp;type=".$imgtype;
    $res .= "&amp;x=1050&amp;y=250";
    $res .= "&amp;from=".$from."&amp;to=".$to;
    $res .= "\" style=\"margin: 15px 0px 25px 0px;\" />";
    return $res;
  }

  echo("
             <table class=\"table table-striped table-bordered table-hover table-condensed table-rounded\">
               <thead>
                 <tr style=\"font-weight: bold; \">
                   <th width=\"250\">Period</th>
                   <th>Type</th>
                   <th>Allowed</th>
                   <th>Inbound</th>
                   <th>Outbound</th>
                   <th>Total</th>
                   <th>95th %ile</th>
                   <th style=\"text-align: center;\">Overusage</th>
                   <th colspan=\"2\" style=\"text-align: right;\"><a class=\"btn btn-mini\" style=\"color: #555;\" href=\"".generate_url($vars, array('detail' => "all"))."\"><img src=\"images/16/chart_curve.png\" alt=\"Show details\" title=\"Show details\" /> Show all details</a></th>
                 </tr>
               </thead>
               <tbody>");

  foreach (dbFetchRows("SELECT * FROM `bill_history` WHERE `bill_id` = ? ORDER BY `bill_datefrom` DESC LIMIT 24", array($bill_id)) as $history)
  {
    if (bill_permitted($history['bill_id']))
    {
      unset($class);
      $datefrom       = $history['bill_datefrom'];
      $dateto         = $history['bill_dateto'];
      $type           = $history['bill_type'];
      $percent        = $history['bill_percent'];
      $dir_95th       = $history['dir_95th'];
      $rate_95th      = formatRates($history['rate_95th']);
      $total_data     = format_number($history['traf_total'], $config['billing']['base']);

      if ($type == "CDR")
      {
         $allowed = formatRates($history['bill_allowed']);
         $used    = formatRates($history['rate_95th']);
         $in      = formatRates($history['rate_95th_in']);
         $out     = formatRates($history['rate_95th_out']);
         $overuse = (($history['bill_overuse'] <= 0) ? "<span class=\"badge badge-success\">-</span>" : "<span class=\"badge badge-important\">".formatRates($history['bill_overuse'])."</span>");
         $label   = "inverse";
      } elseif ($type == "Quota") {
         $allowed = format_number($history['bill_allowed'], $config['billing']['base']);
         $used    = format_number($history['total_data'], $config['billing']['base']);
         $in      = format_number($history['traf_in'], $config['billing']['base']);
         $out     = format_number($history['traf_out'], $config['billing']['base']);
         $overuse = (($history['bill_overuse'] <= 0) ? "<span class=\"badge badge-success\">-</span>" : "<span class=\"badge badge-imprtant\">".format_number($history['bill_overuse'], $config['billing']['base'])."B</span>");
         $label   = "info";
      }

      $total_data     = (($type == "Quota") ? "<span class=\"badge badge-warning\"><strong>".$total_data."</strong></span>" : "<span class=\"badge\">".$total_data."</span>");
      $rate_95th      = (($type == "CDR") ? "<span class=\"badge badge-warning\"><strong>".$rate_95th."</strong></span>" : "<span class=\"badge\">".$rate_95th."</span>");

      switch(true) {
        case ($percent >= 90):
          $perc['BG'] = "danger";
          break;
        case ($percent >= 75):
          $perc['BG'] = "warning";
          break;
        case ($percent >= 50):
          $perc['BG'] = "success";
          break;
        default:
          $perc['BG'] = "info";
      }
      $perc['width']  = (($percent <= "100") ? $percent : "100");
      $url            = generate_url($vars, array('detail' => $history['bill_hist_id']));

      $background = get_percentage_colours($percent);

      echo("
                <tr>
                  <td><i class=\"icon-calendar\"></i> <strong>".strftime("%F", strtotime($datefrom))." to ".strftime("%F", strtotime($dateto))."</strong></td>
                  <td><span class=\"label label-".$label."\">$type</span></td>
                  <td><span class=\"badge badge-success\">$allowed</span></td>
                  <td><span class=\"badge\">$in</span></td>
                  <td><span class=\"badge\">$out</span></td>
                  <td>$total_data</td>
                  <td>$rate_95th</td>
                  <td style=\"text-align: center;\">$overuse</td>
                  <td width=\"225\">
                    ".print_percentage_bar (400, 20, $percent, $percent.'%', "ffffff", $background['left'], 100-$percent."%" , "ffffff", $background['right'])."
                  </td>
                  <td>
                    <div class=\"btn-toolbar\" style=\"margin: 0px auto 0px auto;\">
                      <div class=\"btn-group\">
                        <a class=\"btn btn-mini\" href=\"".$url."\"><img src=\"images/16/chart_curve.png\" alt=\"Show details\" title=\"Show details\" rel=\"tooltip\"/></a>");
                        // Don't show things people can't use!
                        //<a class=\"btn btn-mini\" disabled=\"disabled\" href=\"javascript:;\"><img src=\"images/16/page_white_acrobat.png\" alt=\"PDF Report\" title=\"PDF Report\" rel=\"tooltip\"/></a>
                        //<a href=\"pdf.php?type=billing&report=history&bill_id=".$bill_id."&history_id=".$history['bill_hist_id']."\"><img src=\"images/16/page_white_acrobat.png\" border=\"0\" align=\"absmiddle\" alt=\"PDF Report\" title=\"PDF Report\"/></a>
      echo("
                      </div>
                    </div>
                  </td>
                </tr>");

      if ($vars['detail'] == $history['bill_hist_id'] || $vars['detail'] == "all") {
        $img['bitrate'] = showDetails($bill_id, "bitrate", strtotime($datefrom), strtotime($dateto), $type);
        $img['bw_day']  = showDetails($bill_id, "day", strtotime($datefrom), strtotime($dateto));
        $img['bw_hour'] = showDetails($bill_id, "hour", strtotime($datefrom), strtotime($dateto));
        echo("
                <tr>
                  <td colspan=\"10\" style=\"text-align: center; background-color: #ffffff;\">
                    <!-- <b>Accuate Graph</b><br /> //-->
                    ".$img['bitrate']."<br />
                    <!-- <b>Bandwidth Graph per day</b><br /> //-->
                    ".$img['bw_day']."<br />
                    <!-- <b>Bandwidth Graph per hour</b><br /> //-->
                    ".$img['bw_hour']."
                  </td>
                </tr>");
      }
    } // PERMITTED
  }
  echo("      </tobdy>");
  echo("    </table>");
  echo('  </div>');

?>
