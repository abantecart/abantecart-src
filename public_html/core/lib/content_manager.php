<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

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

/**
 * Class AContentManager
 *
 * @property ADB              $db
 * @property ALanguageManager $language
 * @property AConfig          $config
 * @property ASession         $session
 * @property ACache           $cache
 *
 */
class AContentManager
{
    /**
     * @var Registry
     */
    protected $registry;
    public $errors = 0;

    public function __construct()
    {
        if (!IS_ADMIN) { // forbid for non admin calls
            throw new AException (AC_ERR_LOAD, 'Error: permission denied to change custom content');
        }
        $this->registry = Registry::getInstance();
    }

    public function __get($key)
    {
        return $this->registry->get($key);
    }

    public function __set($key, $value)
    {
        $this->registry->set($key, $value);
    }

    /**
     * @param array $data
     *
     * @return int
     * @throws AException
     */
    public function addContent($data)
    {
        if (!is_array($data) || !$data) {
            return false;
        }
        $sql = "INSERT INTO ".$this->db->table("contents")." 
							(parent_content_id, sort_order, status)
							 VALUES ('".(int)$data['parent_content_id'][0]."',  '".( int )$data ['sort_order'][0]."',
								  '".( int )$data ['status']."')";
        $this->db->query($sql);
        $content_id = $this->db->getLastId();
        //exclude first record from loop above
        unset($data['parent_content_id'][0], $data ['sort_order'][0]);

        if (empty ($data['keyword'])) {
            $seo_key = SEOEncode($data['title'], 'content_id', $content_id);
        } else {
            $seo_key = SEOEncode($data['keyword'], 'content_id', $content_id);
        }
        if ($seo_key) {
            $this->language->replaceDescriptions('url_aliases',
                ['query' => "content_id=".( int )$content_id],
                [(int)$this->language->getContentLanguageID() => ['keyword' => $seo_key]]);
        } else {
            $this->db->query("DELETE
							FROM ".$this->db->table("url_aliases")." 
							WHERE query = 'content_id=".( int )$content_id."'
								AND language_id = '".(int)$this->language->getContentLanguageID()."'");
        }

        if ($data['parent_content_id']) {
            foreach ($data['parent_content_id'] as $k => $parent_id) {
                $sql = "INSERT INTO ".$this->db->table("contents")." (content_id,parent_content_id, sort_order, status)
											VALUES ('".( int )$content_id."',
													'".(int)$parent_id."',
													'".( int )$data['sort_order'][$k]."',
													'".( int )$data ['status']."')";
                $this->db->query($sql);
            }
        }
        $languages = $this->language->getAvailableLanguages();

        foreach ($languages as $language) {
            $this->language->replaceDescriptions('content_descriptions',
                ['content_id' => (int)$content_id],
                [
                    ( int )$language['language_id'] => [
                        'title'            => $data ['title'],
                        'description'      => $data ['description'],
                        'meta_description' => $data ['meta_description'],
                        'meta_keywords'    => $data ['meta_keywords'],
                        'content'          => $data ['content'],
                    ],
                ]);
        }
        if ($data ['store_id']) {
            foreach ($data ['store_id'] as $store_id) {
                $sql = "INSERT INTO ".$this->db->table("contents_to_stores")." (content_id,store_id)
								VALUES ('".$content_id."','".(int)$store_id."')";
                $this->db->query($sql);
            }
        }

        $this->cache->remove('content');
        return $content_id;
    }

    /**
     * @param int $content_id
     * @param array $data
     *
     * @return bool
     * @throws AException
     */
    public function editContent($content_id, $data)
    {
        if (!$content_id) {
            return false;
        }
        $language_id = (int)$this->language->getContentLanguageID();

        //Delete store and insert back again with the same ID.
        //Area for improvement
        $sql = "DELETE FROM ".$this->db->table("contents")." 
					WHERE content_id='".$content_id."'; ";
        $this->db->query($sql);
        //insert back
        foreach ($data['parent_content_id'] as $parent_id) {
            $sql = "INSERT INTO ".$this->db->table("contents")." (content_id,parent_content_id, sort_order, status)
					VALUES ('".( int )$content_id."',
							'".(int)$parent_id."',
							'".( int )$data['sort_order'][$parent_id]."',
							'".( int )$data ['status']."'); ";
            $this->db->query($sql);
        }

        $update = [
            'title'            => $data ['title'],
            'description'      => $data ['description'],
            'meta_keywords'    => $data ['meta_keywords'],
            'meta_description' => $data ['meta_description'],
            'content'          => $data ['content'],
        ];

        $this->language->replaceDescriptions('content_descriptions',
            ['content_id' => (int)$content_id],
            [(int)$language_id => $update]);

        if (isset($data['keyword'])) {
            $data['keyword'] = SEOEncode($data['keyword'], 'content_id', $content_id);
            if ($data['keyword']) {
                $this->language->replaceDescriptions('url_aliases',
                    ['query' => "content_id=".( int )$content_id],
                    [(int)$this->language->getContentLanguageID() => ['keyword' => $data['keyword']]]);
            } else {
                $this->db->query("DELETE
								FROM ".$this->db->table("url_aliases")." 
								WHERE query = 'content_id=".( int )$content_id."'
									AND language_id = '".(int)$this->language->getContentLanguageID()."'");
            }
        }
        if ($data['store_id']) {
            $sql = "DELETE FROM ".$this->db->table("contents_to_stores")." WHERE content_id='".$content_id."'";
            $this->db->query($sql);

            foreach ($data['store_id'] as $store_id) {
                $sql = "INSERT INTO ".$this->db->table("contents_to_stores")." (content_id,store_id)
								VALUES ('".$content_id."','".(int)$store_id."')";
                $this->db->query($sql);
            }
        }
        $this->cache->remove('content');
        $this->cache->remove('storefront_menu');
        return true;
    }

    /**
     * @param int $content_id
     * @param string $field
     * @param mixed $value
     * @param null|int $parent_content_id
     *
     * @return bool
     * @throws AException
     */
    public function editContentField($content_id, $field, $value, $parent_content_id = null)
    {
        $content_id = (int)$content_id;
        $language_id = (int)$this->language->getContentLanguageID();
        if (!$language_id) {
            return false;
        }

        switch ($field) {
            case 'status' :
                $this->db->query("UPDATE ".$this->db->table("contents")." 
									SET `status` = '".(int)$value."'
									WHERE content_id = '".(int)$content_id."'");
                break;
            case 'sort_order' :
                $this->db->query("UPDATE ".$this->db->table("contents")." 
								SET `sort_order` = '".(int)$value."'
								WHERE content_id = '".(int)$content_id."'
									AND parent_content_id='".(int)$parent_content_id."'");

                break;
            case 'title' :
            case 'description' :
            case 'meta_description' :
            case 'meta_keywords' :
            case 'content' :
                $this->language->replaceDescriptions('content_descriptions',
                    ['content_id' => (int)$content_id],
                    [(int)$language_id => [$field => $value]]);

                break;
            case 'keyword' :
                $value = SEOEncode($value, 'content_id', $content_id);
                if ($value) {
                    $this->language->replaceDescriptions('url_aliases',
                        ['query' => "content_id=".( int )$content_id],
                        [(int)$this->language->getContentLanguageID() => ['keyword' => $value]]);
                } else {
                    $this->db->query("DELETE
									FROM ".$this->db->table("url_aliases")." 
									WHERE query = 'content_id=".( int )$content_id."'
										AND language_id = '".(int)$this->language->getContentLanguageID()."'");
                }

                break;
            case 'parent_content_id':
                // prevent deleting while updating with parent_id==content_id
                $value = (array)$value;
                $tmp = [];
                foreach ($value as $v) {
                    list(, $parent_id) = explode('_', $v);
                    if ($parent_id == $content_id) {
                        continue;
                    }
                    $tmp[$parent_id] = $parent_id;
                }
                $value = $tmp;
                if (sizeof($value) == 1 && current($value) == $content_id) {
                    break;
                }

                $query = "SELECT parent_content_id, sort_order, status
							FROM ".$this->db->table("contents")." 
							WHERE content_id='".$content_id."'";
                $result = $this->db->query($query);
                $sort_orders = [];
                $status = 0;
                if ($result->num_rows) {
                    $status = $result->row['status'];
                    foreach ($result->rows as $row) {
                        $sort_orders[$row['parent_content_id']] = $row['sort_order'];
                    }
                }

                $query = "DELETE FROM ".$this->db->table("contents")." WHERE content_id='".$content_id."'";
                $this->db->query($query);

                $value = !$value ? [0] : $value;
                foreach ($value as $parent_content_id) {
                    $parent_content_id = (int)$parent_content_id;
                    if ($parent_content_id == $content_id) {
                        continue;
                    }
                    $query = "INSERT INTO ".$this->db->table("contents")." (content_id,parent_content_id, sort_order, status)
									VALUES ('".$content_id."',
											'".$parent_content_id."',
											'".(int)$sort_orders[$parent_content_id]."',
											'".$status."');";
                    $this->db->query($query);
                }
                break;
            case 'store_id':
                $query = "DELETE FROM ".$this->db->table("contents_to_stores")." WHERE content_id='".$content_id."'";
                $this->db->query($query);
                foreach ($value as $store_id) {
                    if (has_value($store_id)) {
                        $query = "INSERT INTO ".$this->db->table("contents_to_stores")." (content_id,store_id)
										VALUES ('".$content_id."','".(int)$store_id."')";
                        $this->db->query($query);
                    }
                }
                break;
        }

        $this->cache->remove('content');
        $this->cache->remove('storefront_menu');
        return true;
    }

    /**
     * @param int $content_id
     * @throws AException
     */
    public function deleteContent($content_id)
    {
        $lm = new ALayoutManager();
        $lm->deletePageLayout('pages/content/content', 'content_id', ( int )$content_id);

        $this->db->query("DELETE FROM ".$this->db->table("contents")." WHERE content_id = '".( int )$content_id."'");
        $this->db->query("DELETE FROM ".$this->db->table("content_descriptions")." WHERE content_id = '".( int )$content_id."'");
        $this->db->query("DELETE FROM ".$this->db->table("contents_to_stores")." WHERE content_id = '".( int )$content_id."'");
        $this->db->query("DELETE FROM ".$this->db->table("url_aliases")." WHERE `query` = 'content_id=".( int )$content_id."'");

        $this->cache->remove('content');
        $this->cache->remove('storefront_menu');
    }

    /**
     * @param int $content_id
     * @param int $language_id
     *
     * @return mixed
     * @throws AException
     */
    public function getContent($content_id, $language_id = null)
    {
        $output = [];
        $content_id = (int)$content_id;
        if (!has_value($language_id)) {
            $language_id = ( int )$this->language->getContentLanguageID();
        }

        if (!$content_id) {
            return false;
        }
        $sql = "SELECT *
				FROM ".$this->db->table("contents")." i
				LEFT JOIN ".$this->db->table("content_descriptions")." id
					ON (i.content_id = id.content_id AND id.language_id = '".$language_id."')
				WHERE i.content_id = '".( int )$content_id."'
				ORDER BY i.content_id";
        $query = $this->db->query($sql);
        if ($query->num_rows) {
            $i = 0;
            foreach ($query->rows as $row) {
                $idx = $row['parent_content_id'];
                if ($i > 0) {
                    $output[0]['parent_content_id'][] = $row['parent_content_id'];
                    $output[0]['sort_order'][$idx] = $row['sort_order'];
                    continue;
                }
                $row['parent_content_id'] = [$row['parent_content_id']];
                $row['sort_order'] = [$idx => $row['sort_order']];
                $output[$i] = $row;
                $i++;
            }
            $sql = "SELECT *
					FROM ".$this->db->table("url_aliases")." 
					WHERE `query` = 'content_id=".( int )$content_id."'
						AND language_id='".$language_id."'";
            $keyword = $this->db->query($sql);
            if ($keyword->num_rows) {
                $output[0]['keyword'] = $keyword->row['keyword'];
            }
        }

        return $output[0];
    }

    /**
     * @param array $data
     * @param string $mode
     * @param int $store_id
     * @param bool $parent_only
     *
     * @return array|int
     * @throws AException
     */
    public function getContents($data = [], $mode = 'default', $store_id = 0, $parent_only = false)
    {
        if ($parent_only) {
            if ($data["subsql_filter"]) {
                $data["subsql_filter"] .= ' AND ';
            }
            $data["subsql_filter"] .= "i.content_id IN (SELECT parent_content_id
														FROM ".$this->db->table("contents")." 
														WHERE parent_content_id> 0)";
            $data['sort'] = 'i.parent_content_id, i.sort_order';
        }

        $filter = $data['filter'] ?? [];

        if ($data['store_id']) {
            $store_id = (int)$data['store_id'];
        } else {
            $store_id = $store_id !== null ? $store_id : (int)$this->config->get('current_store_id');
        }

        if ($mode == 'total_only') {
            $select_columns = 'count(*) as total';
        } else {
            $select_columns = "id.*,
						cd.title as parent_name,
						( SELECT COUNT(*) FROM ".$this->db->table("contents")." 
						WHERE parent_content_id=i.content_id ) as cnt,
						i.*	";
        }

        $sql = "SELECT ".$select_columns."
				FROM ".$this->db->table("contents")." i
				LEFT JOIN ".$this->db->table("content_descriptions")." id
					ON (i.content_id = id.content_id
						AND id.language_id = '".( int )$this->language->getContentLanguageID()."')
				LEFT JOIN ".$this->db->table("content_descriptions")." cd
					ON (cd.content_id = i.parent_content_id
						AND cd.language_id = '".( int )$this->language->getContentLanguageID()."')
				LEFT JOIN ".$this->db->table('contents_to_stores')." cs
					ON i.content_id = cs.content_id
				";

        $sql .= "WHERE COALESCE(cs.store_id, 0) = '".$store_id."' ";

        if (!empty ($data ['subsql_filter'])) {
            $sql .= " AND ".str_replace('`name`', 'id.name', $data ['subsql_filter']);
        }

        if (isset($filter['id.title']) && !is_null($filter['id.title'])) {
            $sql .= " AND id.title LIKE '%".(float)$filter['pfrom']."%' ";
        }
        if (isset($filter['status'])) {
            $sql .= " AND i.status = '".(int)$filter['status']."'";
        }
        if (isset($filter['parent_id'])) {
            $sql .= " AND i.parent_content_id = '".(int)$filter['parent_id']."'";
        }

        //If for total, we're done building the query
        if ($mode == 'total_only') {
            $query = $this->db->query($sql);
            return $query->row['total'];
        }

        $sort_data = [
            'parent_content_id ' => 'i.parent_content_id',
            'title'              => 'id.title',
            'sort_order'         => 'i.sort_order',
            'status'             => 'i.status',
        ];

        if (isset($data['sort']) && in_array($data['sort'], array_keys($sort_data))) {
            $sql .= " ORDER BY ".$data ['sort'];
        } else {
            $sql .= " ORDER BY i.parent_content_id, i.sort_order";
        }

        if (isset($data['order']) && (strtoupper($data['order']) == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset ($data ['start']) || isset ($data ['limit'])) {
            if ($data ['start'] < 0) {
                $data ['start'] = 0;
            }

            if ($data ['limit'] < 1) {
                $data ['limit'] = 20;
            }

            $sql .= " LIMIT ".( int )$data ['start'].",".( int )$data ['limit'];
        }

        $query = $this->db->query($sql);

        $output = [];

        if (!$parent_only) {
            if ($query->num_rows) {
                foreach ($query->rows as $row) {
                    $parent = (int)$row['parent_content_id'];
                    if (is_array($output[(int)$row['content_id']]['parent_content_id'])) {
                        $output[(int)$row['content_id']]['parent_content_id'][$parent] = $parent;
                        $output[(int)$row['content_id']]['sort_order'][$parent] = (int)$row['sort_order'];
                    } else {
                        $output[(int)$row['content_id']] = $row;
                        $output[(int)$row['content_id']]['parent_content_id'] = [$parent => $parent];
                        $output[(int)$row['content_id']]['sort_order'] = [$parent => (int)$row['sort_order']];
                    }
                }
            }
        } else {
            $output = $query->rows;
        }
        return $output;
    }

    /**
     * @return array
     * @throws AException
     */
    public function getLeafContents()
    {
        $query = $this->db->query(
            "SELECT t1.content_id as content_id
				 FROM ".$this->db->table("contents")." AS t1
				 LEFT JOIN ".$this->db->table("contents")." as t2	ON t1.content_id = t2.parent_content_id
				 WHERE t2.content_id IS NULL");
        $result = [];
        foreach ($query->rows as $r) {
            $result[$r['content_id']] = $r['content_id'];
        }
        return $result;
    }

    /**
     * @param array $data
     *
     * @return int
     * @throws AException
     */
    public function getTotalContents($data = [])
    {
        return $this->getContents($data, 'total_only');
    }

    /**
     * @param array $data
     * @param int $store_id
     *
     * @return array
     * @throws AException
     */
    public function getParentContents($data = [], $store_id = 0)
    {
        return $this->getContents($data, '', $store_id, true);
    }

    /**
     * @param bool $parent_only
     * @param bool $without_top
     * @param int $store_id
     *
     * @return array
     * @throws AException
     */
    public function getContentsForSelect($parent_only = false, $without_top = false, $store_id = 0, $only_enabled = false)
    {
        $all = $parent_only
            ? $this->getParentContents([], $store_id)
            : $this->getContents([], '', $store_id, false);
        if (!$without_top) {
            return array_merge(['0_0' => $this->language->get('text_top_level')], $this->buildContentTree($all, 0, 1,$only_enabled));
        } else {
            return $this->buildContentTree($all,0,0,$only_enabled);
        }
    }

    /**
     * Recursive function for building tree of content.
     * Note that same content can have two parents!
     *
     * @param     $all_contents array with all contents. it contains element with key
     *                          parent_content_id that is array  - all parent ids
     * @param int $parent_id
     * @param int $level
     *
     * @return array
     */
    public function buildContentTree($all_contents, $parent_id = 0, $level = 0, $only_enabled = false)
    {
        $output = [];
        foreach ($all_contents as $content) {
            foreach ($content['parent_content_id'] as $par_id) {
                //look for leave content (leave cannot be of 0 ID)
                if($only_enabled && !$content['status']){
                    continue;
                }

                if ($par_id == $parent_id && $content['content_id']) {
                    $output[$parent_id.'_'.$content['content_id']] = str_repeat('&nbsp;&nbsp;', $level).$content['title'];
                    $output = array_merge($output, $this->buildContentTree($all_contents, $content['content_id'], $level + 1, $only_enabled));
                }
            }
        }
        return $output;
    }

    /**
     * method returns store list for selectbox for edit form of Content page
     *
     * @return array
     * @throws AException
     */
    public function getContentStores()
    {
        $output = [];
        $query = "SELECT s.store_id, COALESCE(cs.content_id,0) as content_id, s.name
				 FROM ".$this->db->table("contents_to_stores")." cs
				 RIGHT JOIN ".$this->db->table("stores")." s ON s.store_id = cs.store_id;";

        $result = $this->db->query($query);
        if ($result->num_rows) {
            foreach ($result->rows as $row) {
                $output[$row['store_id']][$row['content_id']] = $row['name'];
            }
        }
        return $output;
    }
}
