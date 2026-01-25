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

namespace helper;

use phpunit\AbanteCartTestBootstrap;

/**
 * Class SystemCheckTest
 */
class SystemCheckTest extends AbanteCartTestBootstrap
{
    private $original_save_path = '';

    public function setUp(): void
    {
        parent::bootstrap();
        $this->original_save_path = ini_get('session.save_path');
    }

    protected function tearDown(): void
    {
        ini_set('session.save_path', $this->original_save_path);
    }

    public function testCheckTmpSessionSavePath()
    {
        ini_set('session.save_path', '/tmp');
        $result = check_session_save_path();
        $this->assertEquals([], $result);
    }

    public function testCheckTmp2SessionSavePath()
    {
        ini_set('session.save_path', '1;/tmp');
        $result = check_session_save_path();
        $this->assertEquals([], $result);
    }

    public function testCheckTmp3SessionSavePath()
    {
        ini_set('session.save_path', '1;0666;/tmp');
        $result = check_session_save_path();
        $this->assertEquals([], $result);
    }
}