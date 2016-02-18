<?php
final class DefaultTextMarketer{
	public $errors = array();
	private $registry;
	private $config;
	private $sender;
	public function __construct(){
		$this->registry = Registry::getInstance();
		$this->registry->get('language')->load('default_textmarketer/default_textmarketer');
		$this->config = $this->registry->get('config');
		try{
			include_once('sendsms.php');
			$this->sender = new SendSMS( $this->config->get('default_textmarketer_username'),
								$this->config->get('default_textmarketer_password'),
								$this->config->get('default_textmarketer_test'));
		}catch(AException $e){}
	}

	public function getProtocol(){
		return 'sms';
	}

	public function getProtocolTitle(){
		return $this->registry->get('language')->get('default_textmarketer_protocol_title') ;
	}

	public function getName(){
		return 'TextMarketer';
	}

	public function send($to, $text){

		$log = $this->registry->get('log');
		try{
			$result = $this->sender->send($text,$to,$this->config->get('store_name'));
//$log->write(var_export($result, true));
		}catch(AException $e){}


//$log->write('SMS sent to: '.$to.', text:'.$text);

		return true;

	}

	public function sendFew($to, $text){
		foreach($to as $uri){
			$this->send($uri, $text);
		}
	}

	public function validateURI($uri){
		$this->errors = array();
		$uri = trim($uri);
		$uri = trim($uri,',');

		$uris = explode(',',$uri);
		foreach($uris as $u){
			$u = trim($u);
			if(!$u){
				continue;
			}
			$u = preg_replace('/[^0-9\+]/','',$u);
			if($u[0]!='+'){
				$u = '+'.$u;
			}
			if(!preg_match('/^\+[1-9]{1}[0-9]{3,14}$/',$u) ){
				$this->errors[] = 'Mobile number '.$u.' is not valid!';
			}
		}

		if($this->errors){
			return false;
		}else{
			return true;
		}
	}

	/**
	 * Function builds form element for storefront side (customer account page)
	 *
	 * @param AForm $form
	 * @param string $value
	 * @return object
	 */
	public function getURIField($form, $value=''){
		$this->registry->get('language')->load('default_textmarketer/default_textmarketer');
		return $form->getFieldHtml(
										array(
		                                        'type' => 'phone',
		                                        'name' => 'sms',
		                                        'value' => $value,
												'label_text' => $this->registry->get('language')->get('entry_sms')
										));
	}
}