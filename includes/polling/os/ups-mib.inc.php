<?php

$version = trim(snmp_get($device, "upsIdentUPSSoftwareVersion.0", "-OQv", "UPS-MIB"),'"');

$manufacturer = trim(snmp_get($device, "upsIdentManufacturer.0", "-OQv", "UPS-MIB"),'"');
$model = trim(snmp_get($device, "upsIdentModel.0", "-OQv", "UPS-MIB"),'"');

$hardware = $manufacturer . ' ' . $model;

# Clean up
$hardware = str_replace("Liebert Corporation Liebert", "Liebert", $hardware);

## FIXME: Alerting on these OIDs!
#
# upsAlarm	GROUP	1.3.6.1.2.1.33.1.6
#   	upsAlarmsPresent	SCALAR	read-only	Gauge32	1.3.6.1.2.1.33.1.6.1.0
#   	upsAlarmTable	TABLE	not-accessible	SEQUENCE OF	1.3.6.1.2.1.33.1.6.2
#  	upsAlarmEntry	ENTRY	not-accessible	UpsAlarmEntry	1.3.6.1.2.1.33.1.6.2.1
# 	upsAlarmId	TABULAR	not-accessible	PositiveInteger	1.3.6.1.2.1.33.1.6.2.1.1
# 	upsAlarmDescr	TABULAR	read-only	AutonomousType	1.3.6.1.2.1.33.1.6.2.1.2
# 	upsAlarmTime	TABULAR	read-only	TimeStamp	1.3.6.1.2.1.33.1.6.2.1.3
# upsWellKnownAlarms	OBJ ID		 	1.3.6.1.2.1.33.1.6.3
#   	upsAlarmBatteryBad		OBJ IDENTITY			1.3.6.1.2.1.33.1.6.3.1
#   	upsAlarmOnBattery		OBJ IDENTITY			1.3.6.1.2.1.33.1.6.3.2
#   	upsAlarmLowBattery		OBJ IDENTITY			1.3.6.1.2.1.33.1.6.3.3
#   	upsAlarmDepletedBattery		OBJ IDENTITY			1.3.6.1.2.1.33.1.6.3.4
#   	upsAlarmTempBad			OBJ IDENTITY			1.3.6.1.2.1.33.1.6.3.5
#   	upsAlarmInputBad		OBJ IDENTITY			1.3.6.1.2.1.33.1.6.3.6
#   	upsAlarmOutputBad		OBJ IDENTITY			1.3.6.1.2.1.33.1.6.3.7
#   	upsAlarmOutputOverload		OBJ IDENTITY			1.3.6.1.2.1.33.1.6.3.8
#   	upsAlarmOnBypass		OBJ IDENTITY			1.3.6.1.2.1.33.1.6.3.9
#   	upsAlarmBypassBad		OBJ IDENTITY			1.3.6.1.2.1.33.1.6.3.10
#   	upsAlarmOutputOffAsRequested	OBJ IDENTITY			1.3.6.1.2.1.33.1.6.3.11
#   	upsAlarmUpsOffAsRequested	OBJ IDENTITY			1.3.6.1.2.1.33.1.6.3.12
#   	upsAlarmChargerFailed		OBJ IDENTITY			1.3.6.1.2.1.33.1.6.3.13
#   	upsAlarmUpsOutputOff		OBJ IDENTITY			1.3.6.1.2.1.33.1.6.3.14
#   	upsAlarmUpsSystemOff		OBJ IDENTITY			1.3.6.1.2.1.33.1.6.3.15
#   	upsAlarmFanFailure		OBJ IDENTITY			1.3.6.1.2.1.33.1.6.3.16
#   	upsAlarmFuseFailure		OBJ IDENTITY			1.3.6.1.2.1.33.1.6.3.17
#   	upsAlarmGeneralFault		OBJ IDENTITY			1.3.6.1.2.1.33.1.6.3.18
#   	upsAlarmDiagnosticTestFailed	OBJ IDENTITY			1.3.6.1.2.1.33.1.6.3.19
#   	upsAlarmCommunicationsLost	OBJ IDENTITY			1.3.6.1.2.1.33.1.6.3.20
#   	upsAlarmAwaitingPower		OBJ IDENTITY			1.3.6.1.2.1.33.1.6.3.21
#   	upsAlarmShutdownPending		OBJ IDENTITY			1.3.6.1.2.1.33.1.6.3.22
#   	upsAlarmShutdownImminent	OBJ IDENTITY			1.3.6.1.2.1.33.1.6.3.23
#   	upsAlarmTestInProgress		OBJ IDENTITY			1.3.6.1.2.1.33.1.6.3.24

// EOF