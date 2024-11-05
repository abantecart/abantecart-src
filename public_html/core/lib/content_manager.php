<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * Class AContentManager
 *
 * @property ADB $db
 * @property ALanguageManager $language
 * @property AConfig $config
 * @property ASession $session
 * @property ACache $cache
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
        $sql = "INSERT INTO " . $this->db->table("contents") . " 
               SET parent_content_id = '" . (int)$data['parent_content_id'] . "',
                    sort_order = '" . (int)$data ['sort_order'] . "',
                    status = '" . (int)$data['status'] . "',
                    icon_rl_id = '" . (int)$data['icon_rl_id'] . "',
                    author = '" . $this->db->escape($data['author']) . "',
                    publish_date = " . ($data['publish_date'] ? "'" . $this->db->escape($data['publish_date']) . "'" : "NULL") . ",
                    expire_date = " . ($data['expire_date'] ? "'" . $this->db->escape($data['expire_date']) . "'" : "NULL") . ",
                    date_modified = NOW(),
                    date_added = NOW()";

        $this->db->query($sql);
        $content_id = (int)$this->db->getLastId();

        //save multilingual language content
        $mlData = [];
        if ($data['languages']) {
            $mlData = $data['languages'];
        } else {
            $languages = $this->language->getAvailableLanguages();
            foreach ($languages as $language) {
                $mlData[$language['language_id']] = [
                    'title'            => $data['title'],
                    'description'      => $data['description'],
                    'meta_description' => $data['meta_description'],
                    'meta_keywords'    => $data['meta_keywords'],
                    'content'          => $data['content'],
                    'tags'             => $data['tags'],
                    'keyword'          => $data['keyword']
                ];
            }
        }
        $this->saveMLData($content_id, $mlData);
        $this->saveStores($content_id, $data['store_id']);
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
    public function editContent(int $content_id, array $data)
    {
        if (!$content_id) {
            return false;
        }
        $language_id = $this->language->getContentLanguageID();

        $update = [
            "parent_content_id = " . (int)$data['parent_content_id'],
            "status = " . (int)$data['status'],
            "sort_order = " . (int)$data['sort_order'],
            "icon_rl_id = " . (int)$data['icon_rl_id'],
            "author = '" . $this->db->escape($data['author']) . "'",
            "publish_date = " . ($data['publish_date'] ? "'" . $this->db->escape($data['publish_date']) . "'" : "NULL"),
            "expire_date = " . ($data['expire_date'] ? "'" . $this->db->escape($data['expire_date']) . "'" : "NULL"),
            "date_modified = NOW()"
        ];

        if (!empty($update)) {
            $this->db->query(
                "UPDATE `" . $this->db->table("contents`")
                . " SET " . implode(',', $update)
                . " WHERE content_id = '" . $content_id . "'"
            );
        }

        //save multilingual language content
        $mlData = [];
        if ($data['languages']) {
            $mlData = $data['languages'];
        } else {
            $mlData[$language_id] = [
                'title'            => $data['title'],
                'description'      => $data['description'],
                'meta_description' => $data['meta_description'],
                'meta_keywords'    => $data['meta_keywords'],
                'content'          => $data['content'],
                'tags'             => $data['tags'],
                'keyword'          => $data['keyword']
            ];
        }
        $this->saveMLData($content_id, $mlData);

        if ($data['store_id']) {
            $this->saveStores($content_id, $data['store_id']);
        }

        $this->cache->remove('content');
        $this->cache->remove('storefront_menu');
        return true;
    }

    /**
     * @param int $content_id
     * @param string $field
     * @param mixed $value
     *
     * @return bool
     * @throws AException
     */
    public function editContentField(int $content_id, $field, $value)
    {
        if (!$content_id) {
            return false;
        }
        $language_id = $this->language->getContentLanguageID();
        if (!$language_id) {
            return false;
        }
        switch ($field) {
            case 'status' :
            case 'sort_order' :
            case 'parent_content_id':
                $this->db->query(
                    "UPDATE " . $this->db->table("contents") . " 
                    SET `" . $field . "`= '" . (int)$value . "'
                    WHERE content_id = '" . $content_id . "'"
                );
                break;
            case 'author' :
            case 'publish_date' :
            case 'expire_date' :
                $this->db->query("UPDATE " . $this->db->table("contents") . " 
                                    SET `" . $field . "` = '" . $this->db->escape($value) . "'
                                    WHERE content_id = '" . $content_id . "'");
                break;
            case 'title' :
            case 'description' :
            case 'meta_description' :
            case 'meta_keywords' :
            case 'content' :
                $this->language->replaceDescriptions('content_descriptions',
                    ['content_id' => $content_id],
                    [$language_id => [$field => $value]]);

                break;
            case 'keyword' :
                $value = SEOEncode($value, 'content_id', $content_id);
                if ($value) {
                    $this->language->replaceDescriptions('url_aliases',
                        ['query' => "content_id=" . ( int )$content_id],
                        [(int)$this->language->getContentLanguageID() => ['keyword' => $value]]);
                } else {
                    $this->db->query("DELETE
                                    FROM " . $this->db->table("url_aliases") . " 
                                    WHERE query = 'content_id=" . ( int )$content_id . "'
                                        AND language_id = '" . (int)$this->language->getContentLanguageID() . "'");
                }

                break;
            case 'store_id':
                $this->saveStores($content_id, $value);
                break;
            case 'tags' :
                $value = (string)$value;
                $this->language->saveTags(
                    'content_tags',
                    ['content_id' => $content_id],
                    $language_id,
                    $value
                );
        }

        $this->cache->remove('content');
        $this->cache->remove('storefront_menu');
        return true;
    }

    /**
     * @param int $content_id
     * @throws AException
     */
    public function deleteContent(int $content_id)
    {
        $lm = new ALayoutManager();
        $lm->deletePageLayout('pages/content/content', 'content_id', $content_id);

        $this->db->query("DELETE FROM " . $this->db->table("content_descriptions") . " WHERE content_id = '" . $content_id . "'");
        $this->db->query("DELETE FROM " . $this->db->table("contents_to_stores") . " WHERE content_id = '" . $content_id . "'");
        $this->db->query("DELETE FROM " . $this->db->table("url_aliases") . " WHERE `query` = 'content_id=" . $content_id . "'");
        $this->db->query("DELETE FROM " . $this->db->table("content_tags") . " WHERE content_id = '" . $content_id . "'");
        $this->db->query("DELETE FROM " . $this->db->table("contents") . " WHERE content_id = '" . $content_id . "'");

        $this->cache->remove('content');
        $this->cache->remove('storefront_menu');
    }

    /**
     * @param int $content_id
     * @return array|false
     * @throws AException
     */
    public function cloneContent(int $content_id)
    {
        if (!$content_id) {
            return false;
        }
        $languages = $this->language->getAvailableLanguages();
        $mlData = [];
        foreach ($languages as $language) {
            $data = $this->getContent($content_id, (int)$language['language_id']);
            unset($data['content_id']);
            //set status to off for cloned product
            $data['status'] = 0;
            $mlData[$language['language_id']] = [
                'title'            => $data['title'] . ' ( Copy )',
                'description'      => $data['description'],
                'meta_description' => $data['meta_description'],
                'meta_keywords'    => $data['meta_keywords'],
                'content'          => $data['content'],
                'tags'             => $data['tags'],
                'keyword'          => ''
            ];

        }
        $data['languages'] = $mlData;

        $query = "SELECT store_id FROM " . $this->db->table("contents_to_stores") . " WHERE content_id = " . $content_id;
        $result = $this->db->query($query);
        if ($result->num_rows) {
            $data['store_id'] = array_column($result->rows, 'store_id');
        }

        $new_content_id = $this->addContent($data);
        $this->cache->remove('content');
        $layout_clone_result = $this->cloneProductLayout($content_id, $new_content_id);
        return [
            'name'         => $data['title'],
            'id'           => $new_content_id,
            'layout_clone' => $layout_clone_result,
        ];
    }

    /**
     * @param int $content_id
     * @param int|null $language_id
     *
     * @return mixed
     * @throws AException
     */
    public function getContent(int $content_id, ?int $language_id = null)
    {
        $output = [];
        if (!has_value($language_id)) {
            $language_id = $this->language->getContentLanguageID();
        }

        if (!$content_id) {
            return false;
        }
        $sql = "SELECT *
                FROM " . $this->db->table("contents") . " i
                LEFT JOIN " . $this->db->table("content_descriptions") . " id
                    ON (i.content_id = id.content_id AND id.language_id = '" . $language_id . "')
                WHERE i.content_id = '" . $content_id . "'
                ORDER BY i.content_id";
        $query = $this->db->query($sql);
        if ($query->num_rows) {
            foreach ($query->rows as $row) {
                $output = $row;
                break;
            }
            $sql = "SELECT *
                    FROM " . $this->db->table("url_aliases") . " 
                    WHERE `query` = 'content_id=" . $content_id . "'
                        AND language_id='" . $language_id . "'";
            $keyword = $this->db->query($sql);
            if ($keyword->num_rows) {
                $output['keyword'] = $keyword->row['keyword'];
            }
            $output['tags'] = $this->getContentTags($content_id, $language_id);
        }

        return $output;
    }

    /**
     * @param int $content_id
     * @param int $language_id
     *
     * @return array
     * @throws AException
     */
    public function getContentTags($content_id, $language_id = 0)
    {
        $language_id = (int)$language_id;
        $tag_data = [];
        $tagStr = [];

        $query = $this->db->query(
            "SELECT *
            FROM " . $this->db->table("content_tags") . " 
            WHERE content_id = '" . (int)$content_id . "'"
        );

        foreach ($query->rows as $result) {
            $tag_data[$result['language_id']][] = $result['tag'];
        }

        foreach ($tag_data as $language => $tags) {
            $tagStr[$language] = implode(',', $tags);
        }

        if ($language_id) {
            return $tagStr[$language_id];
        } else {
            return $tagStr;
        }
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
                                                        FROM " . $this->db->table("contents") . " 
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
                ( SELECT COUNT(*) FROM " . $this->db->table("contents") . " 
                WHERE parent_content_id = i.content_id ) as cnt,
                i.*	";
        }

        $sql = "SELECT " . $select_columns . "
                FROM " . $this->db->table("contents") . " i
                LEFT JOIN " . $this->db->table("content_descriptions") . " id
                    ON (i.content_id = id.content_id
                        AND id.language_id = '" . ( int )$this->language->getContentLanguageID() . "')
                LEFT JOIN " . $this->db->table("content_descriptions") . " cd
                    ON (cd.content_id = i.parent_content_id
                        AND cd.language_id = '" . ( int )$this->language->getContentLanguageID() . "')
                LEFT JOIN " . $this->db->table('contents_to_stores') . " cs
                    ON i.content_id = cs.content_id
                ";

        $sql .= "WHERE COALESCE(cs.store_id, 0) = '" . $store_id . "' ";

        if (!empty ($data ['subsql_filter'])) {
            $sql .= " AND " . str_replace('`name`', 'id.name', $data ['subsql_filter']);
        }

        if (isset($filter['id.title']) && !is_null($filter['id.title'])) {
            $sql .= " AND id.title LIKE '%" . (float)$filter['pfrom'] . "%' ";
        }
        if (isset($filter['status'])) {
            $sql .= " AND i.status = '" . (int)$filter['status'] . "'";
        }
        if (isset($filter['parent_id'])) {
            $sql .= " AND i.parent_content_id = '" . (int)$filter['parent_id'] . "'";
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
            $sql .= " ORDER BY " . $data ['sort'];
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

            $sql .= " LIMIT " . ( int )$data ['start'] . "," . ( int )$data ['limit'];
        }

        $query = $this->db->query($sql);

        $output = [];

        if (!$parent_only) {
            if ($query->num_rows) {
                foreach ($query->rows as $row) {
                    $output[(int)$row['content_id']] = $row;
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
                 FROM " . $this->db->table("contents") . " AS t1
                 LEFT JOIN " . $this->db->table("contents") . " as t2
                    ON t1.content_id = t2.parent_content_id
                 WHERE t2.content_id IS NULL");
        $result = [];
        foreach ($query->rows as $r) {
            $result[$r['content_id']] = $r['content_id'];
        }
        return $result;
    }

    /**
     * @param $contentID
     * @return bool
     * @throws AException
     */
    public function isParent($contentID)
    {
        $query = $this->db->query(
            "SELECT content_id as content_id
                 FROM " . $this->db->table("contents") . "
                 WHERE parent_content_id = '".(int)$contentID."'");
        if (sizeof($query->rows) > 0) {
            return true;
        } else {
            return  false;
        }
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
     * @param int $store_id
     * @param bool $only_enabled
     * @return array
     * @throws AException
     */
    public function getContentsForSelect($store_id = 0, $only_enabled = false)
    {
        $all = $this->getContents(['sort' => 'parent_content_id', 'order'=> 'ASC'], null, $store_id, false);
        return  [
            '0' => [
                'content_id' => 0,
                'title' => $this->language->get('text_top_level'),
                'children' => []
                ]
            ]
            + $this->buildContentTree($all, 0, 1, $only_enabled);
    }

    /**
     * Recursive function for building tree of content.
     * Note that same content can have two parents!
     *
     * @param  array $all
     * @param int|null $parent_id
     * @param int|null $level
     * @param bool|null $only_enabled
     * @return array
     */
    public function buildContentTree(array $all, ?int $parent_id = 0, ?int $level = 0, ?bool $only_enabled = false)
    {
        $output = [];
        foreach ($all as $content) {
            //look for leave content (leave cannot be of 0 ID)
            if ($only_enabled && !$content['status']) {
                continue;
            }

            if($content['parent_content_id'] == $parent_id) {
                $output[$content['content_id']] = [
                    'content_id' => $content['content_id'],
                    'title' => str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level) . $content['title'],
                    'children' => []
                ];
                $children = $this->buildContentTree($all, (int)$content['content_id'], $level + 1, $only_enabled);
                if($children){
                    $output[$content['content_id']]['children'] = array_column($children, 'content_id');
                }
                $output += $children;
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
                 FROM " . $this->db->table("contents_to_stores") . " cs
                 RIGHT JOIN " . $this->db->table("stores") . " s ON s.store_id = cs.store_id;";

        $result = $this->db->query($query);
        if ($result->num_rows) {
            foreach ($result->rows as $row) {
                $output[$row['store_id']][$row['content_id']] = $row['name'];
            }
        }
        return $output;
    }

    /**
     * method to update multilingual content data
     *
     * @param $content_id int
     * @param $mlData array [ 0 => [title => 'string']]
     *
     * @return bool
     * @throws AException
     */
    protected function saveMLData($content_id, $mlData)
    {
        foreach ($mlData as $language_id => $data) {
            $this->language->replaceDescriptions(
                'content_descriptions',
                ['content_id' => $content_id],
                [
                    $language_id => [
                        'title'            => $data['title'],
                        'description'      => $data['description'],
                        'meta_description' => $data['meta_description'],
                        'meta_keywords'    => $data['meta_keywords'],
                        'content'          => $data['content'],
                    ],
                ]
            );

            if (isset($data['tags'])) {
                $this->language->saveTags(
                    'content_tags',
                    ['content_id' => $content_id],
                    $language_id,
                    $data['tags']
                );
            }

            $data['keyword'] = SEOEncode($data['keyword'], 'content_id', $content_id);
            if ($data['keyword']) {
                $this->language->replaceDescriptions(
                    'url_aliases',
                    ['query' => "content_id=" . $content_id],
                    [$language_id => ['keyword' => $data['keyword']]]
                );
            } else {
                $this->db->query(
                    "DELETE
                    FROM " . $this->db->table("url_aliases") . " 
                    WHERE query = 'content_id=" . $content_id . "'
                        AND language_id = '" . $language_id . "'"
                );
            }
        }
        return true;
    }

    /**
     * method to save/update stores to content
     *
     * @param $content_id int
     * @param $storeIds array
     *
     * @return bool
     * @throws AException
     */
    protected function saveStores(int $content_id, array $storeIds)
    {
        if (!$storeIds) {
            return false;
        }

        $sql = "DELETE FROM " . $this->db->table("contents_to_stores") . " WHERE content_id='" . $content_id . "'";
        $this->db->query($sql);

        foreach ($storeIds as $store_id) {
            if (has_value($store_id)) {
                $sql = "INSERT INTO " . $this->db->table("contents_to_stores") . " (content_id, store_id)
                    VALUES ('" . $content_id . "','" . (int)$store_id . "')";
                $this->db->query($sql);
            }
        }
        return true;
    }

    /**
     * Method to clone layout for the content page
     *
     * @param int $content_id
     * @param int $new_content_id
     *
     * @return null
     * @throws AException
     */
    protected function cloneProductLayout($content_id, $new_content_id)
    {
        if (!has_value($content_id) && !has_value($new_content_id)) {
            return false;
        }

        //clone layout for content if present
        $lm = new ALayoutManager();
        $pages = $lm->getPages('pages/content/content', 'content_id', $content_id);
        if (count($pages) && has_value($pages[0]['page_id'])) {
            $tmpl_id = $this->config->get('config_storefront_template');
            $src_layout_id = $pages[0]['layout_id'];
            $src_page_id = $pages[0]['page_id'];
            //create instance for source layout
            $lm = new ALayoutManager($tmpl_id, $src_page_id, $src_layout_id);
            //create new page
            $page_info = [
                'controller' => 'pages/content/content',
                'key_param'  => 'content_id',
                'key_value'  => $new_content_id,
            ];
            //save new page
            $new_page_id = $lm->savePage($page_info);

            $layout_name = 'Content page ID: ' . $new_content_id;
            //create instance for new layout
            $lm = new ALayoutManager($tmpl_id, $new_page_id, '');
            return $lm->clonePageLayout($src_layout_id, '', $layout_name);
        }
        return false;
    }

}
