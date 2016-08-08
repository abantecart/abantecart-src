<?php
/*
NeoWize Insights - analytics and actionable insights for eCommerce sites.
For more info: www.neowize.com
*/

if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}


/**
 * Misc utils for the NeoWize extension.
 */
class NeowizeUtils {

    // create a random guid string
    protected static function create_guid()
    {
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    // should we execute on this platform?
    public static function shouldRun() {

        // first make sure that PHP_MAJOR_VERSION and PHP_MINOR_VERSION exist. if not, the php version is too old..
        if (defined ('PHP_MAJOR_VERSION') && defined('PHP_MINOR_VERSION'))
        {
            // must be at least version 5.3
            if (PHP_MAJOR_VERSION > 5 || (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION >= 3))
            {
                return true;
            }
        }

        // if got here it means version not supported
        return false;
    }

    // get neowize config (api key, secret key, etc..
    // note: if doesn't exist this will create new api key and secret.
    public static function getConfig() {

    	// make sure neowize dataset exist
        $neowize_data = new ADataset();
        $neowize_data->createDataset('neowize','neowize_settings');

        // get neowize settings for api key and secret key
        $neowize_data = new ADataset('neowize','neowize_settings');
        $settings = $neowize_data->getDatasetProperties();

        // not set yet? create it now
        if (!isset($settings['api_key']))
        {
            // create new api key and secret key
            $settings = array('api_key' => self::create_guid(), 'secret_key' => self::create_guid());

            // create new dataset and set the new data
            $neowize_data = new ADataset('neowize','neowize_settings');
            $neowize_data->setDatasetProperties($settings);
        }

        // return settings
        return $settings;
    }

    // get the neowize block id
    public static function getNeowizeBlockId() {

        // get installation data
        $neowize_data = new ADataset('neowize','neowize_install_data');
        $data = $neowize_data->getDatasetProperties();

        // return block id
        return $data['block_id'];
    }

    // a shortcut to get just the api key.
    public static function getApiKey() {

        $config = self::getConfig();
        return $config['api_key'];
    }

    // return product main image (or empty string if product have no images or if we had an exception).
    public static function getProductImage($product_id, $config)
    {
        // get main image url
        try
        {
            // get primary image
            $resource = new AResource('image');
            $sizes = array('main'  => array('width'  => $config->get('config_image_popup_width'),
                                            'height' => $config->get('config_image_popup_height')
                                      ),
                          'thumb' => array('width'  => $config->get('config_image_thumb_width'),
                                           'height' => $config->get('config_image_thumb_height')
                                      ));
            $main_image = $resource->getResourceAllObjects('products', $product_id, $sizes, 1, false);

            // if image found return its url
            if (isset($main_image['main_url']))
            {
                return $main_image['main_url'];
            }

            // not found..
            return "";
        }
        // on exceptions return empty string
        catch (Exception $e)
        {
            return "";
        }
    }

    // report an exception to log.
	public static function reportException($from_func, $error)
	{
		try
		{
			$registry = Registry::getInstance();
			$log = $registry->get('log');
			$err_msg = 'NeoWize [' . $from_func . '] Caught exception: ' . $error->getMessage();
			$log->write($err_msg);
		}
		catch (Exception $e)
		{
			// failing to report not much to do.....
		}
	}

	// make sure neowize blocks are injected to all layouts in all templates
	// this function is used in case users change template etc and we want to make sure we are still active.
	// @param $caller should be the block / controller that called this action, with access to db and cache.
	// note: this will only write to db if we detect layouts that are missing Neowize block. So don't worry about calling
	// this function unneeded (but also don't call it from storefront because its still db access..)
	public static function reinstallNeowizeBlocks($caller)
	{
	    // get neowize block id
        $block_id = NeowizeUtils::getNeowizeBlockId();

        // get all layout ids
        $sql = "SELECT layout_id FROM " . $caller->db->table("layouts") . " ORDER BY layout_id ASC";
        $result = $caller->db->query($sql);
        $layouts = $result->rows;

        // add our block to all layouts (in 'block_layouts' table)
        foreach($layouts as $layout){

            // get layout id
            $layout_id = $layout['layout_id'];

            // set default position
            $position = 10;

            // get parent instance id for this layout - the root block that has no parent_instance_id is the instance_id we take as our own parent.
            $sql = "SELECT instance_id FROM " . $caller->db->table("block_layouts") . " WHERE layout_id='" . (int)$layout_id . "' AND parent_instance_id='0'";
            $result = $caller->db->query($sql);
            $parent_instance_id = $result->rows[0]['instance_id'];

            // first, make sure not already exist
            $sql = "SELECT count(1) FROM " . $caller->db->table("block_layouts") . " WHERE " .
                                                                                "layout_id='" . $layout_id . "' AND " .
                                                                                "block_id='" . $block_id . "' AND " .
                                                                                "parent_instance_id='" . $parent_instance_id . "'";
            $result = $caller->db->query($sql);
            $count = ( int )$result->rows[0]['count(1)'];
            if ($count > 0)
            {
                continue;
            }

            // then insert block into placeholder
            $sql = "INSERT INTO " . $caller->db->table("block_layouts") . " (" .
                                                 "layout_id, " .
                                                 "block_id, " .
                                                 "parent_instance_id, " .
                                                 "position, " .
                                                 "status, " .
                                                 "date_added, " .
                                                 "date_modified) " .
                "VALUES ('" . ( int )$layout_id . "', " .
                        "'" . ( int )$block_id . "', " .
                        "'" . ( int )$parent_instance_id . "', " .
                        "'" . ( int )$position . "', " .
                        "'1', " .
                      "NOW(), " .
                      "NOW()) ";
            $caller->db->query($sql);
        }

        // clear layouts cache
        if (isset($caller->cache->remove))
        {
            $caller->cache->remove('layout');
        }
	}
}