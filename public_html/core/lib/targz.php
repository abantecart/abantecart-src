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
final class Atargz {
	public function infosTar($src, $data = true) {
		if ($this->is_tar($src)) {
			file_put_contents($tmp = '~tmp(' . microtime() . ').tar', $src);
			$src = $tmp;
		}
		$ptr = fopen($src, 'r');
		while (!feof($ptr))
		{
			$infos = $this->readTarHeader($ptr);
			if ($infos[ 'name' ]) {
				if (!$data) unset($infos[ 'data' ]);
				$result[ $infos[ 'name' ] ] = $infos;
			}
		}
		if (is_file($tmp)) unlink($tmp);
		return $result;
	}

	public function makeTar($src, $dest = false, $compress_level = 5) {
		$src = is_array($src) ? $src : array( $src );
		$src = array_map('realpath', $src);
		$Tar = '';
		$compress_level = ($compress_level<1 || $compress_level>9) ? 5 : $compress_level;
		foreach ($src as $item)
			$item = str_replace('\\', '/', $item);
			$Tar .= $this->addTarItem($item . ((is_dir($item) && substr($item, -1) != '/') ? '/'
		     : ''), dirname($item) . '/');

		$Tar = str_pad($Tar, floor((strlen($Tar) + 10240 - 1) / 10240) * 10240, "\0");
		if (empty($dest)) {
			return $Tar;
		}

		$gz = gzopen($dest, 'w'.(int)$compress_level);
		if ($gz) {
			gzwrite($gz, $Tar);
			gzclose($gz);
		}
		else {
			return false;
		}
	}

	public function extractTar($src, $dest) {
		if ($this->is_tar($src)) {
			file_put_contents($tmp = '~tmp(' . microtime() . ').tar', $src);
			$src = $tmp;
		}
		$ptr = gzopen($src, 'r');
		while (!gzeof($ptr))
		{
			$infos = $this->readTarHeader($ptr);
			if ( $infos[ 'type' ] == '5' )
			{
				if ( !file_exists($dest . $infos[ 'name' ]) ) {
					mkdir($dest . $infos[ 'name' ], 0777, true);
				}
				$result[ ] = $dest . $infos[ 'name' ];
			}
			elseif ( $infos[ 'type' ] == '0' || $infos[ 'type' ] == chr(0) )
			{
				$dirPath = substr($infos[ 'name' ], 0, strrpos($infos[ 'name' ], '/'));
				if ( !file_exists($dest . $dirPath) ) {
					mkdir($dest . $dirPath, 0777, true);
				}
				if ( file_put_contents($dest . $infos[ 'name' ], $infos[ 'data' ]) ) {
					$result[ ] = $dest . $infos[ 'name' ];
				}
			}
			if ($infos)
				chmod($dest . $infos[ 'name' ], 0777);
		}
		if (is_file($tmp)) unlink($tmp);
		return $result;
	}

	private function is_tar($str) {
		$block = substr($str, 0, 512);
		if (strlen($block) != 512) return false;
		$realchecksum = octdec(substr($str, 148, 8));
		$checksum = 0;
		$block = substr_replace($block, '        ', 148, 8);
		for ($i = 0; $i < 512; $i++)
			$checksum += ord(substr($block, $i, 1));
		if ($realchecksum == $checksum) return true;
		return false;
	}

	private function tarHeader512($infos) { /* http://www.mkssoftware.com/docs/man4/tar.4.asp */
		$bigheader = $header = '';
		if (strlen($infos[ 'name100' ]) > 100) {
			$bigheader = pack("a100a8a8a8a12a12a8a1a100a6a2a32a32a8a8a155a12",
			                  '././@LongLink', '0000000', '0000000', '0000000',
			                  sprintf("%011o", strlen($infos[ 'name100' ])), '00000000000',
			                  '        ', 'L', '', 'ustar ', '0',
			                  $infos[ 'userName32' ],
			                  $infos[ 'groupName32' ], '', '', '', '');

			$bigheader .= str_pad($infos[ 'name100' ], floor((strlen($infos[ 'name100' ]) + 512 - 1) / 512) * 512, "\0");

			$checksum = 0;
			for ($i = 0; $i < 512; $i++)
				$checksum += ord(substr($bigheader, $i, 1));
			$bigheader = substr_replace($bigheader, sprintf("%06o", $checksum) . "\0 ", 148, 8);
		}
		$header = pack("a100a8a8a8a12a12a8a1a100a6a2a32a32a8a8a155a12", // book the memorie area
		               substr($infos[ 'name100' ], 0, 100), //  0 	100 	File name
		               str_pad(substr(sprintf("%07o", $infos[ 'mode8' ]), -4), 7, '0', STR_PAD_LEFT), // 100 	8 		File mode
		               sprintf("%07o", $infos[ 'uid8' ]), // 108 	8 		Owner user ID
		               sprintf("%07o", $infos[ 'gid8' ]), // 116 	8 		Group user ID
		               sprintf("%011o", $infos[ 'size12' ]), // 124 	12 		File size in bytes
		               sprintf("%011o", $infos[ 'mtime12' ]), // 136 	12 		Last modification time
		               '        ', // 148 	8 		Check sum for header block
		               $infos[ 'link1' ], // 156 	1 		Link indicator / ustar Type flag
		               $infos[ 'link100' ], // 157 	100 	Name of linked file
		               'ustar ', // 257 	6 		USTAR indicator "ustar"
		               ' ', // 263 	2 		USTAR version "00"
		               $infos[ 'userName32' ], // 265 	32 		Owner user name
		               $infos[ 'groupName32' ], // 297 	32 		Owner group name
		               '', // 329 	8 		Device major number
		               '', // 337 	8 		Device minor number
		               $infos[ 'prefix155' ], // 345 	155 	Filename prefix
		               ''); // 500 	12 		??

		$checksum = 0;
		for ($i = 0; $i < 512; $i++)
			$checksum += ord(substr($header, $i, 1));
		$header = substr_replace($header, sprintf("%06o", $checksum) . "\0 ", 148, 8);

		return $bigheader . $header;
	}

	private function addTarItem($item, $racine) {
		$infos[ 'name100' ] = str_replace($racine, '', $item);
		list (, , $infos[ 'mode8' ], , $infos[ 'uid8' ], $infos[ 'gid8' ], , , , $infos[ 'mtime12' ]) = stat($item);
		$infos[ 'size12' ] = is_dir($item) ? 0 : filesize($item);
		$infos[ 'link1' ] = is_link($item) ? 2 : is_dir($item) ? 5 : 0;
		$infos[ 'link100' ] == 2 ? readlink($item) : "";

		$a = function_exists('posix_getpwuid') ? posix_getpwuid(fileowner($item)) : array( 'name' => 'Unknown' );
		$infos[ 'userName32' ] = $a[ 'name' ];

		$a = function_exists('posix_getgrgid') ? posix_getgrgid(filegroup($item)) : array( 'name' => 'Unknown' );
		$infos[ 'groupName32' ] = $a[ 'name' ];
		$infos[ 'prefix155' ] = '';

		$header = $this->tarHeader512($infos);
		$data = str_pad(file_get_contents($item), floor(($infos[ 'size12' ] + 512 - 1) / 512) * 512, "\0");
		$sub = '';
		if (is_dir($item)) {
			$lst = scandir($item);
			array_shift($lst); // remove  ./  of $lst
			array_shift($lst); // remove ../  of $lst
			foreach ($lst as $subitem)
				$sub .= $this->addTarItem($item . $subitem . (is_dir($item . $subitem) ? '/' : ''), $racine);
		}
		return $header . $data . $sub;
	}

	private function readTarHeader($ptr) {
		$block = fread($ptr, 512);
		if (strlen($block) != 512) return false;
		$hdr = unpack("a100name/a8mode/a8uid/a8gid/a12size/a12mtime/a8checksum/a1type/a100symlink/a6magic/a2version/a32uname/a32gname/a8devmajor/a8devminor/a155prefix/a12temp", $block);
		$hdr[ 'mode' ] = $hdr[ 'mode' ] + 0;
		$hdr[ 'uid' ] = octdec($hdr[ 'uid' ]);
		$hdr[ 'gid' ] = octdec($hdr[ 'gid' ]);
		$hdr[ 'size' ] = octdec($hdr[ 'size' ]);
		$hdr[ 'mtime' ] = octdec($hdr[ 'mtime' ]);
		$hdr[ 'checksum' ] = octdec($hdr[ 'checksum' ]);
		$checksum = 0;
		$block = substr_replace($block, '        ', 148, 8);
		for ($i = 0; $i < 512; $i++)
			$checksum += ord(substr($block, $i, 1));
		if (isset($hdr[ 'name' ]) && $hdr[ 'checksum' ] == $checksum) {
			if ($hdr[ 'name' ] == '././@LongLink' && $hdr[ 'type' ] == 'L') {
				$realName = substr(fread($ptr, floor(($hdr[ 'size' ] + 512 - 1) / 512) * 512), 0, $hdr[ 'size' ] - 1);
				$hdr2 = $this->readTarHeader($ptr);
				$hdr2[ 'name' ] = $realName;
				return $hdr2;
			}
			elseif (strtolower(substr($hdr[ 'magic' ], 0, 5) == 'ustar'))
			{
				if ($hdr[ 'size' ] > 0)
					$hdr[ 'data' ] = substr(fread($ptr, floor(($hdr[ 'size' ] + 512 - 1) / 512) * 512), 0, $hdr[ 'size' ]);
				else $hdr[ 'data' ] = '';
				return $hdr;
			}
			else return false;
		}
		else return false;
	}
}

?>