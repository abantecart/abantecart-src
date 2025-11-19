<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
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
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * Class ControllerPagesExtensionEncryptionDataManager
 */
class ControllerPagesExtensionEncryptionDataManager extends AController
{
    private $error = [];
    private $fields = [
        'key_name',
        'key_length',
        'private_key_type',
        'encrypt_key',
        'passphrase',
        'enc_key',
        'enc_test_mode'
    ];

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('encryption_data_manager/encryption_data_manager');
        $this->document->setTitle($this->language->get('encryption_data_manager_name'));
        $this->load->model('setting/setting');

        $enc = new ASSLEncryption();
        if (!$enc->active || !$enc->getKeyPath()) {
            $this->error['warning'] = $this->language->get('error_openssl_disabled');
        }

        $enc_data = new ADataEncryption();
        if (!$enc_data->active) {
            $this->error['warning'] = $this->language->get('error_data_encryption_disabled');
        }

        if ($this->request->is_POST() && $this->_validate()) {

            $this->cache->remove('encryption');
            $new_keys = [];
            foreach ($this->request->post as $key => $value) {
                $matches = [];
                if (preg_match('/^new_key_for_(.*)$/', $key, $matches) && (int)$value > 0) {
                    $new_keys[$matches[1]] = (int)$value;
                }
            }

            if (!empty($this->request->post['key_name'])) {
                //key creation ste
                $this->request->post['key_name'] = preformatTextID($this->request->post['key_name']);
                $keys = $this->_create_key_pair($this->request->post);
                if ($keys['public'] || $keys['private']) {
                    $this->session->data['success'] = $this->language->getAndReplace(
                        'text_success_key_get',
                        replaces: [$keys['public'], $keys['private']]
                    );
                } else {
                    $this->error['warning'] = $this->language->get('error_generating_keys_failed');
                }
            } else {
                if (!empty($this->request->post['enc_key'])) {
                    //encryption step
                    $enc_result = $this->_initial_data_encryption($this->request->post);
                    if ($this->request->post['enc_test_mode']) {
                        $this->session->data['success'] = $this->language->getAndReplace(
                            'text_encryption_test',
                            replaces: implode('<br/>', $enc_result['result'])
                        );
                    } else {
                        if (count($enc_result['result']) && !count($enc_result['errors'])) {
                            $this->session->data['success'] = '<br>' .
                                $this->language->getAndReplace(
                                    'text_success_encrypting',
                                    replaces: [implode('<br/>', $enc_result['result']), $enc_result['key_name']]
                                );
                        } else {
                            if (count($enc_result['result']) && count($enc_result['errors'])) {
                                //mixed result
                                $this->session->data['success'] = '<br>' .
                                    $this->language->getAndReplace('text_success_encrypting',
                                        replaces: [implode('<br/>', $enc_result['result']), $enc_result['key_name']]
                                    );
                                $this->error['warning'] = '<br>' . implode('<br/>', $enc_result['errors']);
                            } else {
                                $this->error['warning'] = $this->language->get('error_encrypting');
                            }
                        }
                    }
                } else {
                    if (count($new_keys)) {
                        //re-encryption step
                        $enc_result = [];
                        foreach ($new_keys as $old_key_id => $new_key_id) {
                            $enc_result = $this->_key_rotation(['old_key' => $old_key_id, 'new_key' => $new_key_id]);
                            if (count($enc_result['result']) && !count($enc_result['errors'])) {
                                $this->session->data['success'] .= $this->language->getAndReplace(
                                    'text_success_encrypting',
                                    replaces: [implode('<br/>', $enc_result['result']), $enc_result['key_name']]
                                );
                            } else {
                                if (count($enc_result['result']) && count($enc_result['errors'])) {
                                    //mixed result
                                    $this->session->data['success'] .= $this->language->getAndReplace(
                                        'text_success_encrypting',
                                        replaces: [implode('<br/>', $enc_result['result']), $enc_result['key_name']]
                                    );
                                    $this->error['warning'] = '<br>' . implode('<br/>', $enc_result['errors']);
                                } else {
                                    $this->error['warning'] .= $this->language->get('error_encrypting');
                                }
                            }
                        }
                    } else {
                        $this->error['warning'] = $this->language->get('error_required_data_missing');
                    }
                }
            }
        }

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }
        $this->data['success'] = $this->session->data['success'];
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $this->document->initBreadcrumb([
            'href'      => $this->html->getSecureURL('index/home'),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ]);
        $this->document->addBreadcrumb([
            'href'      => $this->html->getSecureURL('extension/extensions/extensions'),
            'text'      => $this->language->get('text_extensions'),
            'separator' => ' :: ',
        ]);
        $this->document->addBreadcrumb([
            'href'      => $this->html->getSecureURL('extension/encryption_data_manager'),
            'text'      => $this->language->get('encryption_data_manager_name'),
            'separator' => ' :: ',
            'current'   => true,
        ]);

        foreach ($this->fields as $f) {
            $this->data [$f] = $this->request->post[$f] ?? $this->config->get($f);
        }

        //Build sections for display 		
        $this->data ['action'] = $this->html->getSecureURL(
            'extension/encryption_data_manager',
            '&extension=encryption_data_manager'
        );
        $this->data['cancel'] = $this->html->getSecureURL('extension/encryption_data_manager');
        $this->data ['heading_title'] = $this->language->get('text_additional_settings');
        $this->data ['update'] = $this->html->getSecureURL(
            'listing_grid/extension/update',
            '&id=encryption_data_manager'
        );

        //load existing keys. 
        $pub_keys_options = $this->_load_key_names($enc, 1);

        $form = new AForm ('HT');
        $form->setForm(
            [
                'form_name' => 'keyRotaionFrm',
                'update'    => $this->data ['update']
            ]
        );

        //encryption usage section
        $enc_usage = [];
        $enc_usage['section_id'] = 'enc_usage';

        $enc_usage['form']['form_open'] = $form->getFieldHtml([
            'type'   => 'form',
            'name'   => 'keyRotaionFrm',
            'attr'   => 'data-confirm-exit="true" class="aform form-horizontal"',
            'action' => $this->data ['action'],
        ]);
        $enc_usage['form']['submit'] = $form->getFieldHtml([
            'type' => 'button',
            'name' => 'submit',
            'text' => $this->language->get('button_encrypt_data'),
        ]);
        $enc_usage['form']['reset'] = $form->getFieldHtml([
            'type' => 'button',
            'name' => 'reset',
            'text' => $this->language->get('button_reset'),
        ]);

        $enc_usage['name'] = $this->language->get('encryption_usage');
        $enc_usage['form_title'] = $enc_usage['name'];

        //load un-encrypted data usage
        $this->data['unencrypted_stats'] = $this->_load_unencrypted_stats($enc_data);

        //load encrypted data usage
        $usage = $this->_load_usage_details($enc, $enc_data);
        //add a new key selector to each row
        foreach ($usage as $i => $u) {
            //remove the current key
            $key_list = $pub_keys_options;
            unset($key_list[$u['key_id']]);
            $key_list[0] = "--";
            $usage[$i]['actons'] = $form->getFieldHtml([
                'type'    => 'selectbox',
                'name'    => 'new_key_for_' . $u['key_id'],
                'options' => $key_list,
                'value'   => 0,
            ]);

        }
        $enc_usage['usage_details'] = $usage;

        $this->data['sections'][] = $enc_usage;

        //key generation section 
        $form = new AForm ('HT');
        $form->setForm(['form_name' => 'keyGenFrm', 'update' => $this->data ['update']]);

        $key_gen = [];
        $key_gen['section_id'] = 'key_gen';
        $key_gen['name'] = $this->language->get('key_gen_section_name');
        $key_gen['form_title'] = $key_gen['name'];
        $key_gen['form']['form_open'] = $form->getFieldHtml([
            'type'   => 'form',
            'name'   => 'keyGenFrm',
            'attr'   => 'data-confirm-exit="true" class="aform form-horizontal"',
            'action' => $this->data ['action'],
        ]);
        $key_gen['form']['submit'] = $form->getFieldHtml([
            'type' => 'button',
            'name' => 'submit',
            'text' => $this->language->get('button_generate_keys'),
        ]);
        $key_gen['form']['reset'] = $form->getFieldHtml([
            'type' => 'button',
            'name' => 'reset',
            'text' => $this->language->get('button_reset'),
        ]);

        $key_gen['form']['fields']['key_name'] = $form->getFieldHtml([
            'type'     => 'input',
            'name'     => 'key_name',
            'value'    => $this->data['key_name'],
            'required' => true,
        ]);

        $key_gen['form']['fields']['key_length'] = $form->getFieldHtml([
            'type'  => 'input',
            'name'  => 'key_length',
            'value' => !$this->data['key_length'] ? 2048 : $this->data['key_length'],
        ]);

        $key_gen['form']['fields']['private_key_type'] = $form->getFieldHtml([
            'type'    => 'selectbox',
            'name'    => 'private_key_type',
            'options' => [
                OPENSSL_KEYTYPE_RSA => 'OPENSSL_KEYTYPE_RSA',
                // Only OPENSSL_KEYTYPE_RSA supported by PHP now
                //	OPENSSL_KEYTYPE_DSA => 'OPENSSL_KEYTYPE_DSA', 
                //	OPENSSL_KEYTYPE_DH => 'OPENSSL_KEYTYPE_DH'
            ],
            'value'   => $this->data['private_key_type'],
        ]);

        /*
        * Password protected key is not supported 
        $key_gen['form']['fields']['encrypt_key'] = $form->getFieldHtml(array(
                'type' => 'checkbox',
                'name' => 'encrypt_key',
                'value' => $this->data['encrypt_key'],
                'style'  => 'btn_switch',
            ));

        $key_gen['form']['fields']['passphrase'] = $form->getFieldHtml(array(
                'type' => 'input',
                'name' => 'passphrase',
                'value' => $this->data['passphrase'],
            ));

        */
        $this->data['sections'][] = $key_gen;

        //data encryption section 		
        $form2 = new AForm ('HT');
        $form2->setForm(['form_name' => 'dataEncFrm', 'update' => $this->data ['update']]);

        $data_enc = [];
        $data_enc['section_id'] = 'data_encryption';
        $data_enc['name'] = $this->language->get('data_encryption');

        $data_enc['form_title'] = $data_enc['name'];
        $data_enc['form']['form_open'] = $form2->getFieldHtml([
            'type'   => 'form',
            'name'   => 'dataEncFrm',
            'attr'   => 'data-confirm-exit="true" class="aform form-horizontal"',
            'action' => $this->data ['action'],
        ]);
        $data_enc['form']['submit'] = $form2->getFieldHtml([
            'type' => 'button',
            'name' => 'submit',
            'text' => $this->language->get('button_encrypt_data'),
        ]);
        $data_enc['form']['reset'] = $form2->getFieldHtml([
            'type' => 'button',
            'name' => 'reset',
            'text' => $this->language->get('button_reset'),
        ]);

        $data_enc['form']['fields']['enc_key'] = $form2->getFieldHtml([
            'type'    => 'selectbox',
            'name'    => 'enc_key',
            'options' => $pub_keys_options,
            'value'   => $this->data['enc_key'],
        ]);
        $data_enc['note'] = $this->language->get('post_encrypting_notice');

        $enc_config_tables = $enc_data->getEcryptedTables();
        if (has_value($enc_config_tables)) {
            /*
            $enc_tables_options = [];
            foreach ($enc_config_tables as $table_name) {
                $enc_tables_options[$table_name] = $table_name;
            }

            //Per table encryption is not supported YET
            $data_enc['form']['fields']['enc_tables'] = $form2->getFieldHtml(array(
                    'type' => 'selectbox',
                    'name' => 'enc_tables',
                    'options' => $enc_tables_options,
                    'value' => $this->data['enc_tables'],
                ));
            */
            $data_enc['form']['fields']['enc_tables'] = implode(', ', $enc_config_tables);

            $data_enc['form']['fields']['enc_test_mode'] = $form2->getFieldHtml([
                'type'  => 'checkbox',
                'name'  => 'enc_test_mode',
                'value' => $this->data['enc_test_mode'],
                'style' => 'btn_switch',
            ]);

            $data_enc['form']['fields']['enc_remove_original'] = $form2->getFieldHtml([
                'type'  => 'checkbox',
                'name'  => 'enc_remove_original',
                'value' => (isset($this->data['enc_remove_original'])) ? $this->data['enc_remove_original'] : 1,
            ]);

        } else {
            $data_enc['note'] = "<b>Enable Data Encryption first!<b>";
        }

        $this->data['sections'][] = $data_enc;

        $this->view->batchAssign($this->language->getASet());

        //load tabs controller
        $this->request->get['extension'] = 'encryption_data_manager';
        $this->data['groups'][] = 'additional_settings';
        $this->data['link_additional_settings'] = $this->html->getSecureURL('extension/encryption_data_manager');
        $this->data['active_group'] = 'additional_settings';

        $tabs_obj = $this->dispatch('pages/extension/extension_tabs', [$this->data]);
        $this->data['tabs'] = $tabs_obj->dispatchGetOutput();
        unset($tabs_obj);

        $obj = $this->dispatch('pages/extension/extension_summary', [$this->data]);
        $this->data['extension_summary'] = $obj->dispatchGetOutput();
        unset($obj);

        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/extension/encryption_data_manager.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    private function _validate()
    {
        if (!$this->user->canModify('extension/encryption_data_manager')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!empty($this->request->post['key_name'])) {
            //validate uniqueness of key name
            $test_row = $this->db->query(
                "SELECT *
                 FROM " . $this->db->table('encryption_keys') . "
                 WHERE `key_name` = '" . $this->db->escape($this->request->post['key_name']) . "'"
            );
            if ($test_row->num_rows) {
                $this->error['warning'] = $this->language->get('error_duplicate_key');
            }
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    private function _create_key_pair($data)
    {
        if (empty($data['key_name'])) {
            return [];
        }

        $data['encrypt_key'] = $data['encrypt_key'] ? 1 : 0;

        //generate keys	and save	
        $enc = new ASSLEncryption ();
        $keys = $enc->generate_ssl_key_pair($data, $data['passphrase']);
        $enc->save_ssl_key_pair($keys, $data['key_name']);

        //update record in the database
        $this->db->query(
            "INSERT INTO " . $this->db->table('encryption_keys') . " 
            SET `key_name` = '" . $data['key_name'] . "', `status` = 1;"
        );

        return $keys;
    }

    //Encrypt data initial from original tables
    private function _initial_data_encryption($data)
    {
        if (empty($data['enc_key'])) {
            return [];
        }
        //load key details 
        $query = $this->db->query(
            "SELECT * 
            FROM " . $this->db->table('encryption_keys') . " 
            WHERE `key_id` = " . (int)$data['enc_key']);
        if ($query->num_rows != 1) {
            return [];
        }

        $key_name = $query->row['key_name'];
        $key_id = $query->row['key_id'];

        $result = [];
        $errors = [];

        //generate keys	and save	
        $enc_data = new ADataEncryption($key_name);
        foreach ($enc_data->getEcryptedTables() as $table_name) {
            //use tables with postfix in the name via $this->db->table()
            $enc_table_name = $this->db->table($table_name);
            $id_field = $enc_data->getEncryptedTableID($table_name);
            // important to use non-encrypted table. Do NOT use table function wrapper
            $query_read = $this->db->query("SELECT * FROM " . $this->db->table($table_name));
            $count = 0;
            foreach ($query_read->rows as $record) {
                //if encrypting customers, the table keeps login as email before encrypting email
                if ($table_name == 'customers' && empty($record['loginname'])) {
                    $record['loginname'] = $record['email'];
                }
                //specify a key to be used for encryption
                $record['key_id'] = $key_id;
                $enc_rec_data = $enc_data->encrypt_data($record, $table_name);
                //check if this is not a test mode and we can write
                $count++;
                if (!$data['enc_test_mode']) {
                    $insertFields = '';
                    foreach ($enc_rec_data as $col => $val) {
                        if (has_value($val)) {
                            if (!empty($insertFields)) {
                                $insertFields .= ", ";
                            }
                            $insertFields .= "`$col` = '" . $this->db->escape($val) . "'";
                        }
                    }

                    try {
                        $this->db->query("INSERT INTO " . $enc_table_name . " SET " . $insertFields . ";");
                    } catch (AException $e) {
                        $errors[] = "Error saving: Table " . $enc_table_name . " record ID: " . $enc_rec_data[$id_field] . "! See error log for details";
                        $this->log->write($e->getMessage());
                        $count--;
                        continue;
                    }

                    //remove the original record if requested
                    if ($data['enc_remove_original']) {
                        $this->db->query("DELETE FROM " . $this->db->table($table_name) . " WHERE " . $id_field . "=" . $record[$id_field]);
                    }

                } else {
                    //check if such a row exists for test
                    $test_row = $this->db->query(
                        "SELECT * 
                        FROM " . $this->db->table($table_name) . " 
                        WHERE " . $id_field . " = " . $enc_rec_data[$id_field]
                    );
                    if ($test_row->num_rows) {
                        $errors[] = "Error: Duplicate record ID: " . $enc_rec_data[$id_field]
                            . " in the table " . $this->db->table($table_name) . " !";
                        $count--;
                    }
                }

            }
            $result[] = "<b>Table " . $table_name . " has encrypted " . $count . " records with key name " . $key_name . "</b>";
        }
        return ['key_name' => $key_name, 'result' => $result, 'errors' => $errors];
    }

    //Re-encrypt data with a new key
    private function _key_rotation($data)
    {
        if (empty($data['old_key']) && empty($data['new_key'])) {
            return [];
        }
        //load key details 
        $query = $this->db->query(
            "SELECT * 
            FROM " . $this->db->table('encryption_keys') . " 
            WHERE `key_id` = " . (int)$data['new_key']);
        if ($query->num_rows != 1) {
            return [];
        }

        $key_name = $query->row['key_name'];
        $key_id = $query->row['key_id'];

        $result = [];
        $errors = [];

        //generate keys	and save	
        $enc_data = new ADataEncryption($key_name);
        foreach ($enc_data->getEcryptedTables() as $table_name) {
            $enc_fields = $enc_data->getEcryptedFields($table_name);
            $id_field = $enc_data->getEncryptedTableID($table_name);
            // important to use encrypted table.
            $query_read = $this->db->query(
                "SELECT * 
                FROM " . $this->db->table($table_name) . " 
                WHERE key_id = " . (int)$data['old_key']
            );
            $count = 0;
            foreach ($query_read->rows as $record) {
                //decrypt original data 	
                $decryptedRow = $this->dcrypt->decrypt_data($record, $table_name);
                //specify a new key to be used for encryption
                $decryptedRow['key_id'] = $key_id;
                $enc_rec_data = $enc_data->encrypt_data($decryptedRow, $table_name);
                //check if this is not a test mode and we can write
                $count++;

                //Update records with a new encrypted data
                //build update statement with encrypted fields
                $update_sql = "key_id = " . $key_id;
                foreach ($enc_fields as $enc_fld) {
                    $update_sql .= ' ,' . $enc_fld . " = '" . $enc_rec_data[$enc_fld] . "' ";
                }
                $update_sql = "UPDATE " . $this->db->table($table_name) . " SET " . $update_sql;
                $update_sql .= " WHERE " . $id_field . "='" . $enc_rec_data[$id_field] . "'";
                try {
                    $this->db->query($update_sql);
                } catch (AException $e) {
                    $errors[] = "Error: Table $table_name record ID: "
                        . $enc_rec_data[$id_field] . " with key name $key_name failed updating!";
                    $count--;
                    continue;
                }
            }
            $result[] = "<b>Table " . $table_name . " has encrypted " . $count . " records with key name " . $key_name . "</b>";
        }
        return ['key_name' => $key_name, 'result' => $result, 'errors' => $errors];
    }

    private function _load_usage_details($enc, $enc_data)
    {
        $usage = [];
        $keys = $this->_load_keys($enc, 1);
        foreach ($keys as $key) {
            $data = [];
            $data['key_name'] = $key['key_name'];
            $data['key_id'] = $key['key_id'];
            $data['key_usage'] = $this->_load_encrypted_stats($enc_data, $key['key_id']);
            $usage[] = $data;
        }
        return $usage;
    }

    /**
     * @param ASSLEncryption $enc
     * @param string $status
     *
     * @return array
     * @throws AException
     */
    private function _load_key_names($enc, $status = '')
    {
        //load active keys from db
        $keys = $this->_load_keys($enc, $status);
        $pub_keys_options = [];
        foreach ($keys as $key_record) {
            foreach ($key_record as $pub_keys => $key_name) {
                if ($pub_keys == 'key_name') {
                    $pub_keys_options[$key_record['key_id']] = $key_name;
                    break;
                }
            }
        }
        return $pub_keys_options;
    }

    /**
     * @param ASSLEncryption $enc
     * @param string $status
     *
     * @return array
     * @throws AException
     */
    private function _load_keys($enc, $status = '')
    {
        //get key files from the directory
        $files = array_filter(glob($enc->getKeyPath() . '/*'), function ($file) {
            return preg_match('/.pub$/', $file);
        });
        $pub_keys = array_map(function ($file) {
            return basename($file, ".pub");
        }, $files);
        //load active keys from db
        $status_sql = '';
        if ($status) {
            $status_sql = " WHERE `status` = " . (int)$status;
        }

        $query = $this->db->query("SELECT * FROM " . $this->db->table('encryption_keys') . $status_sql);
        return $query->rows;
    }

    //Usage of tables with encrypted data and key

    /**
     * @param ADataEncryption $enc_data
     * @param int $key_id
     *
     * @return array
     * @throws AException
     */
    private function _load_encrypted_stats($enc_data, $key_id)
    {
        $usage = [];

        $enc_config_tables = $enc_data->getEcryptedTables();
        foreach ($enc_config_tables as $table_name) {
            $row = [];
            $row['table'] = $table_name;
            //select total counts. Important to use encrypted tables with the table function wrapper
            $query = $this->db->query(
                "SELECT count(*) as total FROM " . $this->db->table($table_name) . " WHERE key_id = " . (int)$key_id
            );
            $row['count'] = $query->row['total'];
            $usage[] = $row;
        }
        return $usage;
    }

    /**
     * Usage of tables with unencrypted data
     *
     * @param ADataEncryption $enc_data
     *
     * @return array
     * @throws AException
     */
    private function _load_unencrypted_stats($enc_data)
    {
        $usage = [];

        $enc_config_tables = $enc_data->getEcryptedTables();
        foreach ($enc_config_tables as $table_name) {
            $row = [];
            $row['table'] = $table_name;
            //select total counts. Important to use non-encrypted table. Do NOT use table function wrapper
            $query = $this->db->query("SELECT count(*) as total FROM " . $this->db->table($table_name));
            $row['count'] = $query->row['total'];
            $usage[] = $row;
        }
        return $usage;
    }
}