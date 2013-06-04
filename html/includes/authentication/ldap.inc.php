<?php

$ds = @ldap_connect($config['auth_ldap_server'],$config['auth_ldap_port']);

if ($config['auth_ldap_starttls'] && ($config['auth_ldap_starttls'] == 'optional' || $config['auth_ldap_starttls'] == 'require'))
{
  $tls = ldap_start_tls($ds);
  if ($config['auth_ldap_starttls'] == 'require' && $tls == FALSE)
  {
    print_message("Fatal error: LDAP TLS required but not successfully negotiated:" . ldap_error($ds));
    exit;
  }
}

if ($config['auth_ldap_version'])
{
  ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, $config['auth_ldap_version']);
}

# Private function for this ldap module only
function ldap_bind_dn()
{
  global $config, $debug, $ds;

  if ($debug) { echo("LDAP[Bind][" . $config['auth_ldap_binddn'] . "]\n"); }
  if ($config['auth_ldap_binddn'])
  {
    if (!ldap_bind($ds, $config['auth_ldap_binddn'], $config['auth_ldap_bindpw']))
    {
      print_message("Error binding to LDAP server " . $config['auth_ldap_server']);
      return 1;
    }
  } else {
    if (!ldap_bind($ds))
    {
      print_message("Error binding anonymously to LDAP server " . $config['auth_ldap_server']);
      return 1;
    }
  }
  
  return 0;
}

# Private function for this ldap module only
function ldap_dn_from_username($username)
{
  global $config, $debug, $ds, $cache;

  if (!isset($cache['ldap']['dn'][$username]))
  {
    $filter = "(" . $config['auth_ldap_attr']['uid'] . '=' . $username . ")";
    if ($debug) { echo("LDAP[Filter][$filter][" . trim($config['auth_ldap_suffix'],',') . "]\n"); }
    $search = ldap_search($ds, trim($config['auth_ldap_suffix'],','), $filter);
    $entries = ldap_get_entries($ds, $search);
    if ($entries['count'])
    {
      $cache['ldap']['dn'][$username] = $entries[0]['dn'];
    }
  }
  
  return $cache['ldap']['dn'][$username];
}

# Private function for this ldap module only
function ldap_get_userid($result)
{
  global $config, $debug;

  # For AD, convert SID S-1-5-21-4113566099-323201010-15454308-1104 to 1104 as our numeric unique ID
  if ($config['auth_ldap_attr']['uidNumber'] == "objectSid")
  {
    $sid = explode('-',bin_to_str_sid($result['objectsid'][0]));
    $userid = $sid[count($sid)-1];
  } else {
    $userid = $result[strtolower($config['auth_ldap_attr']['uidnumber'])][0];
  }
  
  return $userid;
}

function authenticate($username,$password)
{
  global $config, $debug, $ds;

  if ($username && $ds)
  {
    if (ldap_bind_dn()) { return 0; }

    $binduser = ldap_dn_from_username($username);
    
    if ($binduser)
    {
      if ($debug) { echo("LDAP[Bind][" . $binduser . "]\n"); }
      if (ldap_bind($ds, $binduser, $password))
      {
        if (!$config['auth_ldap_group'])
        {
          return 1;
        }
        else
        {
          $userdn = ($config['auth_ldap_groupmembertype'] == 'fulldn' ? $binduser : $username);
          if ($debug) { echo("LDAP[Compare][" . $config['auth_ldap_group'] . "][".$config['auth_ldap_groupmemberattr']."][$userdn]\n"); }
          if (ldap_compare($ds,$config['auth_ldap_group'],$config['auth_ldap_groupmemberattr'],$userdn))
          {
            return 1;
          } // FIXME does not support nested groups
        }
      }
      else
      {
        echo(ldap_error($ds));
      }
    }
  }

  return 0;
}

function passwordscanchange($username = "")
{
  return 0;
}

function changepassword($username,$newpassword)
{
  # Not supported (for now)
}

function auth_usermanagement()
{
  return 0;
}

function adduser($username, $password, $level, $email = "", $realname = "", $can_modify_passwd = '1')
{
  # Not supported
  return 0;
}

function user_exists($username)
{
  global $config, $ds;

  if (ldap_bind_dn()) { return 0; }

  $binduser = ldap_dn_from_username($username);
    
  if ($binduser)
  {
    return 1;
  }

  return 0;
}

function get_userlevel($username)
{
  global $config, $debug, $ds, $cache;

  if (!isset($cache['ldap']['level'][$username]))
  {
    $userlevel = 0;

    # Find all defined groups $username is in
    $userdn = ($config['auth_ldap_groupmembertype'] == 'fulldn' ? ldap_dn_from_username($username) : $username);
    $filter = "(&(|(cn=" . join(")(cn=", array_keys($config['auth_ldap_groups'])) . "))(" . $config['auth_ldap_groupmemberattr'] . "=" . $userdn . "))";
    if ($debug) { echo("LDAP[Filter][$filter]\n"); }
    $search = ldap_search($ds, $config['auth_ldap_groupbase'], $filter);
    $entries = ldap_get_entries($ds, $search);

    # Loop the list and find the highest level
    foreach ($entries as $entry)
    {
      $groupname = $entry['cn'][0];
      if ($config['auth_ldap_groups'][$groupname]['level'] > $userlevel)
      {
        $userlevel = $config['auth_ldap_groups'][$groupname]['level'];
      }
    }
  
    if ($debug) { echo("LDAP[Userlevel][$userlevel]\n"); }

    $cache['ldap']['level'][$username] = $userlevel;
  }

  return $cache['ldap']['level'][$username];
}

function get_userid($username)
{
  global $config, $debug, $ds;

  $userid = -1;

  $userdn = ($config['auth_ldap_groupmembertype'] == 'fulldn' ? ldap_dn_from_username($username) : $config['auth_ldap_prefix'] . $username . $config['auth_ldap_suffix']);
  
  $filter = "(" . str_ireplace($config['auth_ldap_suffix'],'',$userdn) . ")";
  if ($debug) { echo("LDAP[Filter][$filter][" . trim($config['auth_ldap_suffix'],',') . "]\n"); }
  $search = ldap_search($ds, trim($config['auth_ldap_suffix'],','), $filter);
  $entries = ldap_get_entries($ds, $search);

  if ($entries['count'])
  {
    $userid = ldap_get_userid($entries[0]);
    if ($debug) { echo("LDAP[UserID][$userid]\n"); }
  }

  return $userid;
}

function deluser($username)
{
  # Not supported
  return 0;
}

function get_userlist()
{
  global $config, $debug, $ds;

  $filter = '(objectClass=' . $config['auth_ldap_objectclass'] . ')';

  if ($debug) { echo("LDAP[Filter][$filter][" . trim($config['auth_ldap_suffix'],',') . "]\n"); }
  $search = ldap_search($ds, trim($config['auth_ldap_suffix'],','), $filter);
  $entries = ldap_get_entries($ds, $search);

  if ($entries['count'])
  {
    for ($i = 0; $i < $entries['count']; $i++)
    {
      $username = $entries[$i][strtolower($config['auth_ldap_attr']['uid'])][0];
      $realname = $entries[$i][strtolower($config['auth_ldap_attr']['cn'])][0];
      $user_id  = ldap_get_userid($entries[$i]);

      $userdn = ($config['auth_ldap_groupmembertype'] == 'fulldn' ? $entries[$i]['dn'] : $username);
      if ($debug) { echo("LDAP[Compare][" . $config['auth_ldap_group'] . "][".$config['auth_ldap_groupmemberattr']."][$userdn]\n"); }
      if (!isset($config['auth_ldap_group']) || ldap_compare($ds,$config['auth_ldap_group'],$config['auth_ldap_groupmemberattr'],$userdn))
      {
        $userlist[] = array('username' => $username, 'realname' => $realname, 'user_id' => $user_id);
      } // FIXME does not support nested groups
    }
  }

  return $userlist;
}


# Private functions for this ldap module only to work with MS AD SIDs

# Returns the textual SID
function bin_to_str_sid($binsid)
{
  $hex_sid = bin2hex($binsid);
  $rev = hexdec(substr($hex_sid, 0, 2));
  $subcount = hexdec(substr($hex_sid, 2, 2));
  $auth = hexdec(substr($hex_sid, 4, 12));
  $result  = "$rev-$auth";

  for ($x=0;$x < $subcount; $x++)
  {
    $subauth[$x] = hexdec(little_endian(substr($hex_sid, 16 + ($x * 8), 8)));
    $result .= "-" . $subauth[$x];
  }

  # Cheat by tacking on the S-
  return 'S-' . $result;
}

# Converts a little-endian hex-number to one, that 'hexdec' can convert
function little_endian($hex)
{
  for ($x = strlen($hex) - 2; $x >= 0; $x = $x - 2)
  {
    $result .= substr($hex, $x, 2);
  }

  return $result;
} 

?>
