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

// Build a list of user ids we can use to search for bills that user is allowed to see.

if ($isAdmin) {
  foreach (dbFetchRows("SELECT * FROM `bill_perms` GROUP BY `user_id` ORDER BY `user_id` ") as $customers) {
    if (bill_permitted($customers['bill_id'])) {
      $customer = dbFetchRow("SELECT * FROM `users` WHERE `user_id` = ? ORDER BY `user_id`", array($customers['user_id']));
      $name     = (empty($customer['realname']) ? $customer['username'] : $customer['realname']);
      $select   = (($_POST['billinguser'] == $customer['user_id']) ? " selected" : "");
      $users[$customer['user_id']] = $name;
    }
  }
} else {
  $users[$_SESSION['user_id']] = $_SESSION['username'];
}

// Billing name field
$search[] = array('type'    => 'text',
                  'name'    => 'Bill Name',
                  'id'      => 'billingname',
                  'value'   => $vars['billingname']);

// Billing type field
$search[] = array('type'    => 'select',
                  'name'    => 'All Types',
                  'id'      => 'billingtype',
                  'value'   => $vars['billingtype'],
                  'values'  => array('cdr' => 'CDR / 95th', 'quota' => 'Quota') );

//Billing user field
$search[] = array('type'    => 'select',
                  'name'    => 'User',
                  'id'      => 'billinguser',
                  'width'   => '130px',
                  'value'   => $vars['billinguser'],
                  'values'  => $users);

print_search_simple($search, 'Bill Search');

?>

