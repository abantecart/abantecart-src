DROP TABLE IF EXISTS `ac_order_status_ids`;
CREATE TABLE `ac_order_status_ids` (
  `order_status_id` int(11) NOT NULL,
  `status_text_id` varchar(32) NOT NULL,
  PRIMARY KEY (`order_status_id`,`status_text_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE UNIQUE INDEX `ac_order_status_ids_idx`
ON `ac_order_status_ids` ( `status_text_id`);

INSERT INTO `ac_order_status_ids` (`order_status_id`, `status_text_id`) VALUES
(0, 'incomplete'),
(1, 'pending'),
(2, 'processing'),
(3, 'shipped'),
(7, 'canceled'),
(5, 'completed'),
(8, 'denied'),
(9, 'canceled_reversal'),
(10, 'failed'),
(11, 'refunded'),
(12, 'reversed'),
(13, 'chargeback'),
(14, 'canceled_by_customer');

UPDATE `ac_fields`
SET regexp_pattern = '/^[A-Z0-9._%-]+@[A-Z0-9][A-Z0-9.-]{0,61}[A-Z0-9]\\.[A-Z]{2,16}$/i'
WHERE field_id = 12 AND field_name = 'email';