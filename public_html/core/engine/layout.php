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
class ALayout {
	/**
	 * @var Registry
	 */
	protected $registry;
	private $page = array();
	private $layout = array();
	public $blocks = array();
	/**
	 * @var string
	 */
	private $tmpl_id;
	/**
	 * @var int
	 */
	private $layout_id;
	/**
	 * @var int
	 */
	public $page_id;

	/**
	 * @param $registry Registry
	 * @param int $template_id
	 */
	public function __construct($registry, $template_id) {
		$this->registry = $registry;
		$this->tmpl_id = $template_id;
		$this->page_id = '';
	}

	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

	/**
	 * @param string $controller
	 * @return int
	 * @throws AException
	 */
	public function buildPageData($controller) {

		//for Maintenance mode
		if ($this->config->get('config_maintenance')) {
			/** @noinspection PhpIncludeInspection */
			require_once(DIR_CORE . "lib/user.php");
			$this->registry->set('user', new AUser($this->registry));
			if (!$this->user->isLogged()) {
				$controller = 'pages/index/maintenance';
			}
		}

		// Locate and set page information. This needs to be called once per page 
		$unique_page = array();

		// find page records for given controller
		$key_param = $this->getKeyParamByController($controller);
		$key_value = $key_param ? $this->request->get[$key_param] : null;
		// for nested categories
		if ($key_param == 'path' && $key_value && is_int(strpos($key_value, '_'))) {
			$key_value = (int)substr($key_value, strrpos($key_value, '_') + 1);
		}

		$key_param = is_null($key_value) ? null : $key_param;

		$pages = $this->getPages($controller, $key_param, $key_value);
		if (empty($pages)) {
			//if no specific page found try to get page for group
			$new_path = preg_replace('/\/\w*$/', '', $controller);
			$pages = $this->getPages($new_path);
		}

		if (empty($pages)) {
			//if no specific page found load generic
			$pages = $this->getPages('generic');

		} else {
			/* if specific pages with key_param presents...
			 in any case first row will be that what we need (see sql "order by" in getPages method) */
			$unique_page = $pages[0];
		}
		// look for key_param and key_value in the request
		/*
		Steps to perform
		1. Based on rt (controller) select all rows from pages table where controller = "$controller"  
		2. Based on $key_param = key_param from pages table for given $controller locate this $key_param value from CGI input. 
		You will have $key_param and $key_value pair. 
		NOTE: key_param will be unique per controller. More optimized select can be used to get key_param from pages table.
		3. Locate id from pages table based on $key_param and $key_value pair
		 where controller = "$controller" and key_param = $key_param and key_value = $this->request->get[$key_param];
		NOTE: Do select only if value present.
		4. If locate page id use the layout.
		*/

		$this->page = !empty($unique_page) ? $unique_page : $pages[0];
		$this->page_id = $this->page['page_id'];
		//if no page found set default page id 1
		if (empty($this->page_id)) {
			$this->page_id = 1;
		}

		//Get the page layout
		$layouts = $this->getLayouts(1);
		if (sizeof($layouts) == 0) {
			//No page specific layout found, load default layout
			$layouts = $this->getDefaultLayout();
			if (sizeof($layouts) == 0) {
				// ????? How to terminate ????
				throw new AException(AC_ERR_LOAD_LAYOUT, 'No layout found for page_id/controller ' . $this->page_id . '::' . $this->page['controller'] . '! ' . genExecTrace('full'));
			}
		}

		$this->layout = $layouts[0];
		$this->layout_id = $this->layout['layout_id'];

		// Get all blacks for the page;
		$blocks = $this->getlayoutBlocks($this->layout_id);
		$this->blocks = $blocks;
		return $this->page_id;
	}

	/**
	 * @param string $controller
	 * @param string $key_param
	 * @param string $key_value
	 * @return array|null
	 */
	public function getPages($controller = '', $key_param = '', $key_value = '') {
		$store_id = (int)$this->config->get('config_store_id');
		$cache_name = 'layout.pages'
				. (!empty($controller) ? '.' . $controller : '')
				. (!empty($key_param) ? '.' . $key_param : '')
				. (!empty($key_value) ? '.' . $key_value : '');
		$cache_name = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_name);
		$pages = $this->cache->get($cache_name, '', $store_id);
		if (!is_null($pages)) {
			// return cached pages
			return $pages;
		}

		$where = '';
		if (!empty ($controller)) {
			$where .= "WHERE controller = '" . $this->db->escape($controller) . "' ";
			if (!empty ($key_param)) {
				if (!empty ($key_value)) {
					// so if we have key_param key_value pair we select pages with controller and with or without key_param
					$where .= " AND ( COALESCE( key_param, '' ) = ''
										OR
										( key_param = '" . $this->db->escape($key_param) . "'
											AND key_value = '" . $this->db->escape($key_value) . "' ) )
								AND template_id = '".$this->tmpl_id."' ";

				} else { //write to log this stuff. it's abnormal situation
					$message = "Error: Error in data of page with controller: '" . $controller . "'. Please check for key_value present where key_param was set";
					$this->messages->saveError('Error', $message);
					$error = new AError ($message);
					$error->toLog()->toDebug();
				}
			}
		}
		
		$sql = "SELECT p.page_id, controller, key_param, key_value, p.date_added, p.date_modified "
				. "FROM " . $this->db->table("pages") . " p "
				."LEFT JOIN " . $this->db->table("pages_layouts") . " pl ON pl.page_id = p.page_id "
				."LEFT JOIN " . $this->db->table("layouts") . " l ON l.layout_id = pl.layout_id "
				. $where
				. "ORDER BY key_param DESC, key_value DESC, p.page_id ASC";
		$query = $this->db->query($sql);
		$pages = $query->rows;
		$this->cache->set($cache_name, $pages, '', $store_id);
		return $pages;
	}

	/**
	 * @param string $controller
	 * @return string
	 */
	public function getKeyParamByController($controller = '') {
		$key = null;
		switch ($controller) {
			case 'pages/product/product':
				$key = 'product_id';
				break;
			case 'pages/product/manufacturer':
				$key = 'manufacturer_id';
				break;
			case 'pages/product/category':
				$key = 'path';
				break;
			case 'pages/content/content':
				$key = 'content_id';
				break;
			default:
				$key = '';
				break;
		}

		return $key;
	}

	public function getDefaultLayout() {
		$store_id = (int)$this->config->get('config_store_id');
		
		$cache_name = 'layout.default.' . $this->tmpl_id;
		$cache_name = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_name);
		$layouts = $this->cache->get($cache_name, '', $store_id);
		if (isset($layouts)) {
			// return cached layouts
			return $layouts;
		}

		$where = 'WHERE template_id = "' . $this->db->escape($this->tmpl_id) . '" ';
		$where .= " AND layout_type = '0' ";

		$sql = "SELECT "
				. "layout_id, "
				. "layout_type, "
				. "layout_name, "
				. "date_added, "
				. "date_modified "
				. "FROM "
				. $this->db->table("layouts") . " "
				. $where
				. " ORDER BY "
				. "layout_id Asc";

		$query = $this->db->query($sql);

		$layouts = $query->rows;

		$this->cache->set($cache_name, $layouts, '', $store_id);

		return $layouts;
	}

	/**
	 * @param string $layout_type
	 * @return array|null
	 */
	public function getLayouts($layout_type = '') {
		//No page id, not need to be here
		if (empty($this->page_id)) {
			return null;
		}
		$store_id = (int)$this->config->get('config_store_id');		
		$cache_name = 'layout.layouts.' . $this->tmpl_id . '.' . $this->page_id
				. (!empty($layout_type) ? '.' . $layout_type : '');
		$cache_name = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_name);
		$layouts = $this->cache->get($cache_name, '', $store_id);
		if (!is_null($layouts)) {
			// return cached layouts
			return $layouts;
		}

		$where = 'WHERE template_id = "' . $this->db->escape($this->tmpl_id) . '" ';
		$join = ", " . $this->db->table("pages_layouts") . " as pl ";
		$where .= " AND pl.page_id = '" . (int)$this->page_id . "' AND l.layout_id = pl.layout_id ";

		if (!empty($layout_type)) {
			$where .= empty($layout_type) ? "" : "AND layout_type = '" . (int)$layout_type . "' ";
		}

		$sql = "SELECT "
				. "l.layout_id as layout_id, "
				. "l.layout_type as layout_type, "
				. "l.layout_name as layout_name, "
				. "l.date_added as date_added, "
				. "l.date_modified as date_modified "
				. "FROM "
				. $this->db->table("layouts") . " as l "
				. $join
				. $where
				. " ORDER BY "
				. "l.layout_id Asc";

		$query = $this->db->query($sql);
		$layouts = $query->rows;
		$this->cache->set($cache_name, $layouts, '', $store_id);

		return $layouts;
	}

	/**
	 * @param int $layout_id
	 * @return array|null
	 * @throws AException
	 */
	public function getlayoutBlocks($layout_id) {
		if (empty($layout_id)) {
			throw new AException(AC_ERR_LOAD_LAYOUT, 'No layout specified for getlayoutBlocks!');
		}
		$store_id = (int)$this->config->get('config_store_id');
		$cache_name = 'layout.blocks.' . $layout_id;
		$cache_name = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_name);
		$blocks = $this->cache->get($cache_name, '', $store_id);
		if (!is_null($blocks)) {
			// return cached blocks
			return $blocks;
		}

		$where = "WHERE bl.layout_id = '" . $layout_id . "' ";
		$where .= "AND bl.block_id = b.block_id AND bl.status = 1 ";

		$sql = "SELECT "
				. "bl.instance_id as instance_id, "
				. "b.block_id as block_id, "
				. "bl.custom_block_id, "
				. "bl.parent_instance_id as parent_instance_id, "
				. "bl.position as position, "
				. "b.block_txt_id as block_txt_id, "
				. "b.controller as controller "
				. "FROM "
				. $this->db->table("blocks") . " as b, "
				. $this->db->table("block_layouts") . " as bl "
				. $where
				. "ORDER BY "
				. "bl.parent_instance_id Asc, bl.position Asc";

		$query = $this->db->query($sql);
		$blocks = $query->rows;

		$this->cache->set($cache_name, $blocks, '', $store_id);

		return $blocks;
	}

	/**
	 * @param int $instance_id
	 * @return array
	 */
	public function getChildren($instance_id = 0) {
		$children = array();
		// Look into all blocks and locate all children
		foreach ($this->blocks as $block) {
			if ((string)$block['parent_instance_id'] == (string)$instance_id) {
				array_push($children, $block);
			}
		}
		return $children;
	}

	/**
	 * @param $child_instance_id
	 * @return array
	 */
	public function getBlockDetails($child_instance_id) {
		//Select block details by controller
		foreach ($this->blocks as $block) {
			if ($block['instance_id'] == $child_instance_id) {
				return $block;
			}
		}
		return array();
	}

	/**
	 * @param int $instance_id
	 * @param string $newchild
	 * @param string $block_txt_id
	 * @param string $template
	 */
	public function addChildFirst($instance_id, $newchild, $block_txt_id, $template) {
		$new_block = array();
		$new_block['parent_instance_id'] = $instance_id;
		$new_block['instance_id'] = $block_txt_id . $instance_id;
		$new_block['block_id'] = $block_txt_id;
		$new_block['controller'] = $newchild;
		$new_block['block_txt_id'] = $block_txt_id;
		$new_block['template'] = $template;
		array_unshift($this->blocks, $new_block);
	}

	/**
	 * @param int $instance_id
	 * @param string $newchild
	 * @param string $block_txt_id
	 * @param string $template
	 */
	public function addChild($instance_id, $newchild, $block_txt_id, $template) {
		$new_block = array();
		$new_block['parent_instance_id'] = $instance_id;
		$new_block['instance_id'] = $block_txt_id . $instance_id;
		$new_block['block_id'] = $block_txt_id;
		$new_block['controller'] = $newchild;
		$new_block['block_txt_id'] = $block_txt_id;
		$new_block['template'] = $template;
		array_push($this->blocks, $new_block);
	}

	/**
	 * @param int $instance_id
	 * @return string
	 */
	public function getBlockTemplate($instance_id) {
		//Select block and parent id by controller
		$block_id = '';
		$parent_block_id = '';
		$parent_instance_id = '';
		$template = '';
		$store_id = (int)$this->config->get('config_store_id');
		
		//locate block id
		foreach ($this->blocks as $block) {
			if ($block['instance_id'] == $instance_id) {
				$block_id = $block['block_id'];
				$parent_instance_id = $block['parent_instance_id'];
				$template = !empty($block['template']) ? $block['template'] : '';
				break;
			}
		}

		//Check if we do not have template set yet in the code
		if ($template) {
			return $template;
		}
		//locate true parent_block id. Not to confuse with parent_instance_id
		foreach ($this->blocks as $block) {
			if ($block['instance_id'] == $parent_instance_id) {
				$parent_block_id = $block['block_id'];
				break;
			}
		}
		if (!empty($block_id) && !empty($parent_block_id)) {

			$cache_name = 'layout.block.template.' . $block_id . '.' . $parent_block_id;
			$cache_name = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_name);
			$template = $this->cache->get($cache_name, '', $store_id);
			if (isset($template)) {
				// return cached $template
				return $template;
			}

			$where = 'WHERE bt.block_id = "' . (int)($block_id) . '" ';
			//locate template based on block parent ID or 0 if generic template is set 
			$where .= 'AND bt.parent_block_id in (' . (int)$parent_block_id . ', 0) ';

			$sql = "SELECT "
					. "bt.template as template, "
					. "bt.date_added as date_added, "
					. "bt.date_modified as date_modified "
					. "FROM "
					. $this->db->table("block_templates") . " as bt "
					. $where
					. "ORDER BY "
					. "bt.parent_block_id Desc";

			$query = $this->db->query($sql);
			if ($query->num_rows) {
				$this->cache->set($cache_name, $query->rows[0]['template'], '', $store_id);
				return $query->rows[0]['template'];
			} else {
				return '';
			}
		}
		return '';
	}

	/**
	 * @param int $custom_block_id
	 * @return array
	 */
	public function getBlockDescriptions($custom_block_id = 0) {
		if (!(int)$custom_block_id) {
			return array();
		}
		$cache_name = 'layout.a.block.descriptions.' . $custom_block_id;
		$output = $this->cache->get($cache_name);
		if (!is_null($output)) {
			// return cached blocks
			return $output;
		}

		$output = array();
		$result = $this->db->query("SELECT bd.*, COALESCE(bl.status,0) as status
										FROM " . $this->db->table("block_descriptions") . " bd
										LEFT JOIN " . $this->db->table("block_layouts") . " bl ON bl.custom_block_id = bd.custom_block_id
										WHERE bd.custom_block_id = '" . ( int )$custom_block_id . "'");
		if ($result->num_rows) {
			foreach ($result->rows as $row) {
				$output[$row['language_id']] = $row;
			}
		}
		$this->cache->set($cache_name, $output);
		return $output;
	}

}