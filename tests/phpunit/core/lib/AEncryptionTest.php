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

use AEncryption;
use PHPUnit\Framework\Attributes\DataProvider;
use phpunit\AbanteCartTestBootstrap;

class AEncryptionTest extends AbanteCartTestBootstrap
{
    private const KEY = 'phpunit-symmetric-key-0123456789';

    protected function setUp(): void
    {
        parent::bootstrap();
    }

    public static function plainValuesProvider(): array
    {
        return [
            'simple'     => ['test text'],
            'token'      => ['admin::' . str_repeat('a1', 16)],
            'unicode'    => ['Привет, мир! ✓'],
            'serialized' => [serialize(['customer_id' => 5, 'email' => 'a@b.c'])],
            'long'       => [str_repeat('0123456789', 100)],
        ];
    }

    #[DataProvider('plainValuesProvider')]
    public function testEncryptDecryptRoundTrip(string $value)
    {
        $enc = new AEncryption(self::KEY);
        $encrypted = $enc->encrypt($value);

        $this->assertNotSame($value, $encrypted);
        $this->assertSame($value, $enc->decrypt($encrypted));
    }

    public function testEncryptedValueIsUrlSafe()
    {
        $enc = new AEncryption(self::KEY);
        for ($i = 0; $i < 20; $i++) {
            $encrypted = $enc->encrypt(random_bytes(32));
            $this->assertDoesNotMatchRegularExpression('/[+\/]/', $encrypted);
        }
    }

    public function testEncryptionUsesRandomIv()
    {
        $enc = new AEncryption(self::KEY);
        $this->assertNotSame($enc->encrypt('same value'), $enc->encrypt('same value'));
    }

    public function testDecryptWithAnotherKeyDoesNotRevealValue()
    {
        $encrypted = (new AEncryption(self::KEY))->encrypt('secret value');
        $this->assertNotSame('secret value', (new AEncryption('another-key'))->decrypt($encrypted));
    }

    public function testEmptyKeyReturnsValueAsIs()
    {
        $enc = new AEncryption('');
        $this->assertSame('plain', $enc->encrypt('plain'));
        $this->assertSame('plain', $enc->decrypt('plain'));
    }

    public function testDecryptOfGarbageReturnsEmptyString()
    {
        //user can pass any value (rtoken in GET, customer cookie)
        $enc = new AEncryption(self::KEY);
        $this->assertSame('', $enc->decrypt('not-encrypted-value'));
    }

    public function testGetHash()
    {
        $expected = defined('SALT') ? md5('password' . SALT) : md5('passwordSALT');
        $this->assertSame($expected, AEncryption::getHash('password'));
    }
}
