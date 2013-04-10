<?php

global $debug;

if ($config['enable_bgp'])
{
  $vendor_oids = array(// Juniper BGP4-V2 MIB
                       'junos' => array('vendor_mib'                => 'BGP4-V2-MIB-JUNIPER',
                                        'vendor_mib_dir'            => mib_dirs('junos'),
                                        'vendor_PeerTable'          => 'jnxBgpM2PeerTable',
                                        'vendor_PeerRemoteAs'       => 'jnxBgpM2PeerRemoteAs',
                                        'vendor_PeerRemoteAddr'     => 'jnxBgpM2PeerRemoteAddr',
                                        'vendor_PeerLocalAddr'      => 'jnxBgpM2PeerLocalAddr',
                                        'vendor_PeerIdentifier'     => 'jnxBgpM2PeerIdentifier',
                                        'vendor_PeerIndex'          => 'jnxBgpM2PeerIndex',
                                        'vendor_PeerRemoteAddrType' => 'jnxBgpM2PeerRemoteAddrType',
                                        'vendor_PrefixCountersSafi' => 'jnxBgpM2PrefixCountersSafi'),
                       'junose' => array('vendor_mib'               => 'BGP4-V2-MIB-JUNIPER',
                                        'vendor_mib_dir'            => mib_dirs('junose'),
                                        'vendor_PeerTable'          => 'jnxBgpM2PeerTable',
                                        'vendor_PeerRemoteAs'       => 'jnxBgpM2PeerRemoteAs',
                                        'vendor_PeerRemoteAddr'     => 'jnxBgpM2PeerRemoteAddr',
                                        'vendor_PeerLocalAddr'      => 'jnxBgpM2PeerLocalAddr',
                                        'vendor_PeerIdentifier'     => 'jnxBgpM2PeerIdentifier',
                                        'vendor_PeerIndex'          => 'jnxBgpM2PeerIndex',
                                        'vendor_PeerRemoteAddrType' => 'jnxBgpM2PeerRemoteAddrType',
                                        'vendor_PrefixCountersSafi' => 'jnxBgpM2PrefixCountersSafi'),
                       // Force10 BGP4-V2 MIB
                       'ftos'  => array('vendor_mib'                => 'FORCE10-BGP4-V2-MIB',
                                        'vendor_mib_dir'            => mib_dirs('force10'),
                                        'vendor_PeerTable'          => 'f10BgpM2PeerTable',
                                        'vendor_PeerRemoteAs'       => 'f10BgpM2PeerRemoteAs',
                                        'vendor_PeerRemoteAddr'     => 'f10BgpM2PeerRemoteAddr',
                                        'vendor_PeerLocalAddr'      => 'f10BgpM2PeerLocalAddr',
                                        'vendor_PeerIdentifier'     => 'f10BgpM2PeerIdentifier',
                                        'vendor_PeerIndex'          => 'f10BgpM2PeerIndex',
                                        'vendor_PeerRemoteAddrType' => 'f10BgpM2PeerRemoteAddrType',
                                        'vendor_PrefixCountersSafi' => 'f10BgpM2PrefixCountersSafi')
                       );
  if (isset($vendor_oids[$device['os']]))
  {
    foreach($vendor_oids[$device['os']] as $v => $val) { $$v = $val; }
    $use_vendor = TRUE;
  } else {
    $use_vendor = FALSE;
  }

  // Discover BGP peers

  /// NOTE. PeerIdentifier != PeerRemoteAddr
  
  echo("BGP Sessions: ");

  $bgpLocalAs = snmp_get($device, 'bgpLocalAs.0', '-Oqvn', 'BGP4-MIB', mib_dirs());
  if ($device['os'] == 'junos' && $bgpLocalAs == '0')
  {
    // On JunOS BGP4-MIB::bgpLocalAs.0 is always '0'.
    $j_bgpLocalAs = trim(snmp_walk($device, 'jnxBgpM2PeerLocalAs', '-Oqvn', 'BGP4-V2-MIB-JUNIPER', mib_dirs('junos')));
    list($bgpLocalAs) = explode("\n", $j_bgpLocalAs);
  }
  
  if (is_numeric($bgpLocalAs))
  {
    echo("AS$bgpLocalAs ");

    if ($bgpLocalAs != $device['bgpLocalAs'])
    {
      if (!$device['bgpLocalAs'])
      {
        log_event('BGP Local ASN added: AS' . $bgpLocalAs, $device, 'bgp');
      }
      elseif (!$bgpLocalAs)
      {
        log_event('BGP Local ASN removed: AS' . $device['bgpLocalAs'], $device, 'bgp');
      }
      else
      {
        log_event('BGP ASN changed: AS' . $device['bgpLocalAs'] . ' -> AS' . $bgpLocalAs, $device, 'bgp');
      }
      dbUpdate(array('bgpLocalAs' => $bgpLocalAs) , 'devices', 'device_id = ?', array($device['device_id']));
      echo('Updated ASN (from '.$device['bgpLocalAs']." -> $bgpLocalAs)\n");
    }

    $peers_data = snmpwalk_cache_oid($device, 'bgpPeerRemoteAs', array(), 'BGP4-MIB', mib_dirs());
    $peers_data = snmpwalk_cache_oid($device, 'bgpPeerRemoteAddr', $peers_data, 'BGP4-MIB', mib_dirs());
    $peers_data = snmpwalk_cache_oid($device, 'bgpPeerLocalAddr', $peers_data, 'BGP4-MIB', mib_dirs());
    $peers_data = snmpwalk_cache_oid($device, 'bgpPeerIdentifier', $peers_data, 'BGP4-MIB', mib_dirs());
    if ($debug) { echo("BGP4-MIB Peers: \n");}
    foreach ($peers_data as $peer)
    {
      $peer_as = $peer['bgpPeerRemoteAs'];
      $peer_ip = $peer['bgpPeerRemoteAddr'];
      $local_ip = $peer['bgpPeerLocalAddr'];
      if (!isset($p_list[$peer_ip][$peer_as]) && $peer_ip != '0.0.0.0')
      {
        if ($debug) { echo("Found peer IP: $peer_ip (AS$peer_as, LocalIP: $local_ip)\n"); }
        $peerlist[] = array('id' => $peer['bgpPeerIdentifier'], 'local_ip' => $local_ip, 'ip' => $peer_ip, 'as' => $peer_as);
        $p_list[$peer_ip][$peer_as] = 1;
      }
    }

    if ($use_vendor)
    {
      $vendor_bgp = snmpwalk_cache_oid_num2($device, $vendor_PeerRemoteAs, $vendor_bgp, $vendor_mib, $vendor_mib_dir, TRUE);
      $vendor_bgp = snmpwalk_cache_oid_num2($device, $vendor_PeerRemoteAddr, $vendor_bgp, $vendor_mib, $vendor_mib_dir, TRUE);
      $vendor_bgp = snmpwalk_cache_oid_num2($device, $vendor_PeerLocalAddr, $vendor_bgp, $vendor_mib, $vendor_mib_dir, TRUE);
      $vendor_bgp = snmpwalk_cache_oid_num2($device, $vendor_PeerIdentifier, $vendor_bgp, $vendor_mib, $vendor_mib_dir, TRUE);

      if ($debug) { echo("$vendor_mib Peers: \n"); }
      foreach ($vendor_bgp as $entry)
      {
        $peer_ip = hex2ip($entry[$vendor_PeerRemoteAddr]);
        $local_ip = hex2ip($entry[$vendor_PeerLocalAddr]);
        $peer_as = $entry[$vendor_PeerRemoteAs];
        if (!isset($p_list[$peer_ip][$peer_as]) && $peer_ip != '0.0.0.0')
        {
          $p_list[$peer_ip][$peer_as] = 1;
          $peerlist[] = array('id' => $entry[$vendor_PeerIdentifier], 'local_ip' => $local_ip, 'ip' => $peer_ip, 'as' => $peer_as);
          if ($debug) { echo("Found peer IP: $peer_ip (AS$peer_as, LocalIP: $local_ip)\n"); }
        }
      }
    } # Vendors
    
  } else {
    echo("No BGP on host");
    if ($device['bgpLocalAs'])
    {
      log_event('BGP ASN removed: AS' . $device['bgpLocalAs'], $device, 'bgp');
      dbUpdate(array('bgpLocalAs' => 'NULL') , 'devices', 'device_id = ?', array($device['device_id']));
      echo('Removed ASN ('.$device['bgpLocalAs']."\n");
    } # End if
  } # End if

  // Process discovered peers

  if (isset($peerlist))
  {
    // Walk vendor oids
    if ($use_vendor)
    {
      $vendor_bgp = snmpwalk_cache_oid_num2($device, $vendor_PeerRemoteAs, $vendor_bgp, $vendor_mib, $vendor_mib_dir, TRUE);
      $vendor_bgp = snmpwalk_cache_oid_num2($device, $vendor_PeerRemoteAddr, $vendor_bgp, $vendor_mib, $vendor_mib_dir, TRUE);
      $vendor_bgp = snmpwalk_cache_oid_num2($device, $vendor_PeerRemoteAddrType, $vendor_bgp, $vendor_mib, $vendor_mib_dir, TRUE);
      $vendor_bgp = snmpwalk_cache_oid_num2($device, $vendor_PeerIndex, $vendor_bgp, $vendor_mib, $vendor_mib_dir, TRUE);
      $vendor_counters = snmpwalk_cache_oid($device, $vendor_PrefixCountersSafi, array(), $vendor_mib, $vendor_mib_dir);
    }

    foreach ($peerlist as $peer)
    {
      $astext = get_astext($peer['as']);

      if (dbFetchCell('SELECT COUNT(*) FROM `bgpPeers` WHERE `device_id` = ? AND bgpPeerRemoteAddr = ?', array($device['device_id'], $peer['ip'])) < '1')
      {
        $params = array('device_id' => $device['device_id'], 'bgpPeerIdentifier' => $peer['id'], 'bgpPeerRemoteAddr' => $peer['ip'], 'bgpPeerLocalAddr' => $peer['local_ip'], 'bgpPeerRemoteAS' => $peer['as'], 'astext' => mres($astext));
        dbInsert($params, 'bgpPeers');
        echo('+');
      } else {
        dbUpdate(array('bgpPeerRemoteAs' => $peer['as'], 'astext' => mres($astext), 'bgpPeerLocalAddr' => $peer['local_ip'], 'bgpPeerIdentifier' => $peer['id']) , 'bgpPeers', 'device_id = ? AND bgpPeerRemoteAddr = ?', array($device['device_id'], $peer['ip']));
        echo('.');
      }

      if ($device['os_group'] == "cisco" || $use_vendor)
      {

        if ($device['os_group'] == "cisco")
        {
          // Get afi/safi and populate cbgp on cisco ios (xe/xr)
          unset($af_list);

          $af_data = snmpwalk_cache_multi_oid($device, 'cbgpPeerAddrFamilyName', $cbgp, 'CISCO-BGP4-MIB', mib_dirs('cisco'));
          
          foreach ($af_data as $af => $entry)
          {
            $afisafi = explode('.', $af);
            $c = count($afisafi);
            $afi = $afisafi[$c - 2];
            $safi = $afisafi[$c - 1];
            if ($debug) { echo("AS: $peer_as, IP: $peer_ip, AFI: $afi, SAFI: $safi\n"); }
            $text = $entity['cbgpPeerAddrFamilyName'];
            if ($afi && $safi)
            {
              if (dbFetchCell('SELECT COUNT(*) FROM `bgpPeers_cbgp` WHERE `device_id` = ? AND bgpPeerRemoteAddr = ? AND afi = ? AND safi = ?', array($device['device_id'], $peer['ip'], $afi, $safi)) == 0)
              {
                $params = array('device_id' => $device['device_id'], 'bgpPeerRemoteAddr' => $peer['ip'], 'bgpPeerIndex' => $index, 'afi' => $afi, 'safi' => $safi);
                dbInsert($params, 'bgpPeers_cbgp');
              }
            }
          }
        } # os_group=cisco

        if ($use_vendor)
        {
          // See posible AFI/SAFI here: https://www.juniper.net/techpubs/en_US/junos12.3/topics/topic-map/bgp-multiprotocol.html
          $afis['ipv4'] = '1';
          $afis['ipv6'] = '2';
          $safis = array(1 => 'unicast',
                         2 => 'multicast',
                         128 => 'vpn');

          foreach ($vendor_bgp as $entry)
          {
            $peer_ip = hex2ip($entry[$vendor_PeerRemoteAddr]);
            $peer_as = $entry[$vendor_PeerRemoteAs];
            if ($peer['ip'] == $peer_ip && $peer['as'] == $peer_as)
            {
              $index = $entry[$vendor_PeerIndex];
              $afi = $entry[$vendor_PeerRemoteAddrType];
              
              foreach ($safis as $i => $safi)
              {
                if (isset($vendor_counters[$index.'.'.$afi.".$i"]) || isset($vendor_counters[$index.'.'.$afis[$afi].".$i"]))
                {
                  if (is_numeric($afi)) { $afi = $afis[$afi]; }
                  if ($debug) { echo ("INDEX: $index, AS: $peer_as, IP: $peer_ip, AFI: $afi, SAFI: $safi\n"); }
                  if (dbFetchCell('SELECT COUNT(*) FROM `bgpPeers_cbgp` WHERE `device_id` = ? AND bgpPeerRemoteAddr = ? AND afi = ? AND safi = ?', array($device['device_id'], $peer['ip'], $afi, $safi)) == 0)
                  {
                    $params = array('device_id' => $device['device_id'], 'bgpPeerRemoteAddr' => $peer['ip'], 'bgpPeerIndex' => $index, 'afi' => $afi, 'safi' => $safi);
                    dbInsert($params, 'bgpPeers_cbgp');
                  } elseif ($index >= 0) {
                    // Update Index
                    $params = array('device_id' => $device['device_id'], 'bgpPeerRemoteAddr' => $peer['ip'], 'afi' => $afi, 'safi' => $safi);
                    dbUpdate(array('bgpPeerIndex' => $index), 'bgpPeers_cbgp', 'device_id = ? AND bgpPeerRemoteAddr = ? AND afi = ? AND safi = ?', array($device['device_id'], $peer['ip'], $afi, $safi));
                  }
                }
              }
              break;
            }
          }
        } # Vendors

        $query = 'SELECT * FROM bgpPeers_cbgp WHERE `device_id` = ? AND bgpPeerRemoteAddr = ?';
        foreach (dbFetchRows($query, array($device['device_id'], $peer['ip'])) as $entry)
        {
          if ($afi != $entry['afi'] && $safi != $entry['safi'])
          {
            dbDelete('bgpPeers_cbgp', '`device_id` = ? AND bgpPeerRemoteAddr = ? AND afi = ? AND safi = ?', array($device['device_id'], $peer['ip'], $afi, $safi));
          }
        } # AF list
      } # os=cisco|some vendors
    } # Foreach
        
    unset($afi, $safi, $index);
  } # isset

  
  
  // Delete removed peers

  $query = 'SELECT * FROM bgpPeers WHERE device_id = ?';
  foreach (dbFetchRows($query, array($device['device_id'])) as $entry)
  {
    $exists = FALSE;

    foreach ($peerlist as $peer)
    {
      if ($peer['ip'] == $entry['bgpPeerRemoteAddr'])
      {
        $exists = TRUE;
        break;
      }
    }

    if (!$exists && ($device['os_group'] == "cisco" || isset($vendor_oids[$device['os']])))
    {
      $query = 'SELECT cbgp_id FROM bgpPeers_cbgp WHERE device_id = ? AND bgpPeerRemoteAddr = ?';
      foreach (dbFetchRows($query, array($device['device_id'], $peer['ip'])) as $cbgp_id)
      {
        dbDelete('bgpPeers_cbgp', '`cbgp_id` = ?', array($cbgp_id));
        dbDelete('bgpPeers_cbgp-state', '`cbgp_id` = ?', array($cbgp_id));
      }
    }
    
    if (!$exists)
    {
      dbDelete('bgpPeers', '`bgpPeer_id` = ?', array($entry['bgpPeer_id']));
      dbDelete('bgpPeers-state', '`bgpPeer_id` = ?', array($entry['bgpPeer_id']));
      echo("-");
    }
  }

  unset($p_list, $peerlist);

  echo("\n");
}

?>
