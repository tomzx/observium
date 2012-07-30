<?php

// APC Voltages
if ($device['os'] == "apc")
{
  # UPS
  $inputs = snmp_get($device, "upsPhaseNumInputs.0", "-Ovq", "PowerNet-MIB");
  $outputs = snmp_get($device, "upsPhaseNumOutputs.0", "-Ovq", "PowerNet-MIB");

  # Check if we have values for these, if not, try other code paths below.
  if ($inputs || $outputs)
  {
    echo("APC PowerNet-MIB UPS");

    $cache['apc'] = snmpwalk_cache_multi_oid($device, "upsPhaseInputVoltage", $cache['apc'], "PowerNet-MIB");
    $cache['apc'] = snmpwalk_cache_multi_oid($device, "upsPhaseInputMinVoltage", $cache['apc'], "PowerNet-MIB");
    $cache['apc'] = snmpwalk_cache_multi_oid($device, "upsPhaseInputMaxVoltage", $cache['apc'], "PowerNet-MIB");

    $cache['apc'] = snmpwalk_cache_multi_oid($device, "upsPhaseOutputVoltage", $cache['apc'], "PowerNet-MIB");
    $cache['apc'] = snmpwalk_cache_multi_oid($device, "upsPhaseOutputMinVoltage", $cache['apc'], "PowerNet-MIB");
    $cache['apc'] = snmpwalk_cache_multi_oid($device, "upsPhaseOutputMaxVoltage", $cache['apc'], "PowerNet-MIB");

    echo(" In ");

    # Process each input, per phase
    for ($i = 1;$i <= $inputs;$i++)
    {
      # FIXME also cache_multi_oid ?
      $name = trim(snmp_get($device, "upsPhaseInputName.$i", "-Ovq", "PowerNet-MIB"),'"');
      $phases = snmp_get($device, "upsPhaseNumInputPhases.$i", "-Ovq","PowerNet-MIB");
      $tindex = snmp_get($device, "upsPhaseInputTableIndex.$i", "-Ovq", "PowerNet-MIB");
      $itype = snmp_get($device, "upsPhaseInputType.$i", "-Ovq", "PowerNet-MIB");

      for ($p = 1;$p <= $phases;$p++)
      {
        $type = "apc";
        
        $index     = "2.3.1.3.$tindex.1.$p";
        $current_oid = ".1.3.6.1.4.1.318.1.1.1.9.$index";

        $current   = $cache['apc']["$tindex.1.$p"]['upsPhaseInputVoltage'];
        $limit     = $cache['apc']["$tindex.1.$p"]['upsPhaseInputMaxVoltage'];
        $lowlimit  = $cache['apc']["$tindex.1.$p"]['upsPhaseInputMinVoltage'];
        
        if ($itype == "bypass") { $name = "Bypass"; }
        
        $descr     = "$name Phase $p";

        if ($current != -1)
        {
          discover_sensor($valid['sensor'], 'voltage', $device, $current_oid, $index, $type, $descr, 1, 1, $lowlimit, NULL, NULL, $limit, $current);
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
        
        $index     = "3.3.1.3.$tindex.1.$p";
        $current_oid = ".1.3.6.1.4.1.318.1.1.1.9.$index";
        
        $current   = $cache['apc']["$tindex.1.$p"]['upsPhaseOutputVoltage'];
        $limit     = $cache['apc']["$tindex.1.$p"]['upsPhaseOutputMaxVoltage'];
        $lowlimit  = $cache['apc']["$tindex.1.$p"]['upsPhaseOutputMinVoltage'];
        
        $descr     = "$name Phase $p";

        if ($current != -1)
        {
          discover_sensor($valid['sensor'], 'voltage', $device, $current_oid, $index, $type, $descr, 1, 1, $lowlimit, NULL, NULL, $limit, $current);
        }
      }
    }
  }
  else
  {
    # Try other APC MIB parts
    
    ## ATS ##################################################################################################

    $oids = snmp_walk($device, "atsInputVoltage", "-OsqnU", "PowerNet-MIB");
    if ($debug) { echo($oids."\n"); }
    if ($oids) echo("APC In ");
    $divisor = 1;
    $type = "apc";
    foreach (explode("\n", $oids) as $data)
    {
      $data = trim($data);
      if ($data)
      {
        list($oid,$current) = explode(" ", $data,2);
        $split_oid = explode('.',$oid);
        $index = $split_oid[count($split_oid)-3];
        $oid  = "1.3.6.1.4.1.318.1.1.8.5.3.3.1.3." . $index . ".1.1";
        $descr = "Input Feed " . chr(64+$index);
  
        discover_sensor($valid['sensor'], 'voltage', $device, $oid, "3.3.1.3.$index", $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);
      }
    }
  
    $oids = snmp_walk($device, "atsOutputVoltage", "-OsqnU", "PowerNet-MIB");
    if ($debug) { echo($oids."\n"); }
    if ($oids) echo(" APC Out ");
    $divisor = 1;
    $type = "apc";
    foreach (explode("\n", $oids) as $data)
    {
      $data = trim($data);
      if ($data)
      {
        list($oid,$current) = explode(" ", $data,2);
        $split_oid = explode('.',$oid);
        $index = $split_oid[count($split_oid)-3];
        $oid  = "1.3.6.1.4.1.318.1.1.8.5.4.3.1.3." . $index . ".1.1";
        $descr = "Output Feed"; if (count(explode("\n", $oids)) > 1) { $descr .= " $index"; }
  
        discover_sensor($valid['sensor'], 'voltage', $device, $oid, "4.3.1.3.$index", $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);
      }
    }

    ## UPS ##################################################################################################
     
    # Fetch high precision voltage (Precision 0.1)
    $oids = snmp_get($device, "upsHighPrecInputLineVoltage.0", "-OsqnU", "PowerNet-MIB");
    if ($debug) { echo($oids."\n"); }
    if ($oids)
    {
      echo(" APC In ");
      list($oid,$current) = explode(" ",$oids);
      $divisor = 10;
      $current /= $divisor;
      $type = "apc";
      $index = "3.3.1.0";
      $descr = "Input";
    
      discover_sensor($valid['sensor'], 'voltage', $device, $oid, $index, $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);
    }
    else
    {
      # If this is not available, fetch regular voltage (Precision 1)
      $oids = snmp_get($device, "upsAdvInputLineVoltage.0", "-OsqnU", "PowerNet-MIB");
      if ($debug) { echo($oids."\n"); }
      if ($oids)
      {
        echo(" APC In ");
        list($oid,$current) = explode(" ",$oids);
        $divisor = 1;
        $type = "apc";
        $index = "3.2.1.0";
        $descr = "Input";
  
        discover_sensor($valid['sensor'], 'voltage', $device, $oid, $index, $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);
      }
    }
  
    # Fetch high precision voltage (Precision 0.1)
    $oids = snmp_get($device, "upsHighPrecOutputVoltage.0", "-OsqnU", "PowerNet-MIB");
    if ($debug) { echo($oids."\n"); }
    if ($oids)
    {
      echo(" APC Out ");
      list($oid,$current) = explode(" ",$oids);
      $divisor = 10;
      $current /= $divisor;
      $type = "apc";
      $index = "4.3.1.0";
      $descr = "Output";
    
      discover_sensor($valid['sensor'], 'voltage', $device, $oid, $index, $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);
    }
    else
    {
      # If this is not available, fetch regular voltage (Precision 1)
      $oids = snmp_get($device, "upsAdvOutputVoltage.0", "-OsqnU", "PowerNet-MIB");
      if ($debug) { echo($oids."\n"); }
      if ($oids)
      {
        echo(" APC Out ");
        list($oid,$current) = explode(" ",$oids);
        $divisor = 1;
        $type = "apc";
        $index = "4.2.1.0";
        $descr = "Output";
  
        discover_sensor($valid['sensor'], 'voltage', $device, $oid, $index, $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);
      }
    }
  
    ## PDU ##################################################################################################

    $oids = snmp_walk($device, "rPDUIdentDeviceLinetoLineVoltage.0", "-OsqnU", "PowerNet-MIB");
    if ($debug) { echo($oids."\n"); }
    if ($oids)
    {
      echo(" Voltage In ");
      list($oid,$current) = explode(" ",$oids);
      $divisor = 1;
      $type = "apc";
      $index = "1";
      $descr = "Input";
  
      discover_sensor($valid['sensor'], 'voltage', $device, $oid, $index, $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);
    }
  
    $oids = snmp_walk($device, "rPDU2PhaseStatusVoltage", "-OsqnU", "PowerNet-MIB");
    if ($debug) { echo($oids."\n"); }
    if ($oids)
    {
      echo(" Voltage In ");
      list($oid,$current) = explode(" ",$oids);
      $divisor = 1;
      $type = "apc";
      $index = "1";
      $descr = "Input";
  
      discover_sensor($valid['sensor'], 'voltage', $device, $oid, $index, $type, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current);
    }
  }
}
  
?>
