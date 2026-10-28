<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2026 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */

namespace lib;

use FilesystemIterator;
use PHPUnit\Framework\TestCase;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * System check of the output buffer.
 *
 * Any byte located outside of <?php ... ?> tags (spaces, empty lines, BOM before the opening tag
 * or whitespace after the closing one) goes straight to the output buffer when the file is included.
 * For html it is harmless, but documents like xml, json, csv or files for download,
 * built in a controller and passed to $this->response->setOutput(), become invalid.
 *
 * The test scans all php-files of the cart and reports every file that would print something
 * just by being included.
 */
class OutputBufferTest extends TestCase
{
    /** directories (relative to public_html) that are not a part of the cart's code */
    private const SKIP_DIRS = [
        'resources',
        'image',
        'download',
        'system/cache',
        'system/logs',
        'system/page_builder',
    ];

    /**
     * Files that print html by design (not included into a response of controllers)
     * Paths are relative to public_html
     */
    private const ALLOWED_FILES = [
        'static_pages/demo_mode.php',
        'static_pages/index.php',
    ];

    private static function root(): string
    {
        return dirname(__DIR__, 4) . DIRECTORY_SEPARATOR . 'public_html';
    }

    /**
     * @return array of absolute paths of php-files
     */
    private static function phpFiles(string $root): array
    {
        $skip = array_map(fn($d) => $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $d), self::SKIP_DIRS);

        $it = new RecursiveIteratorIterator(
            new RecursiveCallbackFilterIterator(
                new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS),
                function ($file) use ($skip) {
                    if (!$file->isDir()) {
                        return true;
                    }
                    // third-party code (also inside of extensions) is not under our control
                    return $file->getFilename() !== 'vendor' && !in_array($file->getPathname(), $skip, true);
                }
            )
        );

        $files = [];
        foreach ($it as $file) {
            if ($file->isFile() && strtolower($file->getExtension()) === 'php') {
                $files[] = $file->getPathname();
            }
        }
        sort($files);
        return $files;
    }

    /**
     * Returns description of the problem or empty string when the file is clean.
     */
    public static function detectLeak(string $code): string
    {
        if ($code === '') {
            return '';
        }

        if (str_starts_with($code, "\xEF\xBB\xBF")) {
            return 'BOM before the opening tag';
        }

        $tokens = token_get_all($code);
        $first = $tokens[0];
        if (is_array($first) && $first[0] === T_INLINE_HTML) {
            // shebang line is stripped by php cli
            if (!str_starts_with($first[1], '#!')) {
                return 'output before the opening tag: ' . self::visible($first[1]);
            }
        }

        $last = end($tokens);
        if (is_array($last) && $last[0] === T_INLINE_HTML && count($tokens) > 1) {
            return 'output after the closing tag: ' . self::visible($last[1]);
        }

        return '';
    }

    private static function visible(string $str): string
    {
        $str = substr($str, 0, 30);
        return '"' . addcslashes($str, "\0..\37\177..\377") . '"';
    }

    public function testDetectorFindsLeaks(): void
    {
        $this->assertSame('', self::detectLeak("<?php\necho 1;\n"));
        $this->assertSame('', self::detectLeak("<?php\necho 1;\n?>"));
        $this->assertSame('', self::detectLeak("<?php\necho 1;\n?>\n"), 'one newline after ?> is eaten by php');
        $this->assertSame('', self::detectLeak("#!/usr/bin/php\n<?php\necho 1;\n"));
        $this->assertNotSame('', self::detectLeak(" <?php\necho 1;\n"));
        $this->assertNotSame('', self::detectLeak("\n<?php\necho 1;\n"));
        $this->assertNotSame('', self::detectLeak("\xEF\xBB\xBF<?php\necho 1;\n"));
        $this->assertNotSame('', self::detectLeak("<?php\necho 1;\n?>\n\n"));
        $this->assertNotSame('', self::detectLeak("<?php\necho 1;\n?> "));
    }

    /**
     * Real check: the file is included under output buffering and the buffer must stay empty.
     */
    public function testDetectorMatchesRealOutput(): void
    {
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'abc_ob_' . uniqid();
        mkdir($dir);
        $clean = $dir . '/clean.php';
        $dirty = $dir . '/dirty.php';
        file_put_contents($clean, "<?php\n\$a = 1;\n?>\n");
        file_put_contents($dirty, "  <?php\n\$a = 1;\n?>\n\n");

        try {
            foreach ([$clean => false, $dirty => true] as $file => $leaks) {
                ob_start();
                include $file;
                $buffer = ob_get_clean();
                $this->assertSame($leaks, $buffer !== '', basename($file) . ' output buffer');
                $this->assertSame($leaks, self::detectLeak(file_get_contents($file)) !== '');
            }
        } finally {
            unlink($clean);
            unlink($dirty);
            rmdir($dir);
        }
    }

    public function testNoOutputBeforeOpeningOrAfterClosingTags(): void
    {
        $root = self::root();
        $this->assertDirectoryExists($root);

        $allowed = array_map(
            fn($f) => $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $f),
            self::ALLOWED_FILES
        );

        $errors = [];
        $checked = 0;
        foreach (self::phpFiles($root) as $path) {
            if (in_array($path, $allowed, true)) {
                continue;
            }
            $checked++;
            $problem = self::detectLeak((string)file_get_contents($path));
            if ($problem) {
                $errors[] = substr($path, strlen($root) + 1) . ' - ' . $problem;
            }
        }

        $this->assertGreaterThan(0, $checked, 'No php-files found in ' . $root);
        $this->assertSame(
            [],
            $errors,
            "Found php-files that print something to the output buffer on include:\n" . implode("\n", $errors)
        );
    }
}
