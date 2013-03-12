<?php

// APC
if ($device['os'] == "apc")
{
  # PDU - Phase
  $oids = snmp_walk($device, "rPDUStatusPhaseIndex", "-OsqnU", "PowerNet-MIB");
  if ($oids)
  {
    if ($debug) { echo($oids."\n"); }
    $oids = trim($oids);
    if ($oids) echo("APC PowerNet-MIB Phase ");
    $type = "apc";
    $precision = "10";
    foreach (explode("\n", $oids) as $data)
    {
      $data = trim($data);
      if ($data)
      {
        list($oid,$kind) = explode(" ", $data);
        $split_oid = explode('.',$oid);
        $index = $split_oid[count($split_oid)-1];

        $current_oid   = "1.3.6.1.4.1.318.1.1.12.2.3.1.1.2.".$index;        #rPDULoadStatusLoad
        $phase_oid     = "1.3.6.1.4.1.318.1.1.12.2.3.1.1.4.".$index;        #rPDULoadStatusPhaseNumber
        $limit_oid     = "1.3.6.1.4.1.318.1.1.12.2.2.1.1.4.".$index;        #rPDULoadPhaseConfigOverloadThreshold
        $lowlimit_oid  = "1.3.6.1.4.1.318.1.1.12.2.2.1.1.2.".$index;        #rPDULoadPhaseConfigLowLoadThreshold
        $warnlimit_oid = "1.3.6.1.4.1.318.1.1.12.2.2.1.1.3.".$index;        #rPDULoadPhaseConfigNearOverloadThreshold

        $phase     = snmp_get($device, $phase_oid, "-Oqv", "");
        $current   = snmp_get($device, $current_oid, "-Oqv", "") / $precision;
        $limit     = snmp_get($device, $limit_oid, "-Oqv", "");                        # No / $precision here! Nice, APC!
        $lowlimit  = snmp_get($device, $lowlimit_oid, "-Oqv", "");                # No / $precision here! Nice, APC!
        $warnlimit = snmp_get($device, $warnlimit_oid, "-Oqv", "");                # No / $precision here! Nice, APC!
        if (count(explode("\n",$oids)) != 1)
        {
          $descr     = "Phase $phase";
        }
        else
        {
          $descr     = "Output";
        }

        discover_sensor($valid['sensor'], 'current', $device, $current_oid, $index, $type, $descr, 10, 1, $lowlimit, NULL, $warnlimit, $limit, $current);
      }
    }
  }

  unset($oids);

  #v2 firmware- first bank is total, v3 firmware, 3rd bank is total
  $oids = snmp_walk($device, "rPDULoadBankConfigIndex", "-OsqnU", "PowerNet-MIB");        # should work with firmware v2 and v3
  if ($oids)
  {
    echo("APC PowerNet-MIB Banks ");
    if ($debug) { echo($oids."\n"); }
    $oids = trim($oids);
    $type = "apc";
    $precision = "10";

    # version 2 does some stuff differently- total power is first oid in index instead of the last.
    # will look something like "AOS v2.6.4 / App v2.6.5"
    $baseversion = "3";
    if (stristr($device['version'], 'AOS v2') == TRUE) { $baseversion = "2"; }

    foreach (explode("\n", $oids) as $data)
    {
      $data = trim($data);
      if ($data)
      {
        list($oid,$kind) = explode(" ", $data);
        $split_oid = explode('.',$oid);

        $index = $split_oid[count($split_oid)-1];

        $banknum = $index -1;
        $descr = "Bank ".$banknum;
        if ($baseversion == "3")
        {
          if ($index == "1") { $descr = "Bank Total"; }
        }
        if ($baseversion == "2")
        {
          if ($index == "1") { $descr = "Bank Total"; }
        }

        $current_oid   = "1.3.6.1.4.1.318.1.1.12.2.3.1.1.2.".$index;  #rPDULoadStatusLoad
        $bank_oid      = "1.3.6.1.4.1.318.1.1.12.2.3.1.1.5.".$index;  #rPDULoadStatusBankNumber
        $limit_oid     = "1.3.6.1.4.1.318.1.1.12.2.4.1.1.4.".$index;  #rPDULoadBankConfigOverloadThreshold
        $lowlimit_oid  = "1.3.6.1.4.1.318.1.1.12.2.4.1.1.2.".$index;  #rPDULoadBankConfigLowLoadThreshold
        $warnlimit_oid = "1.3.6.1.4.1.318.1.1.12.2.4.1.1.3.".$index;  #rPDULoadBankConfigNearOverloadThreshold

        $bank      = snmp_get($device, $bank_oid, "-Oqv", "");
        $current   = snmp_get($device, $current_oid, "-Oqv", "") / $precision;
        $limit     = snmp_get($device, $limit_oid, "-Oqv", "");
        $lowlimit  = snmp_get($device, $lowlimit_oid, "-Oqv", "");
        $warnlimit = snmp_get($device, $warnlimit_oid, "-Oqv", "");

        discover_sensor($valid['sensor'], 'current', $device, $current_oid, $index, $type, $descr, 10, 1, $lowlimit, NULL, $warnlimit, $limit, $current);
      }
    }

    unset($baseversion);
  }

  unset($oids);

 #Per Outlet Power Bar
  $oids = snmp_walk($device, "1.3.6.1.4.1.318.1.1.26.9.4.3.1.1", "-t 30 -OsqnU", "PowerNet-MIB");
  if ($oids)
  {
    echo("APC PowerNet-MIB Outlets ");
    if ($debug) { echo($oids."\n"); }
    $oids = trim($oids);
    $type = "apc";
    $precision = "10";

    foreach (explode("\n", $oids) as $data)
    {
      $data = trim($data);
      if ($data)
      {
        list($oid,$kind) = explode(" ", $data);
        $split_oid = explode('.',$oid);

        $index = $split_oid[count($split_oid)-1];

        $voltage_oid   = "1.3.6.1.4.1.318.1.1.26.6.3.1.6";            #rPDU2PhaseStatusVoltage

        $current_oid   = "1.3.6.1.4.1.318.1.1.26.9.4.3.1.6.".$index;  #rPDU2OutletMeteredStatusCurrent
        $limit_oid     = "1.3.6.1.4.1.318.1.1.26.9.4.1.1.7.".$index;  #rPDU2OutletMeteredConfigOverloadCurrentThreshold
        $lowlimit_oid  = "1.3.6.1.4.1.318.1.1.26.9.4.1.1.7.".$index;  #rPDU2OutletMeteredConfigLowLoadCurrentThreshold
        $warnlimit_oid = "1.3.6.1.4.1.318.1.1.26.9.4.1.1.6.".$index;  #rPDU2OutletMeteredConfigNearOverloadCurrentThreshold
        $name_oid      = "1.3.6.1.4.1.318.1.1.26.9.4.3.1.3.".$index;  #rPDU2OutletMeteredStatusName

        $voltage   = snmp_get($device, $voltage_oid, "-Oqv", "");

        $current   = snmp_get($device, $current_oid, "-Oqv", "") / $precision;
        $limit     = snmp_get($device, $limit_oid, "-Oqv", "") / $voltage;
        $lowlimit  = snmp_get($device, $lowlimit_oid, "-Oqv", "") / $voltage;
        $warnlimit = snmp_get($device, $warnlimit_oid, "-Oqv", "") / $voltage;
        $descr     = "Outlet " . $index . " - " .  snmp_get($device, $name_oid, "-Oqv", "");

        discover_sensor($valid['sensor'], 'current', $device, $current_oid, $index, $type, $descr, 10, 1, $lowlimit, NULL, $warnlimit, $limit, $current);
      }
    }
  }

  unset($oids);

  # ATS
  $oids = snmp_walk($device, "atsConfigPhaseTableIndex", "-OsqnU", "PowerNet-MIB");
  if ($oids)
  {
    $type = "apc";
    if ($debug) { print_r($oids); }
    $oids = trim($oids);
    if ($oids) echo("APC PowerNet-MIB ATS ");
    $current_oid   = "1.3.6.1.4.1.318.1.1.8.5.4.3.1.4.1.1.1";  #atsOutputCurrent
    $limit_oid     = "1.3.6.1.4.1.318.1.1.8.4.16.1.5.1";       #atsConfigPhaseOverLoadThreshold
    $lowlimit_oid  = "1.3.6.1.4.1.318.1.1.8.4.16.1.3.1";       #atsConfigPhaseLowLoadThreshold
    $warnlimit_oid = "1.3.6.1.4.1.318.1.1.8.4.16.1.4.1";       #atsConfigPhaseNearOverLoadThreshold
    $index         = 1;

    $current   = snmp_get($device, $current_oid, "-Oqv", "") / $precision;
    $limit     = snmp_get($device, $limit_oid, "-Oqv", "");     # No / $precision here! Nice, APC!
    $lowlimit  = snmp_get($device, $lowlimit_oid, "-Oqv", "");  # No / $precision here! Nice, APC!
    $warnlimit = snmp_get($device, $warnlimit_oid, "-Oqv", ""); # No / $precision here! Nice, APC!
    $descr     = "Output Feed";

    discover_sensor($valid['sensor'], 'current', $device, $current_oid, $index, $type, $descr, 10, 1, $lowlimit, NULL, $warnlimit, $limit, $current);
  }

  unset($oids);

  # UPS
  $inputs = snmp_get($device, "upsPhaseNumInputs.0", "-Ovq", "PowerNet-MIB");
  $outputs = snmp_get($device, "upsPhaseNumOutputs.0", "-Ovq", "PowerNet-MIB");

  if ($inputs || $outputs)
  {
    echo("APC PowerNet-MIB UPS");

    $cache['apc'] = snmpwalk_cache_multi_oid($device, "upsPhaseInputCurrent", $cache['apc'], "PowerNet-MIB");
    $cache['apc'] = snmpwalk_cache_multi_oid($device, "upsPhaseInputMinCurrent", $cache['apc'], "PowerNet-MIB");
    $cache['apc'] = snmpwalk_cache_multi_oid($device, "upsPhaseInputMaxCurrent", $cache['apc'], "PowerNet-MIB");

    $cache['apc'] = snmpwalk_cache_multi_oid($device, "upsPhaseOutputCurrent", $cache['apc'], "PowerNet-MIB");
    $cache['apc'] = snmpwalk_cache_multi_oid($device, "upsPhaseOutputMinCurrent", $cache['apc'], "PowerNet-MIB");
    $cache['apc'] = snmpwalk_cache_multi_oid($device, "upsPhaseOutputMaxCurrent", $cache['apc'], "PowerNet-MIB");

    echo(" In ");

    # Process each input, per phase
    for ($i = 1;$i <= $inputs;$i++)
    {
      # FIXME also cache_multi_oid ?
      $name = trim(snmp_get($device, "upsPhaseInputName.$i", "-Ovq", "PowerNet-MIB"),'"');
      $phases = snmp_get($device, "upsPhaseNumInputPhases.$i", "-Ovq","PowerNet-MIB");
      $tindex = snmp_get($device, "upsPhaseInputTableIndex.$i", "-Ovq", "PowerNet-MIB");

      for ($p = 1;$p <= $phases;$p++)
      {
        $type = "apc";

        $index     = "6.$tindex.1.$p";
        $current_oid = ".1.3.6.1.4.1.318.1.1.1.9.2.3.1.$index";

        $current   = $cache['apc']["$tindex.1.$p"]['upsPhaseInputCurrent'] / 10;
        $limit     = $cache['apc']["$tindex.1.$p"]['upsPhaseInputMaxCurrent'];
        $lowlimit  = $cache['apc']["$tindex.1.$p"]['upsPhaseInputMinCurrent'];

        $descr     = "$name Phase $p";

        if ($current != -0.1)
        {
          discover_sensor($valid['sensor'], 'current', $device, $current_oid, $index, $type, $descr, 10, 1, $lowlimit, NULL, NULL, $limit, $current);
        }
      }
    }

    echo(" Out ");

    # Process each output, per phase
    for ($o = 1;$o <= $outputs;$o++)
    {
      $name = "Output"; if ($outputs > 1) { $name .= " $o"; } # Output doesn't have a name in the MIB, add number if >1
      $phases = snmp_get($device, "upsPhaseNumOutputPhases.$o", "-Ovq","PowerNet-MIB");
      $tindex = snmp_get($device, "upsPhaseOutputTableIndex.$o", "-Ovq", "PowerNet-MIB");

      for ($p = 1;$p <= $phases;$p++)
      {
        $type = "apc";

        $index     = "4.$tindex.1.$p";
        $current_oid = ".1.3.6.1.4.1.318.1.1.1.9.3.3.1.$index";

        $current   = $cache['apc']["$tindex.1.$p"]['upsPhaseOutputCurrent'] / 10;
        $limit     = $cache['apc']["$tindex.1.$p"]['upsPhaseOutputMaxCurrent'];
        $lowlimit  = $cache['apc']["$tindex.1.$p"]['upsPhaseOutputMinCurrent'];

        $descr     = "$name Phase $p";

        if ($current != -0.1)
        {
          discover_sensor($valid['sensor'], 'current', $device, $current_oid, $index, $type, $descr, 10, 1, $lowlimit, NULL, NULL, $limit, $current);
        }
      }
    }
  }
  else
  {
    # Try other APC MIB parts

    ## UPS ##################################################################################################

    # Fetch high precision current (Precision 0.1)
    $oids = snmp_get($device, "upsHighPrecOutputCurrent.0", "-OsqnU", "PowerNet-MIB");
    if ($debug) { echo($oids."\n"); }
    if ($oids)
    {
      echo(" APC Out ");
      list($oid,$current) = explode(" ",$oids);
      $divisor = 10;
      $current /= $divisor;
      $type = "apc";
      $index = "4.3.4.0";
      $descr = "Output Current";

      discover_sensor($valid['sensor'], 'current', $device, $oid, $index, $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);
    }
    else
    {
      # If this is not available, fetch regular voltage (Precision 1)
      $oids = snmp_get($device, "upsAdvOutputCurrent.0", "-OsqnU", "PowerNet-MIB");
      if ($debug) { echo($oids."\n"); }
      if ($oids)
      {
        echo(" APC Out ");
        list($oid,$current) = explode(" ",$oids);
        $divisor = 1;
        $type = "apc";
        $index = "4.2.4.0";
        $descr = "Output Current";

        discover_sensor($valid['sensor'], 'current', $device, $oid, $index, $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);
      }
    }
  }
}

?>
