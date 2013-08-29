<?php
/**
 * Observium
 *
 *   This files is part of Observium.
 *
 * @package    observium
 * @subpackage applications
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */

$app_sections = array('system' => "System",
                      'backend' => "Backend",
                      'jvm' => "Java VM",
                     );

$app_graphs['system'] = array(
                'zimbra_fdcount'  => 'Open file descriptors',
                );

$app_graphs['backend'] = array(
                'zimbra_mtaqueue'     => 'MTA queue size',
                'zimbra_connections'  => 'Open connections',
                'zimbra_threads'      => 'Threads',
                );

$app_graphs['jvm'] = array(
                'zimbra_jvmthreads'      => 'JVM Threads',
                );
