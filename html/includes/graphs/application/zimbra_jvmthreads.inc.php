<?php

include('includes/graphs/common.inc.php');

$rrd_filename = $config["rrd_dir"] . '/' . $device["hostname"] . '/app-zimbra-threads.rrd';

$array = array(
               'ImapSSLServer' => array('descr' => 'IMAP SSL Server'),
               'ImapServer' => array('descr' => 'IMAP Server'),
               'LmtpServer' => array('descr' => 'LMTP Server'),
               'Pop3SSLServer' => array('descr' => 'POP3 SSL Server'),
               'Pop3Server' => array('descr' => 'POP3 Server'),
               'GC' => array('descr' => 'Garbage Collection'),
               'AnonymousIoService' => array('descr' => 'Anonymous I/O Service'),
               'CloudRoutingReader' => array('descr' => 'Cloud Routing Reader'),
               'ScheduledTask' => array('descr' => 'Scheduled Task'),
               'SocketAcceptor' => array('descr' => 'Socket Acceptor'),
               'Thread' => array('descr' => 'Thread'),
               'Timer' => array('descr' => 'Timer'),
               'btpool' => array('descr' => 'BT Pool'),
               'pool' => array('descr' => 'Pool'),
               'other' => array('descr' => 'Other'),
              );

$nototal = 1;
$colours = "mixed";
$unit_text = "Threads";

$i = 0;

if (is_file($rrd_filename))
{
  foreach ($array as $ds => $vars)
  {
    $rrd_list[$i]['filename'] = $rrd_filename;
    $rrd_list[$i]['descr'] = $vars['descr'];
    $rrd_list[$i]['ds'] = $ds;
    $rrd_list[$i]['colour'] = ($vars['colour'] ? $vars['colour'] : $config['graph_colours'][$colours][$i]);
    $i++;
  }
} else { echo("file missing: $file");  }

include("includes/graphs/generic_multi_simplex_seperated.inc.php");

unset($rrd_list);

$noheader = 1;

$array = array(
               'total' => array('descr' => 'Total', 'colour' => '000000'), 
              );

if (is_file($rrd_filename))
{
  foreach ($array as $ds => $vars)
  {
    $rrd_list[$i]['filename'] = $rrd_filename;
    $rrd_list[$i]['descr'] = $vars['descr'];
    $rrd_list[$i]['ds'] = $ds;
    $rrd_list[$i]['colour'] = ($vars['colour'] ? $vars['colour'] : $config['graph_colours'][$colours][$i]);
    $i++;
  }
} else { echo("file missing: $file"); }

include("includes/graphs/generic_multi_line.inc.php");

?>
