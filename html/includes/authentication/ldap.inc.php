<?php

$ds = @ldap_connect($config['auth_ldap_server'],$config['auth_ldap_port']);

if ($config['auth_ldap_starttls'] && ($config['auth_ldap_starttls'] == 'optional' || $config['auth_ldap_starttls'] == 'require'))
{
  $tls = ldap_start_tls($ds);
  if ($config['auth_ldap_starttls'] == 'require' && $tls == FALSE)
  {
    print_error("Fatal error: LDAP TLS required but not successfully negotiated [" . ldap_error($ds) . "]");
    exit;
  }
}

if ($config['auth_ldap_version'])
{
  ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, $config['auth_ldap_version']);
}

if ($config['auth_ldap_kerberized'])
{
  $_SESSION['username'] = $_SERVER['REMOTE_USER'];
  $_SESSION['authenticated'] = TRUE;
}

function authenticate($username, $password)
{
  global $config, $debug, $ds;

  if ($username && $ds)
  {
    if (ldap_bind_dn($username, $password)) { return 0; }

    $binduser = ldap_dn_from_username($username);
    
    if ($binduser)
    {
      if ($debug) { echo("LDAP[Bind][" . $binduser . "]\n"); }

      // Auth via Apache Kerberos module + LDAP fallback -> automatically authenticated
      if ($config['auth_ldap_kerberized'] || ldap_bind($ds, $binduser, $password))
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

function auth_can_logout()
{
  global $config;

  // If kerberized, login is handled through apache; if not, we can log out.
  return (!$config['auth_ldap_kerberized']);
}

function auth_can_change_password($username = "")
{
  return 0;
}

function auth_change_password($username,$newpassword)
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

function auth_user_exists($username)
{
  global $config, $ds;

  if (ldap_bind_dn()) { return 0; } // Will not work without bind user or anon bind

  $binduser = ldap_dn_from_username($username);
    
  if ($binduser)
  {
    return 1;
  }

  return 0;
}

function auth_user_level($username)
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

function auth_user_id($username)
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
    $userid = ldap_auth_user_id($entries[0]);
    if ($debug) { echo("LDAP[UserID][$userid]\n"); }
  }

  return $userid;
}

function deluser($username)
{
  # Not supported
  return 0;
}

function auth_user_list()
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
      $user_id  = ldap_auth_user_id($entries[$i]);

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


# Private function for this ldap module only
# Returns the textual SID for Active Directory
function ldap_bin_to_str_sid($binsid)
{
  $hex_sid = bin2hex($binsid);
  $rev = hexdec(substr($hex_sid, 0, 2));
  $subcount = hexdec(substr($hex_sid, 2, 2));
  $auth = hexdec(substr($hex_sid, 4, 12));
  $result  = "$rev-$auth";

  for ($x=0;$x < $subcount; $x++)
  {
    $subauth[$x] = hexdec(ldap_little_endian(substr($hex_sid, 16 + ($x * 8), 8)));
    $result .= "-" . $subauth[$x];
  }

  # Cheat by tacking on the S-
  return 'S-' . $result;
}

# Private function for this ldap module only
# Converts a little-endian hex-number to one, that 'hexdec' can convert
function ldap_little_endian($hex)
{
  for ($x = strlen($hex) - 2; $x >= 0; $x = $x - 2)
  {
    $result .= substr($hex, $x, 2);
  }

  return $result;
} 

# Private function for this ldap module only
# Bind with either the configured bind DN, the user's configured DN, or anonymously, depending on config.
function ldap_bind_dn($username = "", $password = "")
{
  global $config, $debug, $ds;

  if ($config['auth_ldap_binddn'])
  {
    if ($debug) { echo("LDAP[Bind][" . $config['auth_ldap_binddn'] . "]\n"); }
    if (!ldap_bind($ds, $config['auth_ldap_binddn'], $config['auth_ldap_bindpw']))
    {
      print_error("Error binding to LDAP server: " . $config['auth_ldap_server']);
      return 1;
    }
  } else {
    # Try anonymous bind if configured to do so
    if ($config['auth_ldap_bindanonymous'])
    {
      if ($debug) { echo("LDAP[Bind][anonymous]\n"); }
      if (!ldap_bind($ds))
      {
        return 1;
      }
    } else {
      if ($debug) { echo("LDAP[Bind][" . $config['auth_ldap_prefix'] . $username . $config['auth_ldap_suffix'] . "]\n"); }
      if (!ldap_bind($ds, $config['auth_ldap_prefix'] . $username . $config['auth_ldap_suffix'], $password))
      {
        return 1;
      }
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
function ldap_auth_user_id($result)
{
  global $config, $debug;

  # For AD, convert SID S-1-5-21-4113566099-323201010-15454308-1104 to 1104 as our numeric unique ID
  if ($config['auth_ldap_attr']['uidNumber'] == "objectSid")
  {
    $sid = explode('-',ldap_bin_to_str_sid($result['objectsid'][0]));
    $userid = $sid[count($sid)-1];
  } else {
    $userid = $result[strtolower($config['auth_ldap_attr']['uidnumber'])][0];
  }
  
  return $userid;
}


?>
