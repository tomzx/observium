<?php

/////////////////////////////////////////////////////////
//  NO CHANGES TO THIS FILE, IT IS NOT USER-EDITABLE   //
/////////////////////////////////////////////////////////
//               YES, THAT MEANS YOU                   //
/////////////////////////////////////////////////////////

// Definitions related to various entities known by Observium

#$config['entities']['device']['entity_id_field']        = 'device_id';
#$config['entities']['device']['entity_descr_field']     = "hostname";
#$config['entities']['device']['entity_table']           = "devices";
#$config['entities']['device']['icon']                   = "oicon-servers";
#$config['entities']['device']['graph']                  = array('type' => 'mempool_usage', 'id' => '@mempool_id');

$config['entities']['mempool']['entity_id_field']        = 'mempool_id';
$config['entities']['mempool']['entity_descr_field']     = "mempool_descr";
$config['entities']['mempool']['entity_table']           = "mempools";
$config['entities']['mempool']['icon']                   = "oicon-memory";
$config['entities']['mempool']['graph']                  = array('type' => 'mempool_usage', 'id' => '@mempool_id');

$config['entities']['processor']['entity_id_field']        = 'processor_id';
$config['entities']['processor']['entity_descr_field']     = "processor_descr";
$config['entities']['processor']['entity_table']           = "processors";
$config['entities']['processor']['icon']                   = "oicon-processor";
$config['entities']['processor']['graph']                  = array('type' => 'processor_usage', 'id' => '@processor_id');

$config['entities']['sensor']['entity_id_field']         = "sensor_id";
$config['entities']['sensor']['entity_descr_field']      = "sensor_descr";
$config['entities']['sensor']['entity_table']            = "sensors";
$config['entities']['sensor']['entity_ignore_field']     = "sensor_ignore";
$config['entities']['sensor']['entity_disable_field']    = "sensor_disable";
$config['entities']['sensor']['icon']                    = "oicon-contrast";
$config['entities']['sensor']['graph']                   = array('type' => 'sensor_graph', 'id' => '@sensor_id');

$config['entities']['bgp_peer']['entity_id_field']       = "bgpPeer_id";
$config['entities']['bgp_peer']['entity_descr_field']    = "bgpPeerRemoteAddr";
$config['entities']['bgp_peer']['entity_table']          = "bgpPeers";
$config['entities']['bgp_peer']['icon']                  = "oicon-chain";

$config['entities']['netscaler_vsvr']['entity_id_field']      = "vsvr_id";
$config['entities']['netscaler_vsvr']['entity_descr_field']   = "vsvr_label";
$config['entities']['netscaler_vsvr']['entity_table']         = "netscaler_vservers";
$config['entities']['netscaler_vsvr']['entity_ignore_field']  = "vsvr_ignore";
$config['entities']['netscaler_vsvr']['icon']                 = "oicon-server";

$config['entities']['netscaler_svc']['entity_id_field']     = "svc_id";
$config['entities']['netscaler_svc']['entity_descr_field']  = "svc_label";
$config['entities']['netscaler_svc']['entity_table']        = "netscaler_services";
$config['entities']['netscaler_svc']['entity_ignore_field'] = "svc_ignore";
$config['entities']['netscaler_svc']['icon']                = "oicon-service-bell";

$config['entities']['port']['entity_id_field']           = "port_id";
$config['entities']['port']['entity_descr_field']        = "ifDescr";
$config['entities']['port']['entity_table']              = "ports";
$config['entities']['port']['entity_ignore_field']       = "ignore";
$config['entities']['port']['entity_disable_field']      = "disable";
$config['entities']['port']['icon']                      = "oicon-network-ethernet";
$config['entities']['port']['graph']                     = array('type' => 'port_bits', 'id' => '@port_id');

$config['entity_default']['icon']                      = "oicon-circle-metal";

// ksort($config['entities']);

// EOF
