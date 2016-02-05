ALTER TABLE `ac_customer_groups`
ADD COLUMN `tax_exempt` tinyint(1) NOT NULL DEFAULT '0';

ALTER TABLE `ac_tax_rates`
ADD COLUMN `tax_exempt_groups` text DEFAULT NULL; 

ALTER TABLE `ac_online_customers`
CHANGE COLUMN `ip` `ip` VARCHAR(50) NOT NULL ,
ADD INDEX `ac_online_customers_idx` (`date_added` ASC);

ALTER TABLE `ac_customers`
ADD COLUMN `sms` VARCHAR(32) NULL AFTER `fax`;


ALTER TABLE `ac_orders`
DROP INDEX `ac_orders_idx` ,
ADD INDEX `ac_orders_idx`
	(`invoice_id` ASC,
	`store_id` ASC,
	`customer_group_id` ASC,
	`shipping_zone_id` ASC,
	`shipping_country_id` ASC,
	`payment_zone_id` ASC,
	`payment_country_id` ASC,
	`language_id` ASC,
	`currency_id` ASC,
	`coupon_id` ASC,
	`customer_id` ASC,
	`date_modified` ASC,
	`date_added` ASC);



UPDATE `ac_zones` SET `code` = 'KK' WHERE `zone_id`= 2721 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'AB' WHERE `zone_id`= 2722 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'CHU' WHERE `zone_id`= 2723 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'ARK' WHERE `zone_id`= 2724 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'AST' WHERE `zone_id`= 2725 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'ALT' WHERE `zone_id`= 2726 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'BEL' WHERE `zone_id`= 2727 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'YEV' WHERE `zone_id`= 2728 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'AMU' WHERE `zone_id`= 2729 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'BRY' WHERE `zone_id`= 2730 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'CU' WHERE `zone_id`= 2731 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'CHE' WHERE `zone_id`= 2732 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'KC' WHERE `zone_id`= 2733 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'ZAB' WHERE `zone_id`= 2734 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'TDN' WHERE `zone_id`= 2735 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'KL' WHERE `zone_id`= 2736 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'MOS' WHERE `zone_id`= 2737 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'AL' WHERE `zone_id`= 2738 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'CE' WHERE `zone_id`= 2739 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'IRK' WHERE `zone_id`= 2740 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'IVA' WHERE `zone_id`= 2741 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'UD' WHERE `zone_id`= 2742 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'KGD' WHERE `zone_id`= 2743 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'KLU' WHERE `zone_id`= 2744 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'KIR' WHERE `zone_id`= 2745 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'TA' WHERE `zone_id`= 2746 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'KEM' WHERE `zone_id`= 2747 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'KHA' WHERE `zone_id`= 2748 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'KHM' WHERE `zone_id`= 2749 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'KOS' WHERE `zone_id`= 2750 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'KDA' WHERE `zone_id`= 2751 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'KYA' WHERE `zone_id`= 2752 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'KPO' WHERE `zone_id`= 2753 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'KGN' WHERE `zone_id`= 2754 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'KRS' WHERE `zone_id`= 2755 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'TY' WHERE `zone_id`= 2756 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'LIP' WHERE `zone_id`= 2757 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'MAG' WHERE `zone_id`= 2758 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'DA' WHERE `zone_id`= 2759 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'AD' WHERE `zone_id`= 2760 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'MOW' WHERE `zone_id`= 2761 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'MUR' WHERE `zone_id`= 2762 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'KB' WHERE `zone_id`= 2763 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'NEN' WHERE `zone_id`= 2764 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'IN' WHERE `zone_id`= 2765 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'NIZ' WHERE `zone_id`= 2766 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'NGR' WHERE `zone_id`= 2767 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'NVS' WHERE `zone_id`= 2768 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'OMS' WHERE `zone_id`= 2769 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'ORL' WHERE `zone_id`= 2770 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'ORE' WHERE `zone_id`= 2771 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'LEN' WHERE `zone_id`= 2772 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'PNZ' WHERE `zone_id`= 2773 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'PER' WHERE `zone_id`= 2774 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'KAM' WHERE `zone_id`= 2775 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'KR' WHERE `zone_id`= 2776 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'PSK' WHERE `zone_id`= 2777 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'ROS' WHERE `zone_id`= 2778 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'RYA' WHERE `zone_id`= 2779 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'YAN' WHERE `zone_id`= 2780 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'SAM' WHERE `zone_id`= 2781 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'MO' WHERE `zone_id`= 2782 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'SAR' WHERE `zone_id`= 2783 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'SMO' WHERE `zone_id`= 2784 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'SPE' WHERE `zone_id`= 2785 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'STA' WHERE `zone_id`= 2786 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'KO' WHERE `zone_id`= 2787 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'TAM' WHERE `zone_id`= 2788 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'TOM' WHERE `zone_id`= 2789 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'TUL' WHERE `zone_id`= 2790 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'EO' WHERE `zone_id`= 2791 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'TVE' WHERE `zone_id`= 2792 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'TYU' WHERE `zone_id`= 2793 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'BA' WHERE `zone_id`= 2794 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'ULY' WHERE `zone_id`= 2795 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'BU' WHERE `zone_id`= 2796 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'UOB' WHERE `zone_id`= 2797 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'SE' WHERE `zone_id`= 2798 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'VLA' WHERE `zone_id`= 2799 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'PRI' WHERE `zone_id`= 2800 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'VGG' WHERE `zone_id`= 2801 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'VLG' WHERE `zone_id`= 2802 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'VOR' WHERE `zone_id`= 2803 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'SAK' WHERE `zone_id`= 2804 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'SA' WHERE `zone_id`= 2805 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'YAR' WHERE `zone_id`= 2806 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'SVE' WHERE `zone_id`= 2807 AND `country_id` = 176;
UPDATE `ac_zones` SET `code` = 'ME' WHERE `zone_id`= 2808 AND `country_id` = 176;



UPDATE `ac_zone_descriptions` SET `name` = 'Republic of Khakassia' WHERE `zone_id`= 2721 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Aginsky Buryatsky AO' WHERE `zone_id`= 2722 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Chukotka Autonomous Okrug' WHERE `zone_id`= 2723 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Arkhangelsk Region' WHERE `zone_id`= 2724 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Astrakhan Oblast' WHERE `zone_id`= 2725 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Altai Krai' WHERE `zone_id`= 2726 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Belgorod Oblast' WHERE `zone_id`= 2727 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Jewish Autonomous Oblast' WHERE `zone_id`= 2728 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Amur Region' WHERE `zone_id`= 2729 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Bryansk Oblast' WHERE `zone_id`= 2730 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Chuvash Republic' WHERE `zone_id`= 2731 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Chelyabinsk Region' WHERE `zone_id`= 2732 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Karachay-Cherkess Republic' WHERE `zone_id`= 2733 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Zabaykalsky Krai' WHERE `zone_id`= 2734 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Taymyr Dolgano-Nenets Autonomous Okrug' WHERE `zone_id`= 2735 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Republic of Kalmykia' WHERE `zone_id`= 2736 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Moscow Oblast' WHERE `zone_id`= 2737 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Altai Republic' WHERE `zone_id`= 2738 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Chechen Republic' WHERE `zone_id`= 2739 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Irkutsk Oblast' WHERE `zone_id`= 2740 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Ivanovo Oblast' WHERE `zone_id`= 2741 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Udmurtia' WHERE `zone_id`= 2742 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Kaliningrad Oblast' WHERE `zone_id`= 2743 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Kaluzhskaya oblast' WHERE `zone_id`= 2744 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Kirov Oblast' WHERE `zone_id`= 2745 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Republic of Tatarstan' WHERE `zone_id`= 2746 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Kemerovo region' WHERE `zone_id`= 2747 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Khabarovsk Krai' WHERE `zone_id`= 2748 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Khanty-Mansi Autonomous Okrug - Yugra' WHERE `zone_id`= 2749 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Kostroma Oblast' WHERE `zone_id`= 2750 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Krasnodar Krai' WHERE `zone_id`= 2751 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Krasnoyarsk Krai' WHERE `zone_id`= 2752 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Komi-Permyatsky AO' WHERE `zone_id`= 2753 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Kurgan Oblast' WHERE `zone_id`= 2754 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Kursk Region' WHERE `zone_id`= 2755 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Respublika Tyva' WHERE `zone_id`= 2756 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Lipetsk Region' WHERE `zone_id`= 2757 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Magadan Oblast' WHERE `zone_id`= 2758 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Republic of Dagestan' WHERE `zone_id`= 2759 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Republic of Adygea' WHERE `zone_id`= 2760 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Moscow' WHERE `zone_id`= 2761 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Murmansk Oblast' WHERE `zone_id`= 2762 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Kabardino-Balkar Republic' WHERE `zone_id`= 2763 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Nenets Autonomous Okrug' WHERE `zone_id`= 2764 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Republic of Ingushetia' WHERE `zone_id`= 2765 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Nizhegorodskaya oblast' WHERE `zone_id`= 2766 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Novgorod Oblast' WHERE `zone_id`= 2767 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Novosibirsk Oblast' WHERE `zone_id`= 2768 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Omsk Oblast' WHERE `zone_id`= 2769 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Oryol Oblast' WHERE `zone_id`= 2770 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Orenburg Oblast' WHERE `zone_id`= 2771 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Leningrad Oblast' WHERE `zone_id`= 2772 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Penza Oblast' WHERE `zone_id`= 2773 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Perm Krai' WHERE `zone_id`= 2774 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Kamchatka Krai' WHERE `zone_id`= 2775 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Republic of Karelia' WHERE `zone_id`= 2776 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Pskov Oblast' WHERE `zone_id`= 2777 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Rostov Region' WHERE `zone_id`= 2778 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Ryazan Oblast' WHERE `zone_id`= 2779 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Yamalo-Nenets Autonomous Okrug' WHERE `zone_id`= 2780 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Samara Region' WHERE `zone_id`= 2781 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Republic of Mordovia' WHERE `zone_id`= 2782 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Saratov Region' WHERE `zone_id`= 2783 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Smolensk Oblast' WHERE `zone_id`= 2784 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Saint Petersburg' WHERE `zone_id`= 2785 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Stavropol Krai' WHERE `zone_id`= 2786 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Komi Republic' WHERE `zone_id`= 2787 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Tambov Region' WHERE `zone_id`= 2788 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Tomsk Oblast' WHERE `zone_id`= 2789 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Tula Region' WHERE `zone_id`= 2790 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Evenkysky AO' WHERE `zone_id`= 2791 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Tver Oblast' WHERE `zone_id`= 2792 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Tyumen Oblast' WHERE `zone_id`= 2793 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Republic of Bashkortostan' WHERE `zone_id`= 2794 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Ulyanovsk Oblast' WHERE `zone_id`= 2795 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Republic of Buryatia' WHERE `zone_id`= 2796 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Ust-Orda Buryat Okrug' WHERE `zone_id`= 2797 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'North Ossetia-Alania' WHERE `zone_id`= 2798 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Vladimir Oblast' WHERE `zone_id`= 2799 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Primorsky Krai' WHERE `zone_id`= 2800 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Volgograd Oblast' WHERE `zone_id`= 2801 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Vologda Oblast' WHERE `zone_id`= 2802 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Voronezh Oblast' WHERE `zone_id`= 2803 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Sakhalin Oblast' WHERE `zone_id`= 2804 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Sakha Republic' WHERE `zone_id`= 2805 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Yaroslavl Oblast' WHERE `zone_id`= 2806 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Sverdlovsk Oblast' WHERE `zone_id`= 2807 AND `language_id` = 1;
UPDATE `ac_zone_descriptions` SET `name` = 'Mari El Republic' WHERE `zone_id`= 2808 AND `language_id` = 1;
