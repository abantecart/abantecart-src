alter table `ac_contents`
    add `author` varchar(128) COLLATE utf8_general_ci NOT NULL DEFAULT '',
    add `content_bar` int(1) NOT NULL DEFAULT '0',
    add `icon_rl_id` int(11),
    add `publish_date` timestamp NULL,
    add `expire_date` timestamp NULL,
    add `date_added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    add `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

update `ac_contents` c set `publish_date` = (
    select `date_added` from `ac_content_descriptions` cd where c.content_id = cd.content_id limit 1
);
UPDATE `ac_content_descriptions` c SET `title` = `name` WHERE title = '';
ALTER TABLE `ac_content_descriptions` DROP `name`;

--Remove duplicate ac_contents entries. content_id is now unique
CREATE TEMPORARY TABLE temp_unique AS
SELECT MIN(content_id) AS content_id, parent_content_id
FROM `ac_contents`
GROUP BY content_id;

DELETE FROM `ac_contents`
WHERE (content_id, parent_content_id) NOT IN (SELECT content_id, parent_content_id FROM temp_unique);

DROP TEMPORARY TABLE temp_unique;

CREATE TABLE `ac_content_tags` (
   `content_id` int(11) NOT NULL,
   `tag` varchar(32) COLLATE utf8_general_ci NOT NULL COMMENT 'translatable',
   `language_id` int(11) NOT NULL,
   PRIMARY KEY  (`content_id`,`tag`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `ac_blocks` (`block_txt_id`, `controller`, `date_added`) VALUES
    ('new_content','blocks/new_content',NOW());

INSERT INTO `ac_block_templates` (`block_id`, `parent_block_id`, `template`, `date_added`) VALUES
    (LAST_INSERT_ID(), 3, 'blocks/new_content.tpl',NOW()),
    (LAST_INSERT_ID(), 6, 'blocks/new_content.tpl',NOW());

INSERT INTO `ac_blocks` (`block_txt_id`, `controller`, `date_added`) VALUES
    ('content_search', 'blocks/content_search', now());

INSERT INTO `ac_block_templates` (`block_id`, `parent_block_id`, `template`, `date_added`) VALUES
    (LAST_INSERT_ID(), 1, 'blocks/content_search.tpl', now()),
    (LAST_INSERT_ID(), 2, 'blocks/content_search.tpl', now()),
    (LAST_INSERT_ID(), 3, 'blocks/content_search.tpl', now()),
    (LAST_INSERT_ID(), 6, 'blocks/content_search.tpl', now());

--
-- DDL for table `fields_history`
--
create table `ac_fields_history`
(
    `hist_id`       int(10)                                not null auto_increment,
    `table_name`    varchar(40)                            not null,
    `record_id`      int                                    not null,
    `field`         varchar(128)                           not null,
    `version`       int(10)        default 1               not null,
    `language_id`   int(10)                                not null,
    `text`          longtext                               not null,
    `date_added`    timestamp  default current_timestamp() null,
    primary key (`hist_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

create index `ac_fields_history_idx`
    on `ac_fields_history` (`table_name`, `record_id`, `field`, `language_id`);

