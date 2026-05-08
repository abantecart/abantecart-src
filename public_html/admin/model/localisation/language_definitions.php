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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ModelLocalisationLanguageDefinitions extends Model
{
    /**
     * @param array $data
     *
     * @return bool
     * @throws AException
     */
    public function addLanguageDefinition($data)
    {
        if (!is_array($data) || !$data) {
            return false;
        }

        $update_data = [];
        foreach ($data as $key => $val) {
            $update_data[$this->db->escape($key)] = htmlspecialchars_decode(trim($val));
        }

        if (empty($update_data['language_key'])
            || empty($update_data['language_value'])
            || empty($update_data['language_id'])
            || empty($update_data['block'])
        ) {
            $message = 'Trying to write new language definition but data is wrong.' . PHP_EOL . '
			   language_key: ' . $update_data['language_key'] . PHP_EOL . ',
			   language_value: ' . $update_data['language_value'] . PHP_EOL . ',
			   block: ' . $update_data['block'] . PHP_EOL . ',
			   section: ' . (int) $update_data['section'] . PHP_EOL . ',
			   language_id: ' . (int) $update_data['language_id'] . '.';
            $warning = new AWarning($message);
            $warning->toLog()->toDebug();
            return false;
        }
        unset($update_data['language_definition_id']);

        //Handle special case of the main block (english.xml, spanish.xml…)
        //do not auto translate this case. autoTranslate will not save to a right key
        $autoTranslate = true;
        if ($this->language->isMainBlock($update_data['block'], $update_data['language_id'])) {
            $autoTranslate = false;
        } else {
            if ($this->language->isMainBlock($update_data['block'])) {
                $autoTranslate = false;
                //this is a main block in another language. Need to get the right block name
                $lang_det = $this->language->getLanguageDetailsByID($update_data['language_id']);
                $update_data['block'] = $lang_det['filename'];
            }
        }

        //save definition. 
        $this->language->replaceDescriptions(
            'language_definitions',
            [
                'section'      => (int) $update_data['section'],
                'block'        => $update_data['block'],
                'language_key' => $update_data['language_key'],
            ],
            [
                $update_data['language_id'] => [
                    'section'        => (int) $update_data['section'],
                    'block'          => $update_data['block'],
                    'language_key'   => $update_data['language_key'],
                    'language_value' => $update_data['language_value'],
                ],
            ],
            $autoTranslate
        );

        $this->cache->remove(['localization', 'admin_menu']);
        return true;
    }

    /**
     * @param int $id
     * @param array $data
     *
     * @return bool
     * @throws AException
     */
    public function editLanguageDefinition($id, $data)
    {
        if (empty($id) || !is_array($data) || !$data) {
            return false;
        }

        //NOTE: On edit we only care about new definition value.
        //Other details are loaded from definition 
        $lang_value = (string) $data['language_value'];
        $update_data = $this->getLanguageDefinition($id);

        //Handle special case of the main block (english.xml, spanish.xml…)
        //do not auto translate this case. autoTranslate will not save to a right key
        $autoTranslate = true;
        if ($this->language->isMainBlock($update_data['block'], $update_data['language_id'])) {
            $autoTranslate = false;
        } else {
            if ($this->language->isMainBlock($update_data['block'])) {
                $autoTranslate = false;
                //this is a main block in another language. Need to get the right block name
                $lang_det = $this->language->getLanguageDetailsByID($update_data['language_id']);
                $update_data['block'] = $lang_det['filename'];
            }
        }

        if (isset($update_data['language_key'])) {
            $this->language->replaceDescriptions(
                'language_definitions',
                [
                    'section'      => (int) $update_data['section'],
                    'block'        => $update_data['block'],
                    'language_key' => $update_data['language_key'],
                ],
                [
                    (int) $update_data['language_id'] => [
                        'section'        => (int) $update_data['section'],
                        'block'          => $update_data['block'],
                        'language_key'   => $update_data['language_key'],
                        'language_value' => html_entity_decode($lang_value, ENT_QUOTES, 'UTF-8'),
                    ],
                ],
                $autoTranslate
            );
        } else {
            $this->language->replaceDescriptions(
                'language_definitions',
                [
                    'section'      => (int) $update_data['section'],
                    'block'        => $update_data['block'],
                    'language_key' => $update_data['language_key'],
                ],
                [
                    (int) $update_data['language_id'] => [
                        'language_value' => html_entity_decode($lang_value, ENT_QUOTES, 'UTF-8'),
                    ],
                ],
                $autoTranslate
            );
        }

        $this->cache->remove(['localization', 'admin_menu']);
        return true;
    }

    /**
     * @param int $id
     *
     * @throws AException
     */
    public function deleteLanguageDefinition($id)
    {
        $result = $this->db->query(
            "SELECT language_id, `section`, `language_key`, `block`
            FROM " . $this->db->table("language_definitions") . " 
            WHERE language_definition_id = '" . (int) $id . "'"
        );
        foreach ($result->rows as $row) {
            $this->db->query(
                "DELETE FROM " . $this->db->table("language_definitions") . " 
							  WHERE `section` = '" . $row['section'] . "'
							  		AND `block` = '" . $row['block'] . "'
							  		AND `language_key` = '" . $row['language_key'] . "'"
            );
        }
        $this->cache->remove(['localization', 'admin_menu']);
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws AException
     */
    public function getLanguageDefinition($id)
    {
        $query = $this->db->query(
            "SELECT DISTINCT *
            FROM " . $this->db->table("language_definitions") . " 
            WHERE language_definition_id = '" . (int) $id . "'"
        );
        return $query->row;
    }

    /**
     * @param string $key
     * @param int $language_id
     * @param string $block
     * @param int $section
     *
     * @return int
     * @throws AException
     */
    public function getLanguageDefinitionIdByKey($key, $language_id, $block, $section)
    {
        $query = $this->db->query(
            "SELECT language_definition_id
            FROM " . $this->db->table("language_definitions") . " 
            WHERE language_key = '" . $this->db->escape($key) . "'
                AND block='" . $this->db->escape($block) . "'
                AND language_id='" . $this->db->escape($language_id) . "'
                AND section='" . (int) $section . "'"
        );
        return (int) $query->row['language_definition_id'];
    }

    /**
     * @param string $key
     * @param string $block
     * @param int $section
     *
     * @return array
     * @throws AException
     */
    public function getAllLanguageDefinitionsIdByKey($key, $block, $section)
    {
        $query = $this->db->query(
            "SELECT language_definition_id
            FROM " . $this->db->table("language_definitions") . " 
            WHERE language_key = '" . $this->db->escape($key) . "'
                AND block='" . $this->db->escape($block) . "'
                AND section='" . (int) $section . "'"
        );

        return $query->rows;
    }

    /**
     * @param array $data
     * @param string $mode
     *
     * @return array
     * @throws AException
     */
    public function getLanguageDefinitions($data = [], $mode = 'default')
    {
        if ($data || $mode == 'total_only') {
            $filter = $data['filter'] ?? [];

            if ($mode == 'total_only') {
                $sql = "SELECT count(*) as total
						FROM " . $this->db->table("language_definitions") . " ld
						LEFT JOIN " . $this->db->table("languages") . " l 
						    ON l.language_id = ld.language_id";
            } else {
                $sql = "SELECT ld.*, l.name as language_name, l.code as language_code
						FROM " . $this->db->table("language_definitions") . " ld
						LEFT JOIN " . $this->db->table("languages") . " l 
						    ON l.language_id = ld.language_id";
            }

            if (has_value($filter['section'])) {
                $filter['section'] = $filter['section'] == 'admin' ? 1 : 0;
                $sql .= " WHERE `section` = '" . (int) $filter['section'] . "' ";
            } else {
                $sql .= " WHERE 1=1 ";
            }

            $data['language_id'] = (int) $data['language_id'] ? : (int) $this->request->get['language_id'];

            if ($data['language_id'] > 0) {
                $sql .= " AND ld.language_id = '" . (int) $data['language_id'] . "'";
            }

            if (!empty($data['subsql_filter'])) {
                $sql .= " AND " . $data['subsql_filter'];
            }

            if (isset($filter['language_key'])) {
                $sql .= " AND `language_key` LIKE '%" . $this->db->escape($filter['language_key'], true) . "%' ";
            }

            if (isset($filter['name'])) {
                $sql .= " AND l.name LIKE '%" . $this->db->escape($filter['name']) . "%' ";
            }

            //If for total, we're done building the query
            if ($mode == 'total_only') {
                $query = $this->db->query($sql);
                return $query->row['total'];
            }

            $sort_data = [
                'date_modified',
                'language_key',
                'language_value',
                'block',
            ];

            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY " . $data['sort'];
            } else {
                $sql .= " ORDER BY date_modified DESC, language_key, block";
            }

            if (isset($data['order']) && (strtoupper($data['order']) == 'DESC')) {
                $sql .= " DESC";
            } else {
                $sql .= " ASC";
            }

            if (isset($data['start']) || isset($data['limit'])) {
                if ($data['start'] < 0) {
                    $data['start'] = 0;
                }

                if ($data['limit'] < 1) {
                    $data['limit'] = 20;
                }

                $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
            }

            $query = $this->db->query($sql);
            return $query->rows;
        } else {
            $cache_key = 'localization.language.definitions';
            $language_data = $this->cache->pull($cache_key);
            if ($language_data === false) {
                $language_data = [];
                $query = $this->db->query(
                    "SELECT *
                   FROM " . $this->db->table("language_definitions") . " 
                   WHERE language_id=" . (int) $this->config->get('admin_language_id') . "
                   ORDER BY date_modified DESC, language_key, block"
                );

                foreach ($query->rows as $result) {
                    $language_data[$result['code']] = [
                        'language_definition_id' => $result['language_definition_id'],
                        'language_id'            => $result['language_id'],
                        'section'                => $result['section'],
                        'block'                  => $result['block'],
                        'language_key'           => $result['language_key'],
                        'language_value'         => $result['language_value'],
                        'date_modified'          => $result['date_modified'],
                    ];
                }
                $this->cache->push($cache_key, $language_data);
            }

            return $language_data;
        }
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws AException
     */
    public function getTotalDefinitions($data = [])
    {
        return $this->getLanguageDefinitions($data, 'total_only');
    }

    /**
     * Load necessary data and build form for definitions add or edit
     *
     * @param ARequest $request - Data from request object
     * @param array $data - from requester
     * @param AForm $form - form object
     *
     * @return array ($data processed and returned)
     * @throws AException
     */
    public function buildFormData(&$request, &$data, &$form)
    {
        $fields = ['language_key', 'language_value', 'block', 'section'];
        $language_definition_id = $request->get['language_definition_id'];
        $view_mode = 'all';

        $main_block = false;

        //if existing definition disable edit for some fields
        $disable_attr = '';
        if (has_value($language_definition_id)) {
            $disable_attr = ' readonly ';
        }

        $languages = $this->language->getAvailableLanguages();

        $content_lang_id = $this->language->getContentLanguageID();
        //load current content language to data
        foreach ($languages as $lang) {
            if ($view_mode != 'all' && $lang['language_id'] != $content_lang_id) {
                continue;
            }
            $data['languages'][$lang['language_id']] = $lang;
        }

        $def_det = [];
        $all_defs = [];
        //!!!! ATTENTION: Important to understand this process flow. See comments
        if (has_value($language_definition_id)) {
            // 1. language_definition_id is provided, load definition based on ID
            $def_det = $this->getLanguageDefinition($language_definition_id);
            if (empty($def_det)) {
                //this is an incorrect ID redirect to create new
                $parms = '&view_mode=' . $view_mode;
                return (['redirect_params' => $parms]);
            }

            //special case then the main file is edited (English, Russian e.t.c. ).
            //Candidate for improvement. Rename these files to main.xml
            $main_block = $this->language->isMainBlock($def_det['block'], $def_det['language_id']);

            // 2. make sure we load all the languages from XML in case they were not used yet.
            foreach ($languages as $lang) {
                $new_lang_obj = new ALanguageManager($this->registry, $lang['code'], $def_det['section']);
                if ($main_block) {
                    $block_path = $lang['filename'];
                    $block = $lang['filename'];
                } else {
                    $block = $def_det['block'];
                    $block_path = $new_lang_obj->convert_block_to_file($def_det['block']);
                    if (empty($block_path)) {
                        $block_path = $block;
                    }
                }
                if ($block_path) {
                    $new_lang_obj->_load($block_path);
                    //now load definition for all languages to be available in the template		
                    $all_defs[] = $this->LoadDefinitionSetEmpty(
                        $def_det['section'], $block, $def_det['language_key'], $lang['language_id']
                    );
                }
            }
            // 3. Redirect to the correct definition for the current content language selected
            //if different from the selected language_definition_id
            if ($def_det['language_id'] != $content_lang_id) {
                if ($main_block) {
                    $block = $data['languages'][$content_lang_id]['filename'];
                } else {
                    $block = $def_det['block'];
                }
                $new_def = $this->LoadDefinitionSetEmpty(
                    $def_det['section'], $block, $def_det['language_key'], $content_lang_id
                );
                //if exists redirect with the correct language_definition_id for the content language
                if (has_value($new_def['language_definition_id'])) {
                    $parms = '&view_mode=' . $view_mode;
                    $parms .= '&language_definition_id=' . $new_def['language_definition_id'];
                    return (['redirect_params' => $parms]);
                } else {
                    //allow creating a new one with a blank definition value
                    $def_det = $new_def;
                }
            }
        }

        foreach ($fields as $field) {
            $data[$field] = $request->post[$field] ?? $def_det[$field] ?? '';
        }

        $data['form']['submit'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'submit',
                'text'  => $this->language->get('button_save'),
                'style' => 'button1',
            ]
        );
        $data['form']['cancel'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'cancel',
                'text'  => $this->language->get('button_cancel'),
                'style' => 'button2',
            ]
        );

        if ($disable_attr) {
            $section_txt = $this->language->get('text_storefront');
            if ($data['section']) {
                $section_txt = $this->language->get('text_admin');
            }
            $data['form']['fields']['section'] = $section_txt .
                $form->getFieldHtml(
                    [
                        'type'  => 'hidden',
                        'name'  => 'section',
                        'value' => $data['section'],
                    ]
                );
            $data['form']['fields']['block'] = $data['block'] .
                $form->getFieldHtml(
                    [
                        'type'  => 'hidden',
                        'name'  => 'block',
                        'value' => $data['block'],
                    ]
                );
            $data['form']['fields']['language_key'] = $data['language_key'] .
                $form->getFieldHtml(
                    [
                        'type'  => 'hidden',
                        'name'  => 'language_key',
                        'value' => $data['language_key'],
                    ]
                );
        } else {
            $data['form']['fields']['section'] = $form->getFieldHtml(
                [
                    'type'     => 'selectbox',
                    'name'     => 'section',
                    'options'  => [
                        1 => $this->language->get('text_admin'),
                        0 => $this->language->get(
                            'text_storefront'
                        ),
                    ],
                    'value'    => $data['section'],
                    'required' => true,
                ]
            );

            $data['form']['fields']['block'] = $form->getFieldHtml(
                [
                    'type'     => 'input',
                    'name'     => 'block',
                    'value'    => $data['block'],
                    'required' => true,
                ]
            );
            $data['form']['fields']['language_key'] = $form->getFieldHtml(
                [
                    'type'     => 'input',
                    'name'     => 'language_key',
                    'value'    => $data['language_key'],
                    'required' => true,
                ]
            );
        }

        if ($main_block) {
            $data['form']['fields']['main_block'] = $form->getFieldHtml(
                [
                    'type'  => 'hidden',
                    'name'  => 'main_block',
                    'value' => 1,
                ]
            );
        }

        //load all language fields for this definition to be available in the template
        foreach ($data['languages'] as $i) {
            $langId = $i['language_id'];
            $value = '';
            $id = '';
            if (!empty($request->post['language_value'][$langId])) {
                $value = $request->post['language_value'][$langId];
                foreach ($all_defs as $ii) {
                    if ($ii['language_id'] == $langId) {
                        $id = $ii['language_definition_id'];
                        break;
                    }
                }
            } else {
                if (!empty($all_defs)) {
                    foreach ($all_defs as $ii) {
                        if ($ii['language_id'] == $langId) {
                            $value = $ii['language_value'];
                            $id = $ii['language_definition_id'];
                            break;
                        }
                    }
                }
            }
            $data['form']['fields']['language_value'][$langId] = $form->getFieldHtml(
                    [
                        'type'     => 'textarea',
                        'name'     => 'language_value[' . $langId . ']',
                        'value'    => $value,
                        'required' => true,
                        'style'    => 'large-field',
                    ]
                )
                . $form->getFieldHtml(
                    [
                        'type'  => 'hidden',
                        'name'  => 'language_definition_id[' . $langId . ']',
                        'value' => $id,
                    ]
                );
        }
        return ([]);
    }

    /**
     * Special load for definition, with value or set empty value if not found
     *
     * @param string $section
     * @param string $block
     * @param string $lang_key
     * @param int $lang_id
     *
     * @return array
     * @throws AException
     */
    public function LoadDefinitionSetEmpty($section, $block, $lang_key, $lang_id)
    {
        $ret_arr = $this->getLanguageDefinitions(
            [
                'subsql_filter' =>
                    "section = '" . $this->db->escape($section) . "' 
                        AND block = '" . $this->db->escape($block) . "' 
                        AND language_key = '" . $this->db->escape($lang_key) . "' ",
                'language_id'   => (int) $lang_id,
            ]
        );
        if (isset($ret_arr[0])) {
            return $ret_arr[0];
        } else {
            //not found any details return only some values (new)
            $ret_arr['block'] = $block;
            $ret_arr['section'] = $section;
            $ret_arr['language_key'] = $lang_key;
            $ret_arr['language_id'] = $lang_id;
            $ret_arr['language_definition_id'] = '';
            return $ret_arr;
        }
    }
}