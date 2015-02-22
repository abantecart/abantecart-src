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
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

final class APagination {
	public $total = 0;
	public $page = 1;
	public $limit = 20;
	public $split = 5;
	public $limits = array();
	public $num_links = 10;
	public $url = '';
	public $text = 'Showing {start} to {end} of {total} ({pages} Pages)';
	public $text_limit = 'Per Page';
	public $text_first = '|&lt;';
	public $text_last = '&gt;|';
	public $text_next = '&gt;';
	public $text_prev = '&lt;';
	public $style_links = 'links';
	public $style_results = 'results';
	public $style_limits = 'limits';
	 
	public function render() {
		$total = $this->total;		
		if ($this->page < 1) {
			$page = 1;
		} else {
			$page = $this->page;
		}

		if (!$this->limit) {
			$limit = 10;
		} else {
			$limit = $this->limit;
		}

		if(!$this->limits){
			$registry = Registry::getInstance();
			$this->limits[0] = $x = ( $this->split ? $this->split : $registry->get('config')->get('config_catalog_limit') );
			while($x<=50){
				$this->limits[] = $x;
				$x += 10;
			}
		}

		$this->url = str_replace('{limit}',$limit,$this->url);
		$num_links = $this->num_links;
		$num_pages = ceil($total / $limit);
		
		$stdout = '';
		
		if ($page > 1) {
			$stdout .= ' <a class="first" href="' . str_replace('{page}', 1, $this->url) . '">' . $this->text_first . '</a> <a class="previous" href="' . str_replace('{page}', $page - 1, $this->url) . '">' . $this->text_prev . '</a> ';
    	}

		if ($num_pages > 1) {
			if ($num_pages <= $num_links) {
				$start = 1;
				$end = $num_pages;
			} else {
				$start = $page - floor($num_links / 2);
				$end = $page + floor($num_links / 2);
			
				if ($start < 1) {
					$end += abs($start) + 1;
					$start = 1;
				}
						
				if ($end > $num_pages) {
					$start -= ($end - $num_pages);
					$end = $num_pages;
				}
			}

			if ($start > 1) {
				$stdout .= ' .... ';
			}

			for ($i = $start; $i <= $end; $i++) {
				if ($page == $i) {
					$stdout .= ' <b>' . $i . '</b> ';
				} else {
					$stdout .= ' <a href="' . str_replace('{page}', $i, $this->url) . '">' . $i . '</a> ';
				}	
			}
							
			if ($end < $num_pages) {
				$stdout .= ' .... ';
			}
		}
		
   		if ($page < $num_pages) {
			$stdout .= ' <a class="next" href="' . str_replace('{page}', $page + 1, $this->url) . '">' . $this->text_next . '</a> <a class="last" href="' . str_replace('{page}', $num_pages, $this->url) . '">' . $this->text_last . '</a> ';
		}
		
		$find = array(
			'{start}',
			'{end}',
			'{total}',
			'{pages}',
			'{limit}'
		);
		
		$replace = array(
			($total) ? (($page - 1) * $limit) + 1 : 0,
			((($page - 1) * $limit) > ($total - $limit)) ? $total : ((($page - 1) * $limit) + $limit),
			$total, 
			$num_pages
		);

		$registry = Registry::getInstance();
		if ( !in_array($this->limit, $this->limits) ) {
			$this->limits[] = $this->limit;
			sort($this->limits);
		}
		$options = array();
		foreach($this->limits as $item){
			$options[$item] = $item;
		}

		$limit_select = $registry->get('html')->buildSelectbox( array(
			                                                        'name' => 'limit',
			                                                        'value'=> $this->limit,
			                                                        'options' => $options,
			                                                        'style' => '',
			                                                        'attr' => ' onchange="location=\'' . str_replace('{page}', 1, $this->url) . '&limit=\'+this.value;"',

		                                                        )

		);

		$limit_select = str_replace('&', '&amp;', $limit_select);
		$limit_select .= '&nbsp;&nbsp;' . $this->text_limit;
		
		return ($limit_select ? '<div class="' . $this->style_limits . '">' . $limit_select . '</div>' : '') . ($stdout ? '<div class="' . $this->style_links . '">' . $stdout . '</div>' : '') . '<div class="' . $this->style_results . '">' . str_replace($find, $replace, $this->text) . '</div>';
	}
}
?>