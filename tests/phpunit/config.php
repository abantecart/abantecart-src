<?php

$dirname = dirname(__FILE__);
$dirname = dirname($dirname);

$dirname = dirname($dirname).'/public_html';
define('ABC_TEST_ROOT_PATH', $dirname);
define('ABC_TEST_HTTP_HOST', 'travis-ci.org');
define('ABC_TEST_PHP_SELF',  'abantecart/abantecart-src/public_html/index.php');

//check abantecart config.php
if(!filesize($dirname.'/system/config.php')){

	$content = "<?php\n";
	$content .= "/**\n";
	$content .= "	AbanteCart, Ideal OpenSource Ecommerce Solution\n";
	$content .= "	http://www.AbanteCart.com\n";
	$content .= "	Copyright © 2011-".date('Y')." Belavier Commerce LLC\n\n";
	$content .= "	Released under the Open Software License (OSL 3.0)\n";
	$content .= "*/\n\n";
	$content .= "define('SERVER_NAME', '" . getenv('SERVER_NAME') . "');\n";
	$content .= "// Admin Section Configuration. You can change this value to any name. Will use ?s=name to access the admin\n";
	$content .= "define('ADMIN_PATH', 'your_admin');\n\n";
	$content .= "// Database Configuration\n";
	$content .= "define('DB_DRIVER', 'amysqli');\n";
	$content .= "define('DB_HOSTNAME', 'localhost');\n";
	$content .= "define('DB_USERNAME', 'root');\n";
	$content .= "define('DB_PASSWORD', '');\n";
	$content .= "define('DB_DATABASE', 'abantecart_test_build');\n";
	$content .= "define('DB_PREFIX', 'ac_');\n";
	$content .= "\n";
	$content .= "define('CACHE_DRIVER', 'file');\n";
	$content .= "// Unique AbanteCart store ID\n";
	$content .= "define('UNIQUE_ID', '" . md5(time()) . "');\n";
	$content .= "// Salt key for oneway encryption of passwords. NOTE: Change of SALT key will cause a loss of all existing users' and customers' passwords!\n";
	$content .= "define('SALT', 'unit');\n";
	$content .= "// Encryption key for protecting sensitive information. NOTE: Change of this key will cause a loss of all existing encrypted information!\n";
	$content .= "define('ENCRYPTION_KEY', '12345');\n";

	$file = fopen($dirname . '/system/config.php', 'w');
	fwrite($file, $content);
	fclose($file);
	unset($file, $content);
}

unset($dirname);