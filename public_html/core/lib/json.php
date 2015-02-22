<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')) {
	header('Location: static_pages/');
}

final class AJson {
	static public function encode($data) {
		if (function_exists('json_encode')) {
			return json_encode($data);
		} else {
			switch (gettype($data)) {
				case 'boolean':
					return $data ? 'true' : 'false';
				case 'integer':
				case 'double':
					return $data;
				case 'resource':
				case 'string':
					return '"' . str_replace(array( "\r", "\n", "<", ">", "&" ), array( '\r', '\n', '\x3c', '\x3e', '\x26' ), addslashes($data)) . '"';
				case 'array':
					if (empty($data) || array_keys($data) === range(0, sizeof($data) - 1)) {
						$stdout = array();

						foreach ($data as $value) {
							$stdout[ ] = AJson::encode($value);
						}

						return '[ ' . implode(', ', $stdout) . ' ]';
					}
				case 'object':
					$stdout = array();

					foreach ($data as $key => $value) {
						$stdout[ ] = AJson::encode(strval($key)) . ': ' . AJson::encode($value);
					}

					return '{ ' . implode(', ', $stdout) . ' }';
				default:
					return 'null';
			}
		}
	}

	static public function decode($json, $assoc = FALSE) {
		if (function_exists('json_decode')) {
			return json_decode($json, $assoc);
		} else {
			$match = '/".*?(?<!\\\\)"/';

			$string = preg_replace($match, '', $json);
			$string = preg_replace('/[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]/', '', $string);

			if ($string != '') {
				return NULL;
			}

			$s2m = array();
			$m2s = array();

			preg_match_all($match, $json, $m);

			foreach ($m[ 0 ] as $s) {
				$hash = '"' . md5($s) . '"';
				$s2m[ $s ] = $hash;
				$m2s[ $hash ] = str_replace('$', '\$', $s);
			}

			$json = strtr($json, $s2m);

			$a = ($assoc) ? '' : '(object) ';

			$data = array(
				':' => '=>',
				'[' => 'array(',
				'{' => "{$a}array(",
				']' => ')',
				'}' => ')'
			);

			$json = strtr($json, $data);

			$json = preg_replace('~([\s\(,>])(-?)0~', '$1$2', $json);

			$json = strtr($json, $m2s);

			$function = @create_function('', "return {$json};");
			$return = ($function) ? $function() : NULL;

			unset($s2m);
			unset($m2s);
			unset($function);

			return $return;
		}
	}
}

?>