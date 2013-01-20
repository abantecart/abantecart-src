<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

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

final class ADocument {
	private $title;
	private $description;
	private $keywords;
	private $base;
	private $charset = 'utf-8';
	private $language = 'en-gb';
	private $direction = 'ltr';
	private $links = array();
	private $styles = array();
	private $scripts = array();
	private $breadcrumbs = array();

	public function setTitle($title) {
		$this->title = $title;
	}

	public function getTitle() {
		return $this->title;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setKeywords($keywords) {
		$this->keywords = $keywords;
	}

	public function getKeywords() {
		return $this->keywords;
	}

	public function setBase($base) {
		$this->base = $base;
	}

	public function getBase() {
		return $this->base;
	}

	public function setCharset($charset) {
		$this->charset = $charset;
	}

	public function getCharset() {
		return $this->charset;
	}

	public function setLanguage($language) {
		$this->language = $language;
	}

	public function getLanguage() {
		return $this->language;
	}

	public function setDirection($direction) {
		$this->direction = $direction;
	}

	public function getDirection() {
		return $this->direction;
	}

	public function resetLinks() {
		$this->links = array();
	}

	/**
	 * method add new Link item
	 *
	 * @param array $item("href"=>"","rel"=>"")
	 * Examples: href => 'www.google.com', 'rel'  => 'canonical'
	 * @return null
	 */
	public function addLink($link_item = array()) {
		if ($link_item[ "href" ]) {
			$this->links[ ] = $link_item;
		}
	}

	public function getLinks() {
		return $this->links;
	}

	public function resetStyles() {
		$this->styles = array();
	}

	/**
	 * method add new Style item
	 *
	 * @param array $item("href"=>"","rel"=>"","media"=>)
	 * Examples: href => 'www.google.com', $rel = 'stylesheet', $media = 'screen'
	 * @return null
	 */
	public function addStyle($style_item = array()) {
		if ($style_item[ "href" ]) {
			$this->styles[ ] = $style_item;
		}
	}

	public function getStyles() {
		return $this->styles;
	}

	public function resetScripts() {
		$this->scripts = array();
	}

	public function addScript($script) {
		$this->scripts[ ] = $script;
	}

	public function getScripts() {
		return $this->scripts;
	}

	/**
	 * method to reset breadcrumbs array
	 *
	 * @param: none
	 * @return: nothing
	 */
	public function resetBreadcrumbs() {
		$this->breadcrumbs = array();
	}

	/**
	 * method to initialize Breadcrumbs aray and add root attribute
	 *
	 * @param array $item("href"=>"", "text"=>"", "separator"=>)
	 * @return: nothing
	 */
	public function initBreadcrumb($breadcrumb_item = array()) {
		$this->resetBreadcrumbs();

		$this->addBreadcrumb($breadcrumb_item);
	}

	/**
	 * method add new Breadcrumb item
	 *
	 * @param array $item("href"=>"", "text"=>"", "separator"=>)
	 * @return null
	 */
	public function addBreadcrumb($breadcrumb_item = array()) {
		if ($breadcrumb_item[ "href" ]) {
			$this->breadcrumbs[ ] = $breadcrumb_item;
		}
	}

	public function getBreadcrumbs() {
		return $this->breadcrumbs;
	}


	/**
	 * trims text with set length and ellipes
	 * @param string $input text to trim
	 * @param int $length in characters to trim to
	 * @param bool $ellipses if ellipses (...) are to be added
	 * @param bool $strip_html if html tags are to be stripped
	 * @return string
	 */
	public function trimText($input, $length, $ellipses = true, $strip_html = false) {
		//strip tags, if desired
		if ($strip_html) {
			$input = strip_tags($input);
		}

		//no need to trim, already shorter than trim length
		if (strlen($input) <= $length) {
			return $input;
		}

		//find last space within length
		$last_space = strrpos(substr($input, 0, $length), ' ');
		$trimmed_text = substr($input, 0, $last_space);

		//add ellipses (...)
		if ($ellipses) {
			$trimmed_text .= '...';
		}

		return $trimmed_text;
	}
}

?>