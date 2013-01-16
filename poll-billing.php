#!/usr/bin/env php
<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    observium
 * @subpackage billing
 * @author     Adam Armstrong <adama@memetic.org>
 * @copyright  (C) 2006 - 2012 Adam Armstrong
 *
 */

chdir(dirname($argv[0]));

// FIXME - implement cli switches, debugging, etc.

include("includes/defaults.inc.php");
include("config.php");
include("includes/definitions.inc.php");
include("includes/functions.php");

$options = getopt("d");

if (isset($options['d']))
{
  echo("DEBUG!\n");
  $debug = TRUE;
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  ini_set('log_errors', 1);
#  ini_set('error_reporting', E_ALL ^ E_NOTICE);
} else {
  $debug = FALSE;
#  ini_set('display_errors', 0);
  ini_set('display_startup_errors', 0);
  ini_set('log_errors', 0);
#  ini_set('error_reporting', 0);
}


$iter = "0";

echo("Starting Polling Session ... \n\n");

foreach (dbFetchRows("SELECT * FROM `bills`") as $bill)
{
  echo("Bill : ".$bill['bill_name']."\n");
  # replace old bill_gb with bill_quota (we're now storing bytes, not gigabytes)
  if ($bill['bill_type'] == "quota" && !is_numeric($bill['bill_quota']))
  {
    $bill['bill_quota'] = $bill['bill_gb'] * $config['billing']['base'] * $config['billing']['base'];
    dbUpdate(array('bill_quota' => $bill['bill_quota']), 'bills', '`bill_id` = ?', array($bill['bill_id']));
    echo("Quota -> ".$bill['bill_quota']);
  }
  poll_bill($bill);
  $iter++;
}

function poll_bill($bill)
{

  $ports = dbFetchRows("SELECT * FROM `bill_ports` as P, `ports` as I, `devices` as D WHERE P.bill_id=? AND I.port_id = P.port_id AND D.device_id = I.device_id", array($bill['bill_id']));
  print_r($ports);
  foreach ($ports as $port)
  {
    echo("\nPolling ".$port['ifDescr']." on ".$port['hostname']."\n");
    $now = time();
    if(is_numeric($port['bill_port_polled'])) { $period = $now - $port['bill_port_polled']; }
    echo("time: ".$now."|".$port['bill_port_polled']." period:".$period."\n");

    if($port['snmpver'] == "1") {
      // SNMPv1 - Use non 64-bit counters
      $oids = "IF-MIB::ifInOctets.".$port['ifIndex']." IF-MIB::ifOutOctets.".$port['ifIndex'];
      $data = snmp_get_multi($port, $oids, "-OQUs", "IF-MIB");
      $data = $data[$port['ifIndex']];
      $data = array('in' => $data['ifInOctets'], 'out' => $data['ifOutOctets']);
    } else {
      // Not SNMPv1 - Use 64-bit counters
      $oids = "IF-MIB::ifHCInOctets.".$port['ifIndex']." IF-MIB::ifHCOutOctets.".$port['ifIndex'];
      $data = snmp_get_multi($port, $oids, "-OQUs", "IF-MIB");
      $data = $data[$port['ifIndex']];
      $data = array('in' => $data['ifHCInOctets'], 'out' => $data['ifHCOutOctets']);
    }

    if (is_numeric($data['in']) && is_numeric($data['out']))
    {
      echo($period ."|".$port['bill_port_counter_in']."|".$port['bill_port_counter_out']."|".$data['in']."|".$port['bill_port_counter_in']."\n");
      // The port returned counters
      if(is_numeric($period) && is_numeric($port['bill_port_counter_in']) && is_numeric($port['bill_port_counter_out']) && $data['in'] >= $port['bill_port_counter_in'] && $data['out'] >= $port['bill_port_counter_out'])
      {
        // Counters are higher or equal to before, seems legit.
        $in_delta  = $data['in']  - $port['bill_port_counter_in'];
        $out_delta = $data['out'] - $port['bill_port_counter_out'];
        echo("Counters valid, delta generated.\n");
      } elseif (is_numeric($period) && is_numeric($port['bill_port_counter_in']) && is_numeric($port['bill_port_counter_out'])) {
        // Counters are lower, we must have wrapped. We'll take the measurement as the amount for this period.
        $in_delta  = $data['in'];
        $out_delta = $data['out'];
        echo("Counters wrapped, delta fudged.\n");
      } else {
        // First update. delta is zero, only insert counters.
        echo("No existing counters.\n");
        $in_delta  = 0;
        $out_delta = 0;
      }

      if($in_delta == $data['in'] || $in_delta == $data['in'])
      {
        // Deltas are equal to counters. Clearly fail.
        echo("Deltas equal counters. Resetting.");
        $in_delta  = 0;
        $out_delta = 0;
      }

      $update = array('bill_port_polled' => $now, 'bill_port_period' => $period,
                      'bill_port_counter_in' => $data['in'], 'bill_port_counter_out' => $data['out'],
                      'bill_port_delta_in' => $in_delta, 'bill_port_delta_out' => $out_delta,
                      );
      dbUpdate($update, 'bill_ports', '`bill_id` = ? AND `port_id` = ?', array($port['bill_id'], $port['port_id']));

      echo("data:  in(N:".$data['in']."|P:".$port['bill_port_counter_in'].") delta:".$in_delta."\n");
      echo("      out(N:".$data['out']."|P:".$port['bill_port_counter_out'].") delta:".$out_delta."\n");

    } else {
      // No counters were returned
      // We don't need to update the database.
      echo("No data.\n");
    }

    dbInsert(array('port_id' => $port['port_id'], 'timestamp' => $now, 'counter' => $data['in'], 'delta' => $in_delta), 'bill_port_in_data');
    dbInsert(array('port_id' => $port['port_id'], 'timestamp' => $now, 'counter' => $data['out'], 'delta' => $out_delta), 'bill_port_out_data');

    $bill_delta     += $in_delta + $out_delta;
    $bill_in_delta  += $in_delta;
    $bill_out_delta += $out_delta;

    echo("deltas: i:".$bill_in_delta." o:".$bill_out_delta." t:".$bill_delta."\n");

  }

  echo("Bill processing...\n");
  $now = time();
  if(is_numeric($bill['bill_polled'])) { $period = $now - $bill['bill_polled']; }
  echo("time: ".$now."|".$bill['bill_polled']." period:".$period."\n");

  if ($period <= '0')
  {
    $bill_delta = '0';
    $bill_in_delta = '0';
    $bill_out_delta = '0';
  }

  dbUpdate(array('bill_polled' => $now), 'bills', '`bill_id` = ?', array($port['bill_id']));

  if ($period < "0" || !is_numeric($period)) {
    logfile("BILLING: negative period! id:".$bill['bill_id']." period:$period delta:$bill_delta in_delta:$bill_in_delta out_delta:$bill_out_delta");
  } else {
    dbInsert(array('bill_id' => $bill['bill_id'], 'timestamp' => array("NOW()"), 'period' => $period, 'delta' => $bill_delta, 'in_delta' => $bill_in_delta, 'out_delta' => $bill_out_delta), 'bill_data');
  }
}

if ($argv[1]) { poll_bill($argv[1]); }

?>
