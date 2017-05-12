<?php

/**
 * Class SystemCheckTest
 */
class SystemCheckTest extends AbanteCartTest{
	private $original_save_path='';
	protected function setUp(){
		parent::__construct();
		$this->original_save_path = ini_get('session.save_path');
	}

	protected function tearDown(){
		ini_set('session.save_path', $this->original_save_path);
	}

	public function testCheckSessionSavePath(){
		ini_set('session.save_path', '');
		$result = check_session_save_path();
		var_Dump(ini_get('session.save_path'));
		$this->assertEquals($result, array());

		ini_set('session.save_path', '/tmp');
		var_Dump(ini_get('session.save_path'));
		$result = check_session_save_path();
		$this->assertEquals($result, array());

		ini_set('session.save_path', '1;/tmp');
		var_Dump(ini_get('session.save_path'));
		$result = check_session_save_path();
		$this->assertEquals($result, array());


		ini_set('session.save_path', '1;0666;/tmp');
		var_Dump(ini_get('session.save_path'));
		$result = check_session_save_path();
		$this->assertEquals($result, array());

	}
}