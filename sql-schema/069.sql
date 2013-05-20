ALTER TABLE  `alert_conditions_global` ADD  `descr_regex` VARCHAR( 128 ) NULL DEFAULT NULL AFTER  `subtype`;
TRUNCATE `ospf_areas`;
TRUNCATE `ospf_instances`;
TRUNCATE `ospf_nbrs`;
TRUNCATE `ospf_ports`;
