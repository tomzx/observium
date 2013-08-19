<?php

// EQLMEMBER-MIB

$eqlgrpmemid = get_dev_attrib($device, 'eqlgrpmemid');

if ($device['os'] == "equallogic" && (isset($eqlgrpmemid)))
{
  echo("EQLMEMBER-MIB ");
  $oids = snmpwalk_cache_oid($device, "eqlMemberHealthDetailsFanTable", array(), "EQLMEMBER-MIB", mib_dirs("equallogic") );

  // copy of eqlMemberHealthDetailsFanIndex
  $sensorname = array(
	"emm0fan0",
	"emm0fan1",
	"emm1fan0",
	"emm1fan1",
	"emm2fan0",
	"emm2fan1",
	"emm3fan0",
	"emm3fan1");
  $sensorid = array(1,2,3,4,5,6,7,8);

  if (is_array($oids))
  {
    if ($debug) { print_vars($oids); }
    foreach ($oids as $index => $entry)
    {
      # EQLMEMBER-MIB returns sensors for all members. only process sensors that match our member id
      if (strstr($index, $eqlgrpmemid))
      {
        $numindex = str_replace($sensorname, $sensorid, $index);
        $entry['oid'] = ".1.3.6.1.4.1.12740.2.1.7.1.3.".$numindex;
        if ($entry['eqlMemberHealthDetailsFanValue'] <> 0)
          {
          discover_sensor($valid['sensor'], 'fanspeed', $device, $entry['oid'], $numindex, 'equallogic',
            $entry['eqlMemberHealthDetailsFanName'], '1', '1',
            $entry['eqlMemberHealthDetailsFanLowCriticalThreshold'],
            $entry['eqlMemberHealthDetailsFanLowWarningThreshold'],
            $entry['eqlMemberHealthDetailsFanHighCriticalThreshold'],
            $entry['eqlMemberHealthDetailsFanHighWarningThreshold'],
            $entry['eqlMemberHealthDetailsFanValue']);
        }
      }
    }
  }
}

?>

