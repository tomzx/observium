ALTER TABLE `mac_accounting` DROP INDEX interface_id;
ALTER TABLE  `observium`.`mac_accounting` ADD UNIQUE  `port_vlan_mac` (  `port_id` ,  `vlan_id` ,  `mac` );
