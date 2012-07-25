<?php

$pagetitle[]      = "Current Billing Period";
$isAdmin          = (($_SESSION['userlevel'] == "10") ? true : false);
$disabled         = ($isAdmin ? "" : "disabled=\"disabled\"");
$links['add']     = ($isAdmin ? generate_url($vars, array('view' => 'add')) : "javascript:;");
$links['prev']    = generate_url($vars, array('view' => 'history'));


echo("<table class=\"table table-bordered table-striped\">
        <thead>
          <tr>
            <th>Billing name</th>
            <th style=\"width: 60px; text-align: center;\">Type</th>
            <th style=\"width: 70px; text-align: center;\">Allowed</th>
            <th style=\"width: 70px; text-align: center;\">Used</th>
            <th style=\"width: 70px; text-align: center;\">Overusage</th>
            <th style=\"width: 225px;\"></th>
            <th style=\"width: 215px;\">
              <div class=\"btn-group\">
                <a class=\"btn btn-mini btn-primary\" style=\"color: #fff;\" href=\"".$links['prev']."\"><i class=\"icon-chevron-left icon-white\"></i> Previous period</a>
                <a class=\"btn btn-mini btn-success\" style=\"color: #fff;\" href=\"".$links['add']."\"".$disabled."\"><i class=\"icon-plus-sign icon-white\"></i> Add Bill</a>
              </div>
            </th>
          </tr>
        </thead>
        <tbody>\n");

foreach (dbFetchRows("SELECT * FROM `bills` ORDER BY `bill_name`") as $bill) {
  if (bill_permitted($bill['bill_id'])) {
    unset($class);
    $day_data     = getDates($bill['bill_day']);
    $datefrom     = $day_data['0'];
    $dateto       = $day_data['1'];
    $rate_data    = $bill;
    $rate_95th    = $rate_data['rate_95th'];
    $dir_95th     = $rate_data['dir_95th'];
    $total_data   = $rate_data['total_data'];
    $rate_average = $rate_data['rate_average'];
    $notes        = $bill['bill_notes'];
    $custid       = $bill['bill_custid'];
    $refid        = $bill['bill_ref'];
    $billid       = $bill['bill_id'];

    if ($bill['bill_type'] == "cdr") {
      $type = "CDR 95th";
      $allowed = format_si($bill['bill_cdr'])."bps";
      $used    = format_si($rate_data['rate_95th'])."bps";
      $percent = round(($rate_data['rate_95th'] / $bill['bill_cdr']) * 100,2);
      $background = get_percentage_colours($percent);
      $overuse = $rate_data['rate_95th'] - $bill['bill_cdr'];
      $overuse = (($overuse <= 0) ? "-" : format_si($overuse)."bps");
    } elseif ($bill['bill_type'] == "quota") {
      $type = "Quota";
      $allowed = format_bytes_billing($bill['bill_quota']);
      $used    = format_bytes_billing($rate_data['total_data']);
      $percent = round(($rate_data['total_data'] / ($bill['bill_quota'])) * 100,2);
      $background = get_percentage_colours($percent);
      $overuse = $rate_data['total_data'] - $bill['bill_quota'];
      $overuse = (($overuse <= 0) ? "-" : format_bytes_billing($overuse));
    }

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
    $perc['width']    = (($percent <= "100") ? $percent : "100");
    $label['type']    = (($type == "Quota") ? "info" : "inverse");
    $label['overuse'] = (($overuse == "-") ? "success" : "important");
    $notes            = (!empty($notes) ? "<br /><span class=\"label\"><i class=\"icon-comment icon-white\"></i> &nbsp;".$notes."&nbsp;</span>" : "");
    $ref['cust']      = (!empty($custid) ? "<a href=\"javascript:;\" rel=\"tooltip\" title=\"".$custid."\"><i class=\"icon-user\"></i></a>" : "");
    $ref['bill']      = (!empty($refid) ? "<a href=\"javascript:;\" rel=\"tooltip\" title=\"".$refid."\"><i class=\"icon-info-sign\"></i></a>" : "");
    $ref['html']      = (((!empty($ref['cust']) || !empty($ref['bill'])) && ($isAdmin == true)) ? "<span style=\"float: right;\">".$ref['cust']."".$ref['bill']."</span>" : "");
    $links['quick']   = generate_url(array('page' => 'bill', 'bill_id' => $billid, 'view' => 'quick'));
    $links['accurate']= generate_url(array('page' => 'bill', 'bill_id' => $billid, 'view' => 'accurate'));
    $links['transfer']= generate_url(array('page' => 'bill', 'bill_id' => $billid, 'view' => 'transfer'));
    $links['history'] = generate_url(array('page' => 'bill', 'bill_id' => $billid, 'view' => 'history'));
    $links['edit']    = ($isAdmin ? generate_url(array('page' => 'bill', 'bill_id' => $billid, 'view' => 'edit')) : "javascript:;");
    $links['reset']   = ($isAdmin ? generate_url(array('page' => 'bill', 'bill_id' => $billid, 'view' => 'reset')) : "javascript:;");
    $links['delete']  = ($isAdmin ? generate_url(array('page' => 'bill', 'bill_id' => $billid, 'view' => 'delete')) : "javascript:;");
    echo("
          <tr>
            <td>
              <i class=\"icon-hdd\"></i> <a href=\"".$links['quick']."\"><strong class=\"interface\">".$bill['bill_name']."</strong></a>".$ref['html']."<br />
              <i class=\"icon-calendar\"></i> ".strftime("%F", strtotime($datefrom))." to ".strftime("%F", strtotime($dateto))."
              ".$notes."
            </td>
            <td style=\"text-align: center;\"><span class=\"label label-".$label['type']."\">".$type."</span></td>
            <td style=\"text-align: center;\"><span class=\"badge badge-success\">".$allowed."</span></td>
            <td style=\"text-align: center;\"><span class=\"badge badge-warning\">".$used."</span></td>
            <td style=\"text-align: center;\"><span class=\"badge badge-".$label['overuse']."\">".$overuse."</span></td>
            <td><div class=\"progress progress-".$perc['BG']."  active\"><div class=\"bar\" style=\"text-align: middle; width: ".$perc['width']."%;\">".$percent."%</div></div></td>
            <td>
              <div class=\"btn-toolbar\" style=\"margin-top: 0px;\">
                <div class=\"btn-group\">
                  <a class=\"btn btn-mini btn-primary\" href=\"".$links['quick']."\" rel=\"tooltip\" title=\"Show quick graphs\"><i class=\"icon-list-alt icon-white\"></i></a>
                  <a class=\"btn btn-mini btn-primary\" href=\"".$links['accurate']."\" rel=\"tooltip\" title=\"Show accurate graphs\"><i class=\"icon-signal icon-white\"></i></a>
                  <a class=\"btn btn-mini btn-primary\" href=\"".$links['transfer']."\" rel=\"tooltip\" title=\"Show transfer graphs\"><i class=\"icon-tasks icon-white\"></i></a>
                  <a class=\"btn btn-mini btn-primary\" href=\"".$links['history']."\" rel=\"tooltip\" title=\"Show historical usage\"><i class=\"icon-calendar icon-white\"></i></a>
                </div>
                <div class=\"btn-group right\">
                  <a class=\"btn btn-mini btn-success\" href=\"".$links['edit']."\" rel=\"tooltip\" title=\"Edit bill\"".$disabled."><i class=\"icon-edit icon-white\"></i></a>
                  <a class=\"btn btn-mini btn-warning\" href=\"".$links['reset']."\" rel=\"tooltip\" title=\"Reset bill\"".$disabled."><i class=\"icon-refresh icon-white\"></i></a>
                  <a class=\"btn btn-mini btn-danger\" href=\"".$links['delete']."\" rel=\"tooltip\" title=\"Delete bill\"".$disabled."><i class=\"icon-trash icon-white\"></i></a>
                </div>
              </div>
            </td>
          </tr>\n");
  } // PERMITTED
}

echo("  </tbody>\n");
echo("</table>\n");

?>
