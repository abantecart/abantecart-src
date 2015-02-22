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

final class AMail {
	protected $to;
	protected $from;
	protected $sender;
	protected $subject;
	protected $text;
	protected $html;
	protected $attachments = array();
	protected $headers = array();
	/**
	 * @var AMessage
	 */
	protected $messages;
	/**
	 * @var ALog
	 */
	protected $log;
	public $protocol = 'mail';
	protected $hostname;
	protected $username;
	protected $password;
	protected $port = 25;
	protected $timeout = 5;
	public $newline = "\n";
	public $crlf = "\r\n";
	public $verp = FALSE;
	public $parameter = '';
	public $error = array();

	/**
	 * @param null | AConfig $config
	 */
	public function __construct($config = null) {
		$registry = Registry::getInstance();
		$config = is_object($config) ? $registry->get('config') : $config;
		//set default configuration values
		$this->protocol = $config->get('config_mail_protocol');
		$this->parameter = $config->get('config_mail_parameter');
		$this->hostname = $config->get('config_smtp_host');
		$this->username = $config->get('config_smtp_username');
		$this->password = $config->get('config_smtp_password');
		$this->port = $config->get('config_smtp_port');
		$this->timeout = $config->get('config_smtp_timeout');
		$this->log = $registry->get('log');
		$this->messages = $registry->get('messages');
	}

	public function setTo($to) {
		$this->to = $to;
	}

	public function setFrom($from) {
		$this->from = $from;
	}

	public function addheader($header, $value) {
		$this->headers[$header] = $value;
	}

	public function setSender($sender) {
		$this->sender = $sender;
	}

	public function setSubject($subject) {
		$this->subject = $subject;
	}

	public function setText($text) {
		$this->text = $text;
	}

	public function setHtml($html) {
		$this->html = $html;
	}

	public function addAttachment($file, $filename = '') {
		if (!$filename) {
			$filename = md5(pathinfo($file, PATHINFO_FILENAME)) . '.' . pathinfo($file, PATHINFO_EXTENSION);
		}

		$this->attachments[] = array(
			'filename' => $filename,
			'file' => $file
		);
	}

	public function send() {

		if (defined('IS_DEMO') && IS_DEMO) {
			return null;
		}

		if (!$this->to) {

			$error = 'Error: E-Mail to required!';
			$this->log->write($error);
			$this->error[] = $error;
			$this->messages->saveError('Mailer error!', 'Can\'t send emails. Please see log for details and check your mail settings.');
			return false;
		}

		if (!$this->from) {
			$error = 'Error: E-Mail from required!';
			$this->log->write($error);
			$this->error[] = $error;
			$this->messages->saveError('Mailer error!', 'Can\'t send emails. Please see log for details and check your mail settings.');
			return false;
		}

		if (!$this->sender) {
			$error = 'Error: E-Mail sender required!';
			$this->log->write($error);
			$this->error[] = $error;
			$this->messages->saveError('Mailer error!', 'Can\'t send emails. Please see log for details and check your mail settings.');
			return false;
		}

		if (!$this->subject) {
			$error = 'Error: E-Mail subject required!';
			$this->log->write($error);
			$this->error[] = $error;
			$this->messages->saveError('Mailer error!', 'Can\'t send emails. Please see log for details and check your mail settings.');
			return false;
		}

		if ((!$this->text) && (!$this->html)) {
			$error = 'Error: E-Mail message required!';
			$this->log->write($error);
			$this->error[] = $error;
			$this->messages->saveError('Mailer error!', 'Can\'t send emails. Please see log for details and check your mail settings.');
			return false;
		}

		if (is_array($this->to)) {
			$to = implode(',', $this->to);
		} else {
			$to = $this->to;
		}

		$boundary = '----=_NextPart_' . md5(rand());

		$header = '';
		if ($this->protocol != 'mail') {
			$header .= 'To: ' . $to . $this->newline;
			$header .= 'Subject: ' . '=?UTF-8?B?' . base64_encode($this->subject) . '?=' . $this->newline;
		}

		$header .= 'Date: ' . date('D, d M Y H:i:s O') . $this->newline;
		$header .= 'From: ' . '=?UTF-8?B?' . base64_encode($this->sender) . '?=' . '<' . $this->from . '>' . $this->newline;
		$header .= 'Reply-To: ' . '=?UTF-8?B?' . base64_encode($this->sender) . '?=' . '<' . $this->from . '>' . $this->newline;

		$header .= 'Return-Path: ' . $this->from . $this->newline;
		$header .= 'X-Mailer: PHP/' . phpversion() . $this->newline;
		$header .= 'MIME-Version: 1.0' . $this->newline;
		$header .= 'Content-Type: multipart/related; boundary="' . $boundary . '"' . $this->newline . $this->newline;

		if (!$this->html) {
			$message = '--' . $boundary . $this->newline;
			$message .= 'Content-Type: text/plain; charset="utf-8"' . $this->newline;
			$message .= 'Content-Transfer-Encoding: 8bit' . $this->newline . $this->newline;
			$message .= $this->text . $this->newline;
		} else {
			$message = '--' . $boundary . $this->newline;
			$message .= 'Content-Type: multipart/alternative; boundary="' . $boundary . '_alt"' . $this->newline . $this->newline;
			$message .= '--' . $boundary . '_alt' . $this->newline;
			$message .= 'Content-Type: text/plain; charset="utf-8"' . $this->newline;
			$message .= 'Content-Transfer-Encoding: 8bit' . $this->newline . $this->newline;

			if ($this->text) {
				$message .= $this->text . $this->newline;
			} else {
				$message .= 'This is a HTML email and your email client software does not support HTML email!' . $this->newline;
			}

			$message .= '--' . $boundary . '_alt' . $this->newline;
			$message .= 'Content-Type: text/html; charset="utf-8"' . $this->newline;
			$message .= 'Content-Transfer-Encoding: base64' . $this->newline . $this->newline;
			$message .= chunk_split(base64_encode($this->html)) . $this->newline;
			$message .= '--' . $boundary . '_alt--' . $this->newline;
		}

		foreach ($this->attachments as $attachment) {
			if (file_exists($attachment['file'])) {
				$handle = fopen($attachment['file'], 'r');
				$content = fread($handle, filesize($attachment['file']));

				fclose($handle);

				$message .= '--' . $boundary . $this->newline;
				$message .= 'Content-Type: application/octet-stream' . $this->newline;
				$message .= 'Content-Transfer-Encoding: base64' . $this->newline;
				$message .= 'Content-Disposition: attachment; filename="' . $attachment['filename'] . '"' . $this->newline;
				$message .= 'Content-ID: <' . basename(urlencode($attachment['filename'])) . '>' . $this->newline;
				$message .= 'X-Attachment-Id: ' . basename(urlencode($attachment['filename'])) . $this->newline . $this->newline;
				$message .= chunk_split(base64_encode($content));
			}
		}

		$message .= '--' . $boundary . '--' . $this->newline;

		if ($this->protocol == 'mail') {
			ini_set('sendmail_from', $this->from);

			if ($this->parameter) {
				mail($to, '=?UTF-8?B?' . base64_encode($this->subject) . '?=', $message, $header, $this->parameter);
			} else {
				mail($to, '=?UTF-8?B?' . base64_encode($this->subject) . '?=', $message, $header);
			}

		} elseif ($this->protocol == 'smtp') {
			$handle = fsockopen($this->hostname, (int)$this->port, $errno, $errstr, (int)$this->timeout);

			if (!$handle) {
				$error = 'Error: ' . $errstr . ' (' . $errno . ')';
				$this->log->write($error);
				$this->error[] = $error;
			} else {
				if (substr(PHP_OS, 0, 3) != 'WIN') {
					socket_set_timeout($handle, $this->timeout, 0);
				}

				while ($line = fgets($handle, 515)) {
					if (substr($line, 3, 1) == ' ') {
						break;
					}
				}

				if (substr($this->hostname, 0, 3) == 'tls') {
					fputs($handle, 'STARTTLS' . $this->crlf);
					$reply = '';
					while ($line = fgets($handle, 515)) {
						$reply .= $line;

						if (substr($line, 3, 1) == ' ') {
							break;
						}
					}

					if (substr($reply, 0, 3) != 220) {
						$error = 'Error: STARTTLS not accepted from server!';
						$this->log->write($error);
						$this->error[] = $error;
					}
				}

				if (!empty($this->username) && !empty($this->password)) {
					fputs($handle, 'EHLO ' . getenv('SERVER_NAME') . $this->crlf);

					$reply = '';

					while ($line = fgets($handle, 515)) {
						$reply .= $line;

						if (substr($line, 3, 1) == ' ') {
							break;
						}
					}

					if (substr($reply, 0, 3) != 250) {
						$error = 'Error: EHLO not accepted from server!';
						$this->log->write($error);
						$this->error[] = $error;
					}

					fputs($handle, 'AUTH LOGIN' . $this->crlf);

					$reply = '';

					while ($line = fgets($handle, 515)) {
						$reply .= $line;

						if (substr($line, 3, 1) == ' ') {
							break;
						}
					}

					if (substr($reply, 0, 3) != 334) {
						$error = 'Error: AUTH LOGIN not accepted from server!';
						$this->log->write($error);
						$this->error[] = $error;
					}

					fputs($handle, base64_encode($this->username) . $this->crlf);

					$reply = '';

					while ($line = fgets($handle, 515)) {
						$reply .= $line;

						if (substr($line, 3, 1) == ' ') {
							break;
						}
					}

					if (substr($reply, 0, 3) != 334) {
						$error = 'Error: Username not accepted from server!';
						$this->log->write($error);
						$this->error[] = $error;
					}

					fputs($handle, base64_encode($this->password) . $this->crlf);

					$reply = '';

					while ($line = fgets($handle, 515)) {
						$reply .= $line;

						if (substr($line, 3, 1) == ' ') {
							break;
						}
					}

					if (substr($reply, 0, 3) != 235) {
						$error = 'Error: Password not accepted from server!';
						$this->log->write($error);
						$this->error[] = $error;
					}
				} else {
					fputs($handle, 'HELO ' . getenv('SERVER_NAME') . $this->crlf);

					$reply = '';

					while ($line = fgets($handle, 515)) {
						$reply .= $line;

						if (substr($line, 3, 1) == ' ') {
							break;
						}
					}

					if (substr($reply, 0, 3) != 250) {
						$error = 'Error: HELO not accepted from server!';
						$this->log->write($error);
						$this->error[] = $error;
					}
				}

				if ($this->verp) {
					fputs($handle, 'MAIL FROM: <' . $this->from . '>XVERP' . $this->crlf);
				} else {
					fputs($handle, 'MAIL FROM: <' . $this->from . '>' . $this->crlf);
				}

				$reply = '';

				while ($line = fgets($handle, 515)) {
					$reply .= $line;

					if (substr($line, 3, 1) == ' ') {
						break;
					}
				}

				if (substr($reply, 0, 3) != 250) {
					$error = 'Error: MAIL FROM not accepted from server!';
					$this->log->write($error);
					$this->error[] = $error;
				}

				if (!is_array($this->to)) {
					fputs($handle, 'RCPT TO: <' . $this->to . '>' . $this->crlf);

					$reply = '';

					while ($line = fgets($handle, 515)) {
						$reply .= $line;

						if (substr($line, 3, 1) == ' ') {
							break;
						}
					}

					if ((substr($reply, 0, 3) != 250) && (substr($reply, 0, 3) != 251)) {
						$error = 'Error: RCPT TO not accepted from server!';
						$this->log->write($error);
						$this->error[] = $error;
					}
				} else {
					foreach ($this->to as $recipient) {
						fputs($handle, 'RCPT TO: <' . $recipient . '>' . $this->crlf);

						$reply = '';

						while ($line = fgets($handle, 515)) {
							$reply .= $line;

							if (substr($line, 3, 1) == ' ') {
								break;
							}
						}

						if ((substr($reply, 0, 3) != 250) && (substr($reply, 0, 3) != 251)) {
							$error = 'Error: RCPT TO not accepted from server!';
							$this->log->write($error);
							$this->error[] = $error;
						}
					}
				}

				fputs($handle, 'DATA' . $this->crlf);

				$reply = '';

				while ($line = fgets($handle, 515)) {
					$reply .= $line;

					if (substr($line, 3, 1) == ' ') {
						break;
					}
				}

				if (substr($reply, 0, 3) != 354) {
					$error = 'Error: DATA not accepted from server!';
					$this->log->write($error);
					$this->error[] = $error;
				}

				fputs($handle, $header . $message . $this->crlf);
				fputs($handle, '.' . $this->crlf);

				$reply = '';

				while ($line = fgets($handle, 515)) {
					$reply .= $line;

					if (substr($line, 3, 1) == ' ') {
						break;
					}
				}

				if (substr($reply, 0, 3) != 250) {
					$error = 'Error: DATA not accepted from server!';
					$this->log->write($error);
					$this->error[] = $error;
				}

				fputs($handle, 'QUIT' . $this->crlf);

				$reply = '';

				while ($line = fgets($handle, 515)) {
					$reply .= $line;

					if (substr($line, 3, 1) == ' ') {
						break;
					}
				}

				if (substr($reply, 0, 3) != 221) {
					$error = 'Error: QUIT not accepted from server!';
					$this->log->write($error);
					$this->error[] = $error;
				}

				fclose($handle);
			}
		}
		if ($this->error) {
			$this->messages->saveError('Mailer error!', 'Can\'t send emails. Please see log for details and check your mail settings.');
		}
	}
}
