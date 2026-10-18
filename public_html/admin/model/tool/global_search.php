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

/** @noinspection PhpMultipleClassDeclarationsInspection */

if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ModelToolGlobalSearch extends Model
{
    /**
     * commands available in the system
     *
     * @var array
     */
    public $commands;

    /**
     * array with descriptions of controller for search
     *
     * @var array
     */
    public $results_controllers = [
        "commands"           => [],
        "orders"             => [
            'alias'    => 'order',
            'id'       => 'order_id',
            'page'     => 'sale/order/details',
            'response' => '',
        ],
        "customers"          => [
            'alias'    => 'customer',
            'id'       => 'customer_id',
            'page'     => 'sale/customer/update',
            'response' => '',
        ],
        "product_categories" => [
            'alias'    => 'category',
            'id'       => 'category_id',
            'page'     => 'catalog/category/update',
            'response' => '',
        ],
        "products"           => [
            'alias'    => 'product',
            'id'       => 'product_id',
            'page'     => 'catalog/product/update',
            'response' => '',
        ],
        "reviews"            => [
            'alias'    => 'review',
            'id'       => 'review_id',
            'page'     => 'catalog/review/update',
            'response' => '',
        ],
        "manufacturers"      => [
            'alias'    => 'brand',
            'id'       => 'manufacturer_id',
            'page'     => 'catalog/manufacturer/update',
            'response' => '',
        ],
        "languages"          => [
            'alias'        => 'language',
            'id'           => 'language_definition_id',
            'extra_fields' => ['language_id'],
            'page'         => 'localisation/language_definition_form/update',
            'response'     => 'localisation/language_definition_form/update',
        ],
        "pages"              => [
            'alias'    => 'information',
            'id'       => ['page_id', 'layout_id', 'tmpl_id'],
            'page'     => 'design/layout',
            'response' => '',
        ],
        "settings"           => [
            'alias'    => 'setting',
            'id'       => ['setting_id', 'active', 'store_id'],
            'page'     => 'setting/setting',
            'response' => 'setting/setting_quick_form',
        ],
        "messages"           => [
            'alias'    => 'information',
            'id'       => 'msg_id',
            'page'     => 'tool/message_manager',
            'response' => '',
        ],
        "extensions"         => [
            'alias'    => 'extension',
            'id'       => 'extension',
            'page'     => 'extension/extensions/edit',
            'page2'    => 'total/%s',
            'response' => '',
        ],
        "downloads"          => [
            'alias'    => 'download',
            'id'       => 'download_id',
            'page'     => '',
            'response' => 'product/product/buildDownloadForm',
        ],
        "contents"           => [
            'alias'    => 'content',
            'id'       => 'content_id',
            'page'     => 'design/content/update',
            'response' => '',
        ],
        "seo_urls"           => [
            'alias'    => 'seo_url',
            'id'       => 'url_alias_id',
            'page'     => 'tool/seo_url/update',
            'response' => '',
        ],
    ];

    public function __construct($registry)
    {
        parent::__construct($registry);

        $text_data = $this->language->getASet('common/action_commands');
        $keys = preg_grep("/^command.*/", array_keys($text_data));
        foreach ($keys as $key) {
            $this->commands[$key] = $text_data[$key];
        }
    }

    /**
     * function returns a list of accessible search categories
     *
     * @param string $keyword
     *
     * @return array
     */
    public function getSearchSources($keyword = '')
    {
        $search_categories = [];
        // limit of keyword length
        if (mb_strlen($keyword) >= 1) {
            foreach ($this->results_controllers as $k => $item) {
                $search_categories[$k] = $item['alias'];
            }
        }
        return $search_categories;
    }

    /**
     * function returns search results in JSON format
     *
     * @param string $search_category
     * @param string $keyword
     * @param string $mode
     *
     * @return array
     * @throws AException
     */
    public function getResult($search_category, $keyword, $mode = 'listing')
    {
        $language_id = (int) $this->config->get('storefront_language_id');
        // two variants of needles for search: with and without html-entities
        $needles = $this->getNeedles($keyword);
        $needle = $needles[0];
        // keys of settings, extensions, e.t.c. have underscores instead of spaces
        $keyNeedles = array_values(
            array_unique(
                array_merge($needles, array_map(fn($n) => str_replace(' ', '_', $n), $needles))
            )
        );

        $page = (int) $this->request->get_or_post('page');
        $rows = (int) $this->request->get_or_post('rows');

        if ($page) {
            $offset = ($page - 1) * $rows;
            $rows_count = $rows;
        } else {
            $offset = 0;
            $rows_count = $mode == 'listing' ? 10 : 3;
        }
        $limit = " LIMIT " . $offset . "," . $rows_count;
        $calc = $this->db->getSqlCalcTotalRows();

        $current_store_id = (int) $this->session->data['current_store_id'];
        $search_languages = array_map('intval', array_column($this->language->getActiveLanguages(), 'language_id'));
        $langs = implode(',', $search_languages) ? : '0';

        switch ($search_category) {
            case 'commands' :
                $possibleCommands = $this->_possibleCommands($needle);
                $result = array_slice($possibleCommands, $offset, $rows_count);
                if ($result) {
                    $result[0]['total_num_rows'] = count($possibleCommands);
                }
                break;
            case 'product_categories' :
                $sql = "SELECT DISTINCT " . $calc . " c.category_id, c.name as title, c.name as text
                        FROM " . $this->db->table("category_descriptions") . " c 
                        WHERE " . $this->like(['c.name'], $needles) . "
                            AND c.language_id IN (" . $langs . ")" . $limit;
                $result = $this->db->query($sql)->rows;
                if ($result) {
                    /** @var ModelCatalogCategory $catModel */
                    $catModel = $this->load->model('catalog/category', 'storefront');
                    foreach ($result as &$row) {
                        $longName = $catModel->getPath($row['category_id'], $language_id);
                        $row['title'] = $row['name'] = $longName;
                    }
                    unset($row);
                }
                break;
            case 'languages' :
                $sql = "SELECT " . $calc . " l.language_definition_id, 
                                l.language_key as title, 
                                CONCAT_WS('  ',l.language_key,l.language_value) as text, 
                                language_id
                        FROM " . $this->db->table("language_definitions") . " l
                        WHERE (" . $this->like(['l.language_value'], $needles)
                    . " OR " . $this->like(['l.language_key'], $keyNeedles) . ")
                            AND l.language_id IN (" . $langs . ")" . $limit;
                $result = $this->db->query($sql)->rows;
                break;
            case 'products' :
                $descJoin = "LEFT JOIN " . $this->db->table("product_descriptions") . " b 
                                ON (b.product_id = a.product_id AND b.language_id IN (" . $langs . "))";
                $sql = "SELECT DISTINCT " . $calc . " a.product_id, b.name as title, b.name as text
                        FROM " . $this->db->table("products") . " a
                        " . $descJoin . "
                        WHERE a.product_id = " . (int) $needle . "
                                OR " . $this->like(['a.supplier_id', 'a.model', 'a.sku'], $needles) . "
                        UNION
                        SELECT DISTINCT pd1.product_id, pd1.name as title, pd1.name as text
                        FROM " . $this->db->table("product_descriptions") . " pd1
                        WHERE " . $this->like(['pd1.name'], $needles) . "
                            AND pd1.language_id IN (" . $langs . ")
                        UNION
                        SELECT DISTINCT a.product_id, CONCAT(pd1.name,' -> ',b.name) as title, pd1.name as text
                        FROM " . $this->db->table("product_option_values") . " a
                        LEFT JOIN " . $this->db->table("product_descriptions") . " pd1
                            ON (pd1.product_id = a.product_id AND pd1.language_id IN (" . $langs . "))
                        LEFT JOIN " . $this->db->table("product_option_value_descriptions") . " b 
                            ON (b.product_option_value_id = a.product_option_value_id 
                                AND b.language_id IN (" . $langs . "))
                        WHERE " . $this->like(['a.sku'], $needles) . "
                        UNION
                        SELECT DISTINCT a.product_id, b.name as title, a.name as text
                        FROM " . $this->db->table("product_option_descriptions") . " a
                        " . $descJoin . "
                        WHERE " . $this->like(['a.name'], $needles) . "
                            AND a.language_id IN (" . $langs . ")
                        UNION
                        SELECT DISTINCT a.product_id, b.name as title, a.name as text
                        FROM " . $this->db->table("product_option_value_descriptions") . " a
                        " . $descJoin . "
                        WHERE " . $this->like(['a.name'], $needles) . "
                            AND a.language_id IN (" . $langs . ")
                        UNION
                        SELECT a.product_id, b.name as title, b.name as text
                        FROM " . $this->db->table("product_tags") . " a
                        " . $descJoin . "
                        WHERE " . $this->like(['a.tag'], $needles, true) . "
                            AND a.language_id IN (" . $langs . ")" . $limit;
                $result = $this->db->query($sql)->rows;
                break;
            case "reviews" :
                $sql = "SELECT DISTINCT " . $calc . " review_id, r.`text`, pd.`name` as title
                        FROM " . $this->db->table("reviews") . " r
                        LEFT JOIN " . $this->db->table("product_descriptions") . " pd
                            ON (pd.product_id = r.product_id AND pd.language_id IN (" . $langs . "))
                        WHERE " . $this->like(['r.`text`', 'r.`author`'], $needles) . $limit;
                $result = $this->db->query($sql)->rows;
                break;
            case "manufacturers" :
                $sql = "SELECT " . $calc . " manufacturer_id, `name` as text, `name` as title
                        FROM " . $this->db->table("manufacturers") . " 
                        WHERE " . $this->like(['name'], $needles) . $limit;
                $result = $this->db->query($sql)->rows;
                break;
            case "orders" :
                $sql = "SELECT " . $calc . " order_id, 
                                CONCAT('order #', order_id) as title,
                                CONCAT(invoice_prefix,' ',firstname,' ',lastname,' ',email)  as text
                        FROM " . $this->db->table("orders") . " 
                        WHERE (" . $this->like(
                        [
                            'invoice_prefix',
                            'firstname',
                            'lastname',
                            'email',
                            'shipping_address_1',
                            'shipping_address_2',
                            'payment_address_1',
                            'payment_address_2',
                        ],
                        $needles
                    ) . "
                                    OR order_id = " . (int) $needle . ")
                                AND language_id = " . $language_id . $limit;
                $result = $this->db->query($sql)->rows;
                break;
            case "customers" :
                $conditions = [];
                foreach (array_filter(explode(' ', $keyword), 'strlen') as $word) {
                    $conditions[] = $this->like(['firstname', 'lastname'], $this->getNeedles($word));
                }
                $conditions[] = $this->like(['email'], $needles);
                $sql = "SELECT " . $calc . " customer_id, 
                                CONCAT('" . ($mode == 'listing' ? "customer: " : "") . "', firstname,' ',lastname) as title,
                                CONCAT(firstname,' ',lastname,' ',email)  as text
                        FROM " . $this->db->table("customers") . " 
                        WHERE (" . implode(' OR ', $conditions) . ")" . $limit;
                $result = $this->db->query($sql)->rows;
                break;
            case "pages" :
                $sql = "SELECT DISTINCT " . $calc . " p.page_id,
                                b.name as title,
                                CONCAT(b.name, ' ',b.title, ' ',b.keywords) as text,
                                pl.layout_id, l.template_id as tmpl_id
                        FROM " . $this->db->table("pages") . " p 
                        LEFT JOIN " . $this->db->table("page_descriptions") . " b 
                            ON (p.page_id = b.page_id AND b.language_id IN (" . $langs . "))
                        LEFT JOIN " . $this->db->table("pages_layouts") . " pl
                            ON (pl.page_id = p.page_id
                                AND pl.layout_id IN 
                                    (SELECT layout_id
                                     FROM " . $this->db->table("layouts") . " 
                                     WHERE template_id = '" . $this->db->escape(
                        $this->config->get('config_storefront_template')
                    ) . "'
                                            AND layout_type='1'))
                        LEFT JOIN " . $this->db->table("layouts") . " l 
                            ON  l.layout_id = pl.layout_id
                        WHERE " . $this->like(['b.name', 'b.title', 'b.keywords'], $needles) . $limit;
                $result = $this->db->query($sql)->rows;
                break;
            case "settings" :
                $sql = "SELECT DISTINCT " . $calc . " setting_id,
                                CONCAT(`group`,'-',s.`key`,'-',s.store_id) as active,
                                s.store_id,
                                COALESCE(l.language_value,s.`key`) as title,
                                COALESCE(l.language_value,s.`key`) as text,
                                e.`key` as extension, e.`type` as type
                        FROM " . $this->db->table("settings") . " s
                        LEFT JOIN " . $this->db->table("extensions") . " e 
                            ON s.`group` = e.`key`
                        LEFT JOIN " . $this->db->table("language_definitions") . " l
                            ON l.language_key LIKE CONCAT(s.`key`,'%')
                        WHERE (" . $this->like(['s.`value`'], $needles) . "
                                OR " . $this->like(['s.`key`'], $keyNeedles) . ")
                            AND s.`key` NOT IN ('encryption_key', 'config_ssl')
                            AND s.`store_id` ='" . $current_store_id . "'
                        UNION
                        SELECT s.setting_id,
                                CONCAT(s.`group`,'-',s.`key`,'-',s.store_id) as active,
                                s.store_id,
                                CONCAT(`group`,' -> ',COALESCE( l.language_value,s.`key` )) as title,
                                CONCAT_WS(' >> ',l.language_value) as text, '', 'core'
                        FROM " . $this->db->table("language_definitions") . " l
                        LEFT JOIN " . $this->db->table("settings") . " s
                            ON l.language_key = CONCAT('entry_',REPLACE(s.`key`,'config_',''))
                        WHERE (" . $this->like(['l.language_value'], $needles) . "
                                OR " . $this->like(['l.language_key'], $keyNeedles) . ")
                            AND block='setting_setting' AND l.language_id ='" . $language_id . "'
                            AND s.`store_id` ='" . $current_store_id . "'
                            AND setting_id > 0" . $limit;
                $result = [];
                //remove all text between span tags
                $regex = '/<span(.*)span>/';
                $clean = fn($text) => str_replace(["\t", "  ", "\n"], "", strip_tags(preg_replace($regex, '', $text)));
                foreach ($this->db->query($sql)->rows as $row) {
                    if (!isset($result[$row['setting_id']])) {
                        $row['title'] = $clean($row['title']);
                        $row['text'] = $clean($row['text'] ? : $row['title']);
                        $result[$row['setting_id']] = $row;
                    }
                }
                $result = array_values($result);
                break;
            case "messages" :
                $sql = "SELECT DISTINCT " . $calc . " msg_id, title as title, `message` as text
                        FROM " . $this->db->table("messages") . " 
                        WHERE " . $this->like(['`title`', '`message`'], $needles) . $limit;
                $result = $this->db->query($sql)->rows;
                break;
            case "extensions" :
                $sql = "SELECT DISTINCT " . $calc . " `key` as extension, `key` as title, `key` as text
                        FROM " . $this->db->table("extensions") . " e
                        WHERE " . $this->like(['`key`'], $keyNeedles) . "
                                    AND `type` <> 'total'" . $limit;
                $result = $this->db->query($sql)->rows;
                break;
            case "downloads" :
                $sql = "SELECT DISTINCT " . $calc . " d.download_id, name as title, name  as text
                        FROM " . $this->db->table("downloads") . " d
                        LEFT JOIN " . $this->db->table("download_descriptions") . " dd
                            ON (d.download_id = dd.download_id AND dd.language_id IN (" . $langs . "))
                        WHERE " . $this->like(['dd.name'], $needles) . $limit;
                $result = $this->db->query($sql)->rows;
                break;
            case "contents" :
                $sql = "SELECT DISTINCT " . $calc . " c.content_id, cd.title, cd.description  as text
                        FROM " . $this->db->table("contents") . " c
                        RIGHT JOIN " . $this->db->table("content_descriptions") . " cd
                            ON (c.content_id = cd.content_id AND cd.language_id IN (" . $langs . "))
                        WHERE " . $this->like(['`title`', '`description`', '`content`'], $needles) . $limit;
                $result = $this->db->query($sql)->rows;
                break;
            case "seo_urls" :
                $sql = "SELECT DISTINCT " . $calc . " url_alias_id, keyword as title, keyword as text
                        FROM " . $this->db->table("url_aliases") . "
                        WHERE " . $this->like(['keyword'], $needles) . "
                            AND language_id IN (" . $langs . ")" . $limit;
                $result = $this->db->query($sql)->rows;
                break;
            default :
                $this->data['result'] = [];
                $this->extensions->hk_ProcessData(
                    $this,
                    'getResult',
                    [
                        'search_category' => $search_category,
                        'term'            => $needle,
                        'limit'           => 3,
                    ]
                );
                $result = $this->data['result'] ? : [];
                break;
        }

        if ($result) {
            $result[0]['total_num_rows'] = $result[0]['total_num_rows'] ?? $this->db->getTotalNumRows();
        }

        if ($mode == 'listing') {
            if ($search_category == 'commands') {
                $result = $this->_prepareCommandsResponse($result);
            } else {
                $result = $this->_prepareResponse(
                    $keyword,
                    $this->results_controllers[$search_category]['page'],
                    $this->results_controllers[$search_category]['id'],
                    $result
                );
            }
        }
        foreach ($result as &$row) {
            $row['controller'] = $this->results_controllers[$search_category]['page'];

            //shorten text for suggestion
            if ($mode != 'listing') {
                $dec_text = htmlentities($row['text'], ENT_QUOTES);
                if (mb_strlen($dec_text) > 100) {
                    $row['text'] = mb_substr($dec_text, 0, 100) . '...';
                }
            }
        }
        $output ["result"] = $result;
        $output ['search_category'] = $search_category;

        return $output;
    }

    /**
     * Needle variants for the search: with html-entities (values are stored encoded) and without them.
     * Lower case and escaped for sql.
     *
     * @param string $text
     *
     * @return array
     * @throws AException
     */
    protected function getNeedles(string $text): array
    {
        return array_values(
            array_unique(
                [
                    $this->db->escape(mb_strtolower(htmlentities($text, ENT_QUOTES))),
                    $this->db->escape(mb_strtolower($text)),
                ]
            )
        );
    }

    /**
     * Builds "(col1 LIKE '%n1%' OR col1 LIKE '%n2%' OR col2 ...)" for every column and needle
     *
     * @param array $columns
     * @param array $needles - already escaped
     * @param bool $startsWith - match only the beginning of the value
     *
     * @return string
     */
    protected function like(array $columns, array $needles, bool $startsWith = false): string
    {
        $conditions = [];
        foreach ($columns as $column) {
            foreach ($needles as $needle) {
                $conditions[] = $column . " LIKE '" . ($startsWith ? '' : '%') . $needle . "%'";
            }
        }
        return '(' . implode(' OR ', $conditions) . ')';
    }

    /**
     * function prepares an array with search results for JSON encoding
     *
     * @param string $keyword
     * @param string $rt
     * @param string|array $key_field (s)
     * @param array $table
     *
     * @return array
     * @throws AException
     */
    protected function _prepareResponse($keyword = '', $rt = '', $key_field = '', $table = [])
    {
        $output = [];
        if (!$rt || !$key_field || !$keyword) {
            return [];
        }

        if ($table && is_array($table)) {
            foreach ($table as $row) {
                $tmp = [];
                $text = '';
                //let's extract and colorize keyword in row
                foreach ($row as $key => $field) {
                    $field_decoded = htmlentities((string) $field, ENT_QUOTES);

                    // if keyword found
                    $pos = mb_stripos($field_decoded, $keyword);
                    if (is_int($pos) && $key != 'title') {
                        $row ['title'] = strip_tags(html_entity_decode($row ['title']));
                        $start = $pos < 50 ? 0 : ($pos - 50);
                        $keyword_len = mb_strlen($keyword);
                        $field_len = mb_strlen($field_decoded);
                        $ellipsis = ($field_len - $keyword_len > 10) ? '...' : '';
                        // before founded word
                        $text .= $ellipsis . mb_substr($field_decoded, $start, $pos - $start);
                        // founded word
                        $len = ($field_len - ($pos + $keyword_len)) > 50 ? 50 : $field_len;
                        // after founded word
                        $text .= mb_substr($field_decoded, ($pos + $keyword_len), $len) . $ellipsis;
                        $row ['text'] = $text;
                        break;
                    }
                }

                // exception for extension settings
                $temp_key_field = $key_field;
                $url = $rt;

                if ($rt == 'setting/setting' && !empty($row['extension'])) {
                    $temp_key_field = $this->results_controllers['extensions']['id'];
                    if ($row['type'] == 'total') { //for order total extensions
                        $url = sprintf($this->results_controllers['extensions']['page2'], $row['extension']);
                    } else {
                        $url = $this->results_controllers['extensions']['page'];
                    }
                }

                if (is_array($temp_key_field)) {
                    $httpQuery = [];
                    foreach ($temp_key_field as $var) {
                        $httpQuery[$var] = $row[$var];
                    }
                } else {
                    $httpQuery = [$temp_key_field => $row[$temp_key_field]];
                }
                $url .= "&" . http_build_query($httpQuery);
                $tmp ['type'] = $row['type'] ?? null;
                $tmp ['href'] = $this->html->getSecureURL($url);
                $tmp ['text'] = $this->html->buildButton(
                    [
                        'href'   => $tmp['href'],
                        'title'  => $row ['text'],
                        'text'   => $row['title'],
                        'target' => '_blank',
                        'style'  => 'link',
                    ]
                );
                $tmp['total_num_rows'] = $table[0]['total_num_rows'] ?? 0;
                $output [] = $tmp;
            }
        }
        return $output;
    }

    protected function _prepareCommandsResponse($table = [])
    {
        $output = [];
        foreach ($table as $row) {
            $tmp = [];
            $tmp['text'] = $this->html->buildButton(
                    [
                        'href'   => $row['url'],
                        'title'  => $row ['text'],
                        'text'   => $row['title'],
                        'target' => '_blank',
                        'style'  => 'link',
                    ]
                );
            $tmp['total_num_rows'] = $table[0]['total_num_rows'] ?? 0;
            $output[] = $tmp;
        }
        return $output;
    }

    /**
     * function to get possible commands for the lookup
     *
     * @param string $keyword
     * @param string $mode ('total')
     *
     * @return array|int
     */
    protected function _possibleCommands($keyword, $mode = '')
    {
        $commands_obj = new AdminCommands();
        $this->commands = $commands_obj->commands;
        $result = $commands_obj->getCommands($keyword);

        if ($mode == 'total') {
            return ($result && isset($result['found_actions']) ? count($result['found_actions']) : 0);
        }

        $output = [];
        if (isset($result['found_actions'])) {
            foreach ($result['found_actions'] as $command) {
                $output[] = [
                    'text'  => $result['command'] . " " . $command['title'] . " " . $result['request'],
                    'title' => $result['command'] . " " . $command['title'] . " " . $result['request'],
                    'url'   => $command['url'],
                ];
            }
        }
        return $output;
    }
}
