<?php

//$isAdmin           = (($_SESSION['userlevel'] == "10") ? true : false);
//$isUser            = bill_permitted($bill['id']);
$isAdd             = (($vars['view'] == "add") ? true : false);
$disabledAdmin     = ($isAdmin ? "" : "disabled=\"disabled\"");
$disabledUser      = ($isUser ? "" : "disabled=\"disabled\"");
$links['quick']    = ($isUser ? generate_url(array('page' => 'bill', 'bill_id' => $bill_id, 'view' => 'quick')) : "javascript:;");
$links['accurate'] = ($isUser ? generate_url(array('page' => 'bill', 'bill_id' => $bill_id, 'view' => 'accurate')) : "javascript:;");
$links['transfer'] = ($isUser ? generate_url(array('page' => 'bill', 'bill_id' => $bill_id, 'view' => 'transfer')) : "javascript:;");
$links['history']  = ($isUser ? generate_url(array('page' => 'bill', 'bill_id' => $bill_id, 'view' => 'history')) : "javascript:;");
$links['api']      = ($isAdmin ? generate_url(array('page' => 'bill', 'bill_id' => $bill_id, 'view' => 'api')) : "javascript:;");
$links['edit']     = ($isAdmin ? generate_url(array('page' => 'bill', 'bill_id' => $bill_id, 'view' => 'edit')) : "javascript:;");
$links['delete']   = ($isAdmin ? generate_url(array('page' => 'bill', 'bill_id' => $bill_id, 'view' => 'delete')) : "javascript:;");
$links['reset']    = ($isAdmin ? generate_url(array('page' => 'bill', 'bill_id' => $bill_id, 'view' => 'reset')) : "javascript:;");
$links['bills']    = generate_url(array('page' => 'bills'));
$active['quick']   = (($vars['view'] == "quick") ? "active " : "");
$active['accurate']= (($vars['view'] == "accurate") ? "active " : "");
$active['transfer']= (($vars['view'] == "transfer") ? "active " : "");
$active['history'] = (($vars['view'] == "history") ? "active " : "");
$active['api']     = (($vars['view'] == "api") ? "active " : "");
$active['edit']    = (($vars['view'] == "edit") ? "active " : "");
$active['reset']   = (($vars['view'] == "reset") ? "active " : "");
$active['delete']  = (($vars['view'] == "delete") ? "active " : "");

if (!$isAdd) {
  echo("<h2 style=\"margin-bottom: 10px;\">Customer billing: ".$bill_data['bill_name']."</h2>");
}

echo("<div class=\"navbar\">
        <div class=\"navbar-inner\">
          <a class=\"brand\">Bill:</a>
          <ul class=\"nav\">");
if ($isUser && !$isAdd) {
  echo("
            <li class=\"".$active['quick']." first\"><a href=\"".$links['quick']."\"><i class=\"icon-list-alt\"></i> Quick Graphs</a></li>
            <li class=\"".$active['accurate']."\"><a href=\"".$links['accurate']."\"><i class=\"icon-signal\"></i> Accurate Graphs</a></li>
            <li class=\"".$active['transfer']."\"><a href=\"".$links['transfer']."\"><i class=\"icon-tasks\"></i> Transfer Graphs</a></li>
            <li class=\"".$active['history']."\"><a href=\"".$links['history']."\"><i class=\"icon-calendar\"></i> Historical Usage</a></li>");
}
if ($isAdmin && !$isAdd) {
  echo("
            <li class=\"spacer\">&nbsp;</li>
            <li class=\"".$active['edit']."first\"><a href=\"".$links['edit']."\"><i class=\"icon-edit\"></i> Edit</a></li>
            <li class=\"".$active['reset']."\"><a href=\"".$links['reset']."\"><i class=\"icon-refresh\"></i> Reset</a></li>
            <li class=\"".$active['delete']."\"><a href=\"".$links['delete']."\"><i class=\"icon-trash\"></i> Delete</a></li>
            <li class=\"".$active['api']."\"><a href=\"".$links['api']."\"><i class=\"icon-share\"></i> Api</a></li>");
}
echo("
          </ul>
          <ul class=\"nav pull-right\">
            <li class=\"first\"><a href=\"".$links['bills']."\"><i class=\"icon-chevron-left\"></i> <strong>Back to bills</strong></a></li>
          </ul>
        </div>
      </div>");

?>
