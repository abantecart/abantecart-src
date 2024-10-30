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

class ASupplier
{
    protected $registry;
    /** @var ACache */
    protected $cache;
    /** @var ADB */
    protected $db;
    /** @var string  */
    protected $code = '';
    protected $objectTypes = [];

    /**
     * @param string $code - supplier text code
     * @throws AException
     */
    public function __construct($code){
        $this->registry = Registry::getInstance();
        $this->cache = $this->registry->get('cache');
        $this->db = $this->registry->get('db');
        $this->code = $code;
        $this->objectTypes = $this->getObjectTypes();
    }

    /**
     * @return array - [object_name => object_id]
     * @throws AException
     */
    public function getObjectTypes()
    {
        $cacheKey = 'supplier.obj_types'.$this->code;
        $output = $this->cache->pull($cacheKey);
        if($output !== false){
            return $output;
        }
        $result = $this->db->query(
            "SELECT * 
            FROM ".$this->db->table('object_types')." 
            WHERE related_to='".$this->db->escape($this->code)."'"
        );
        $output = array_column($result->rows,'id', 'name');
        $this->cache->put($cacheKey, $output);
        return $output;
    }

    public function getObjectData(string $objectTypeName, ?int $objectId, ?string $uid)
    {
        if( !$objectTypeName ){
            throw new AException(
                AC_ERR_USER_ERROR,
                __FUNCTION__.': Illegal parameters! '.var_export(func_get_args(), true)
            );
        }

        $result = $this->db->query(
            "SELECT * 
            FROM ".$this->db->table('supplier_data')." 
            WHERE supplier_code='".$this->db->escape($this->code)."'
                AND object_type_id='".(int)$this->objectTypes[$objectTypeName]."'
                ".($objectId ? " AND object_id = '".$objectId."'" : '')."
                ".($uid ? " AND uid = '".$this->db->escape($uid)."'" : '')
        );
        foreach($result->rows as &$row){
            $row['data'] = json_decode($row['data'], true);
        }
        $result->row['data'] = json_decode($result->row['data'], true);
        return $result;
    }

    public function saveObjectData(string $objectTypeName, int $objectId, string $uid, array $data)
    {
        if( !$objectTypeName || !$objectId || !$uid || !$data ){
            throw new AException(
                AC_ERR_USER_ERROR,
                __FUNCTION__.': Illegal parameters! '.var_export(func_get_args(), true)
            );
        }

        $this->db->query(
            "REPLACE INTO ".$this->db->table('supplier_data')." 
            SET supplier_code='".$this->db->escape($this->code)."',
                object_type_id='".(int)$this->objectTypes[$objectTypeName]."', 
                object_id = '".$objectId."',
                uid = '".$this->db->escape($uid)."',
                data = '".$this->db->escape(json_encode($data))."'"
        );
    }

    /**
     * @param string $code - supplier text code
     * @return bool
     * @throws AException
     */
    public static function addSupplier(string $code)
    {
        if(IS_ADMIN !== true || !$code){
            return false;
        }
        $db = Registry::getInstance()->get('db');
        if(!$db ){
            throw new AException(
                AC_ERR_USER_ERROR,
                __FUNCTION__.': ADB class not found!'
            );
        }

        $db->query(
            "REPLACE INTO ".$db->table('suppliers')." (code, name) 
            VALUES ('".$db->escape($code)."','".$db->escape(mb_strtoupper($code))."');"
        );
        return true;
    }

    public static function removeSupplier(string $code)
    {
        if(IS_ADMIN !== true || !$code){
            return false;
        }
        $db = Registry::getInstance()->get('db');
        if(!$db ){
            throw new AException(
                AC_ERR_USER_ERROR,
                __FUNCTION__.': ADB class not found!'
            );
        }


        $db->query(
            "DELETE FROM ".$db->table('object_types')." 
            WHERE related_to = '".$db->escape($code)."'"
        );
        $db->query(
            "DELETE FROM ".$db->table('supplier_data')." 
            WHERE supplier_code = '".$db->escape($code)."'"
        );
        $db->query(
            "DELETE FROM ".$db->table('suppliers')." 
            WHERE code = '".$db->escape($code)."'"
        );

        return true;

    }

    /**
     * @param string $name - object name (product, category, brand etc))
     * @param string $related_to - textual code
     * @return bool
     * @throws AException
     */
    public static function saveObjectType(string $name, string $related_to)
    {
        if(IS_ADMIN !== true || !$name || !$related_to ){
            return false;
        }
        $db = Registry::getInstance()->get('db');
        if(!$db){
            throw new AException(
                AC_ERR_USER_ERROR,
                __FUNCTION__.': ADB class not found!'
            );
        }

        $db->query(
            "REPLACE INTO ".$db->table('object_types')." (related_to, name) 
            VALUES ('".$db->escape($related_to)."','".$db->escape($name)."');"
        );
        return true;
    }

}