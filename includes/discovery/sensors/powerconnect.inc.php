<?php

if ($device['os'] == "powerconnect")
{
  // FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesTempSensorIndex.0 = INTEGER: 0
  // FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesTempSensorType.0 = INTEGER: fixed(1)
  // FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesTempSensorState.0 = INTEGER: normal(1)
  // FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesTempSensorTemperature.0 = INTEGER: 26

  $temps = snmp_walk($device, "boxServicesTempSensorTemperature", "-OsqnU", "FASTPATH-BOXSERVICES-PRIVATE-MIB");
  if ($debug) { echo($temps."\n"); }

  $index = 0;
  foreach (explode("\n",$temps) as $oids)
  {
    echo(" FASTPATH-BOXSERVICES-PRIVATE-MIB ");
    list($oid,$current) = explode(' ',$oids);
    $divisor = "1";
    $multiplier = "1";
    $type = "powerconnect";
    $index++;
    $descr = "Internal Temperature";
    if (count(explode("\n",$temps)) > 1) { $descr .= " $index"; }

//FIXME: Limits:
//FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesNormalTempRangeMin.0 = INTEGER: 0
//FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesNormalTempRangeMax.0 = INTEGER: 57

    discover_sensor($valid['sensor'], 'temperature', $device, $oid, $index, $type, $descr, $divisor, $multiplier, NULL, NULL, NULL, NULL, $current);
  }
}

/*

FIXME - boolean sensors to monitor:

FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesFansIndex.0 = INTEGER: 0
FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesFansIndex.1 = INTEGER: 1
FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesFansIndex.2 = INTEGER: 2
FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesFansIndex.3 = INTEGER: 3
FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesFansIndex.4 = INTEGER: 4
FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesFanItemState.0 = INTEGER: operational(2)
FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesFanItemState.1 = INTEGER: operational(2)
FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesFanItemState.2 = INTEGER: operational(2)
FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesFanItemState.3 = INTEGER: operational(2)
FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesFanItemState.4 = INTEGER: operational(2)
FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesFanSpeed.0 = INTEGER: 0
FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesFanSpeed.1 = INTEGER: 0
FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesFanSpeed.2 = INTEGER: 0
FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesFanSpeed.3 = INTEGER: 0
FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesFanSpeed.4 = INTEGER: 0
FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesFanDutyLevel.0 = INTEGER: 0
FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesFanDutyLevel.1 = INTEGER: 0
FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesFanDutyLevel.2 = INTEGER: 0
FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesFanDutyLevel.3 = INTEGER: 0
FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesFanDutyLevel.4 = INTEGER: 0

FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesPowSupplyIndex.0 = INTEGER: 0
FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesPowSupplyIndex.1 = INTEGER: 1
FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesPowSupplyItemType.0 = INTEGER: fixed(1)
FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesPowSupplyItemType.1 = INTEGER: removable(2)
FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesPowSupplyItemState.0 = INTEGER: operational(2)
FASTPATH-BOXSERVICES-PRIVATE-MIB::boxServicesPowSupplyItemState.1 = INTEGER: operational(2)

*/
