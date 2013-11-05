insert into ac_settings (store_id, `group`,`key`,`value`) values (0,'system','config_voicecontrol', 1);

alter table `ac_fields` add column `regexp_pattern` varchar(255) NOT NULL DEFAULT '' AFTER `status`;

alter table `ac_field_descriptions` add column `error_text` varchar(255) not null default '' AFTER `language_id`, comment = 'translatable';