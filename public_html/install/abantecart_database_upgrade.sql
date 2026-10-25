DROP TABLE IF EXISTS `ac_collections_to_stores`;
CREATE TABLE `ac_collections_to_stores`
(
    collection_id INT NOT NULL,
    store_id      INT NOT NULL,
    PRIMARY KEY (collection_id, store_id)
)   ENGINE=InnoDb DEFAULT CHARSET=utf8mb4 COLLATE = utf8mb4_unicode_ci;

INSERT INTO `ac_collections_to_stores` (collection_id, store_id)
SELECT id, store_id
FROM `ac_collections`;

ALTER TABLE `ac_collections`
    MODIFY name VARCHAR(255) NULL,
    MODIFY description TEXT NULL,
    MODIFY conditions TEXT NULL,
    DROP COLUMN store_id,
    ADD date_modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP() NOT NULL ON UPDATE CURRENT_TIMESTAMP();

ALTER TABLE `ac_collection_descriptions`
    MODIFY title VARCHAR(255) NULL,
    MODIFY meta_keywords TEXT NULL,
    MODIFY meta_description TEXT NULL,
    ADD content LONGTEXT NOT NULL COMMENT 'translatable',
    ADD date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP() NULL,
    ADD date_modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP() NOT NULL ON UPDATE CURRENT_TIMESTAMP();


ALTER TABLE `ac_email_templates`
    MODIFY text_id VARCHAR(255) NOT NULL,
    MODIFY headers VARCHAR(255) NOT NULL,
    MODIFY subject VARCHAR(255) NOT NULL COMMENT 'translatable',
    MODIFY html_body TEXT NOT NULL COMMENT 'translatable',
    MODIFY text_body TEXT NOT NULL COMMENT 'translatable',
    MODIFY allowed_placeholders TEXT NOT NULL;

ALTER TABLE `ac_field_group_to_form`
    COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `ac_orders`
    ADD `guest_checkout` int(1) NOT NULL DEFAULT '0' AFTER `customer_group_id`;

--
-- DDL for table `url_redirects`
--
DROP TABLE IF EXISTS `ac_url_redirects`;
CREATE TABLE `ac_url_redirects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source` varchar(2048) NOT NULL DEFAULT '' COMMENT 'path, seo keyword or query string. Can be empty if source_host or source_http_headers is set',
  `source_host` varchar(255) NOT NULL DEFAULT '' COMMENT 'HTTP_REFERER host or visitor ip. Empty means any visitor',
  `source_http_headers` text COMMENT 'request headers in the http format, one "Name: value" per line. All of them must be present in the request',
  `match_type` enum('exact','prefix','regex','query') NOT NULL DEFAULT 'exact',
  `target` varchar(2048) NOT NULL DEFAULT '',
  `action` smallint(3) NOT NULL DEFAULT '301' COMMENT '301, 302, 403, 404, 410',
  `keep_query` tinyint(1) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `hits` bigint(20) unsigned NOT NULL DEFAULT '0',
  `last_hit` timestamp NULL DEFAULT NULL,
  `comment` text,
  `date_added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ac_url_redirects_idx` (`status`, `priority`)
) ENGINE=InnoDb DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
