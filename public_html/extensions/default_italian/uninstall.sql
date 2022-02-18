DELETE FROM `ac_language_definitions` WHERE language_id = (SELECT language_id FROM `ac_languages` WHERE `code`='it');
DELETE FROM `ac_languages` WHERE `code`='it';