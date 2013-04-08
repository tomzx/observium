<div class="row">
<div class="span12">

<?php

unset($search, $devices);

//Search by field
$search[] = array('type'    => 'select',
                  'name'    => 'Search By',
                  'id'      => 'searchby',
                  'width'   => '120px',
                  'value'   => $vars['searchby'],
                  'values'  => array('mac' => 'MAC Address', 'ip' => 'IP Address'));
//IP version field
$search[] = array('type'    => 'select',
                  'name'    => 'IP',
                  'id'      => 'ip_version',
                  'width'   => '120px',
                  'value'   => $vars['ip_version'],
                  'values'  => array('' => 'IPv4 & IPv6', '4' => 'IPv4 only', '6' => 'IPv6 only'));
//Address field
$search[] = array('type'    => 'text',
                  'name'    => 'Address',
                  'id'      => 'address',
                  'value'   => $vars['address']);

print_search_simple($search, 'ARP/NDP Search');

// Pagination
$vars['pagination'] = TRUE;
if(!$vars['pagesize']) { $vars['pagesize'] = 100; }
if(!$vars['pageno']) { $vars['pageno'] = 1; }

print_arptable($vars);

?>

  </div> <!-- span12 -->

</div> <!-- row -->