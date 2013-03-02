<?php

//$isAdmin           = (($_SESSION['userlevel'] == "10") ? true : false);
//$isUser            = bill_permitted($bill['id']);
$isAdd             = (($vars['view'] == "add") ? true : false);
$disabledAdmin     = ($isAdmin ? "" : 'disabled="disabled"');
$disabledUser      = ($isUser ? "" : 'disabled="disabled"');
$links['quick']    = ($isUser ? generate_url(array('page' => 'bill', 'bill_id' => $bill_id, 'view' => 'quick')) : 'javascript:;');
$links['accurate'] = ($isUser ? generate_url(array('page' => 'bill', 'bill_id' => $bill_id, 'view' => 'accurate')) : 'javascript:;');
$links['transfer'] = ($isUser ? generate_url(array('page' => 'bill', 'bill_id' => $bill_id, 'view' => 'transfer')) : 'javascript:;');
$links['history']  = ($isUser ? generate_url(array('page' => 'bill', 'bill_id' => $bill_id, 'view' => 'history')) : 'javascript:;');
$links['api']      = ($isAdmin ? generate_url(array('page' => 'bill', 'bill_id' => $bill_id, 'view' => 'api')) : 'javascript:;');
$links['edit']     = ($isAdmin ? generate_url(array('page' => 'bill', 'bill_id' => $bill_id, 'view' => 'edit')) : 'javascript:;');
$links['delete']   = ($isAdmin ? generate_url(array('page' => 'bill', 'bill_id' => $bill_id, 'view' => 'delete')) : 'javascript:;');
$links['reset']    = ($isAdmin ? generate_url(array('page' => 'bill', 'bill_id' => $bill_id, 'view' => 'reset')) : 'javascript:;');
$links['bills']    = generate_url(array('page' => 'bills'));
$active['quick']   = (($vars['view'] == "quick") ? "active " : "");
$active['accurate']= (($vars['view'] == "accurate") ? "active " : "");
$active['transfer']= (($vars['view'] == "transfer") ? "active " : "");
$active['history'] = (($vars['view'] == "history") ? "active " : "");
$active['api']     = (($vars['view'] == "api") ? "active " : "");
$active['edit']    = (($vars['view'] == "edit") ? "active " : "");
$active['reset']   = (($vars['view'] == "reset") ? "active " : "");
$active['delete']  = (($vars['view'] == "delete") ? "active " : "");

$brand = 'Bill';
if (!$isAdd) {
  $brand .= ':'.$bill_data['bill_name'];
}

echo('<div class="navbar navbar-narrow">
        <div class="navbar-inner">
<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target="#bill-nav">
          <span class="icon-bar"></span>
        </button>
          <a class="brand">'.$brand.'</a>
          <div class="nav-collapse" id="bill-nav">
          <ul class="nav">');
if ($isUser && !$isAdd) {
  echo('
            <li class="'.$active['quick'].' first"><a href="'.$links['quick'].'">Quick Graphs</a></li>
            <li class="'.$active['accurate'].'"><a href="'.$links['accurate'].'">Accurate Graphs</a></li>
            <li class="'.$active['transfer'].'"><a href="'.$links['transfer'].'">Transfer Graphs</a></li>
            <li class="'.$active['history'].'"><a href="'.$links['history'].'">Historical Usage</a></li>');
}
if ($isAdmin && !$isAdd) {
  echo('
            <li class="spacer">&nbsp;</li>
            <li class="'.$active['edit'].' first"><a href="'.$links['edit'].'">Edit</a></li>
            <li class="'.$active['reset'].'"><a href="'.$links['reset'].'">Reset</a></li>
            <li class="'.$active['delete'].'"><a href="'.$links['delete'].'">Delete</a></li>');
// Don't show what doesn't work!
//            <li class="'.$active['api'].'"><a href="'.$links['api'].'"><i class="icon-share"></i> Api</a></li>');
}
echo('
          </ul>
          <ul class="nav pull-right">
            <li class="first"><a href="'.$links['bills'].'"><i class="icon-chevron-left"></i> <strong>Back</strong></a></li>
          </ul>
         </div>
        </div>
      </div>');

?>
