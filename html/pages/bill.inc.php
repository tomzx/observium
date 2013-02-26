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


$bill_id    = mres($vars['bill_id']);
$isAdmin    = (($_SESSION['userlevel'] == "10") ? true : false);
$isUser     = bill_permitted($bill_id);

//if ($isAdmin && isset($_POST)) { include("pages/bill/actions.inc.php"); }
//if ($isAdmin && isset($_GET['delete_bill_port'])) { include("pages/bill/actions.inc.php"); }
include("pages/bill/actions.inc.php");

if ($isUser) {
  $bill_data    = dbFetchRow("SELECT * FROM bills WHERE bill_id = ?", array($bill_id));
  if ($vars['view'] == "quick" || $vars['view'] == "accurate" || $vars['view'] == "transfer" || $vars['view'] == "edit") {
#    $bill_data    = dbFetchRow("SELECT * FROM bills WHERE bill_id = ?", array($bill_id));
    $bill_name    = $bill_data['bill_name'];

    $today        = str_replace("-", "", dbFetchCell("SELECT CURDATE()"));
    $yesterday    = str_replace("-", "", dbFetchCell("SELECT DATE_SUB(CURDATE(), INTERVAL 1 DAY)"));
    $tomorrow     = str_replace("-", "", dbFetchCell("SELECT DATE_ADD(CURDATE(), INTERVAL 1 DAY)"));
    $last_month   = str_replace("-", "", dbFetchCell("SELECT DATE_SUB(CURDATE(), INTERVAL 1 MONTH)"));

    $rightnow     = $today . date(His);
    $before       = $yesterday . date(His);
    $lastmonth    = $last_month . date(His);

    $bill_name    = $bill_data['bill_name'];
    $dayofmonth   = $bill_data['bill_day'];

    $day_data     = getDates($dayofmonth);

    $datefrom     = $day_data['0'];
    $dateto       = $day_data['1'];
    $lastfrom     = $day_data['2'];
    $lastto       = $day_data['3'];

    $rate_95th    = $bill_data['rate_95th'];
    $dir_95th     = $bill_data['dir_95th'];
    $total_data   = $bill_data['total_data'];
    $rate_average = $bill_data['rate_average'];

    if ($rate_95th > $paid_kb) {
      $over       = $rate_95th - $paid_kb;
      $bill_text  = $over . "Kbit excess.";
      $bill_color = "#cc0000";
    } else {
      $under      = $paid_kb - $rate_95th;
      $bill_text  = $under . "Kbit headroom.";
      $bill_color = "#0000cc";
    }

    $fromtext     = dbFetchCell("SELECT DATE_FORMAT($datefrom, '%M %D %Y')");
    $totext       = dbFetchCell("SELECT DATE_FORMAT($dateto, '%M %D %Y')");
    $unixfrom     = dbFetchCell("SELECT UNIX_TIMESTAMP('$datefrom')");
    $unixto       = dbFetchCell("SELECT UNIX_TIMESTAMP('$dateto')");

    $unix_prev_from = dbFetchCell("SELECT UNIX_TIMESTAMP('$lastfrom')");
    $unix_prev_to   = dbFetchCell("SELECT UNIX_TIMESTAMP('$lastto')");
  }

  switch($vars['view']) {
    case "quick":
      include("pages/bill/navbar.inc.php");
      include("pages/bill/ports.inc.php");
      include("pages/bill/quick.inc.php");
      break;
    case "accurate":
      include("pages/bill/navbar.inc.php");
      include("pages/bill/ports.inc.php");
      include("pages/bill/accurate.inc.php");
      break;
    case "transfer":
      include("pages/bill/navbar.inc.php");
      include("pages/bill/ports.inc.php");
      include("pages/bill/transfer.inc.php");
      break;
    case "history":
      include("pages/bill/navbar.inc.php");
      include("pages/bill/ports.inc.php");
      include("pages/bill/history.inc.php");
      break;
    case "edit":
      include("pages/bill/navbar.inc.php");
      if ($isAdmin) { include("pages/bill/edit.inc.php"); } else { include("includes/error-no-perm.inc.php"); }
      break;
    case "reset":
      include("pages/bill/navbar.inc.php");
      if ($isAdmin) { include("pages/bill/reset.inc.php"); } else { include("includes/error-no-perm.inc.php"); }
      break;
    case "delete":
      include("pages/bill/navbar.inc.php");
      if ($isAdmin) { include("pages/bill/delete.inc.php"); } else { include("includes/error-no-perm.inc.php"); }
      break;
    case "api":
      include("pages/bill/navbar.inc.php");
      if ($isAdmin) { include("pages/bill/api.inc.php"); } else { include("includes/error-no-perm.inc.php"); }
      break;
    default:
      include("pages/bill/navbar.inc.php");
      include("includes/error-no-perm.inc.php");
  }
}

echo("<script src=\"".$config['base_url']."js/bootstrap-tooltip.js\"></script>\n");
echo("<script src=\"".$config['base_url']."js/bootstrap-tab.js\"></script>\n");
echo("<script src=\"".$config['base_url']."js/billing.js\"></script>\n");

?>
