<?php

/**
 * Class SystemCheckTest
 */
class SystemCheckTest extends AbanteCartTest
{
    private $original_save_path = '';

    public function setUp(): void
    {
        parent::__construct();
        $this->original_save_path = ini_get('session.save_path');
    }

    protected function tearDown(): void
    {
        ini_set('session.save_path', $this->original_save_path);
    }

    public function testCheckEmptySessionSavePath()
    {
        ini_set('session.save_path', '');
        $result = check_session_save_path();
        $this::assertEquals($result, array());
    }

    public function testCheckTmpSessionSavePath()
    {
        ini_set('session.save_path', '/tmp');
        $result = check_session_save_path();
        $this->assertEquals($result, array());
    }

    public function testCheckTmp2SessionSavePath()
    {
        ini_set('session.save_path', '1;/tmp');
        $result = check_session_save_path();
        $this->assertEquals($result, array());
    }

    public function testCheckTmp3SessionSavePath()
    {
        ini_set('session.save_path', '1;0666;/tmp');
        $result = check_session_save_path();
        $this->assertEquals($result, array());
    }
}