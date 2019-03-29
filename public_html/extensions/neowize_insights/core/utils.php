<?php
/*
NeoWize Insights - analytics and actionable insights for eCommerce sites.
For more info: www.neowize.com
*/

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * Misc utils for the NeoWize extension.
 */
class NeowizeUtils
{

    // create a random guid string
    protected static function create_guid()
    {
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    // should we execute on this platform?
    public static function shouldRun()
    {

        // first make sure that PHP_MAJOR_VERSION and PHP_MINOR_VERSION exist. if not, the php version is too old..
        if (defined('PHP_MAJOR_VERSION') && defined('PHP_MINOR_VERSION')) {
            // must be at least version 5.6
            if (PHP_MAJOR_VERSION > 5 || (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION >= 3)) {
                return true;
            }
        }

        // if got here it means version not supported
        return false;
    }

    // get neowize config (api key, secret key, etc..
    // note: if doesn't exist this will create new api key and secret.
    public static function getConfig()
    {

        // make sure neowize dataset exist
        $neowize_data = new ADataset();
        $neowize_data->createDataset('neowize', 'neowize_settings');

        // get neowize settings for api key and secret key
        $neowize_data = new ADataset('neowize', 'neowize_settings');
        $settings = $neowize_data->getDatasetProperties();

        // not set yet? create it now
        if (!isset($settings['api_key'])) {
            // create new api key and secret key
            $settings = array('api_key' => self::create_guid(), 'secret_key' => self::create_guid());

            // create new dataset and set the new data
            $neowize_data = new ADataset('neowize', 'neowize_settings');
            $neowize_data->setDatasetProperties($settings);
        }

        // return settings
        return $settings;
    }

    // a shortcut to get just the api key.
    public static function getApiKey()
    {

        $config = self::getConfig();
        return $config['api_key'];
    }

    // return product main image (or empty string if product have no images or if we had an exception).
    public static function getProductImage($product_id, $config)
    {
        // get main image url
        try {
            // get primary image
            $resource = new AResource('image');
            $sizes = array(
                'main'  => array(
                    'width'  => $config->get('config_image_popup_width'),
                    'height' => $config->get('config_image_popup_height'),
                ),
                'thumb' => array(
                    'width'  => $config->get('config_image_thumb_width'),
                    'height' => $config->get('config_image_thumb_height'),
                ),
            );
            $main_image = $resource->getResourceAllObjects('products', $product_id, $sizes, 1, false);

            // if image found return its url
            if (isset($main_image['main_url'])) {
                return $main_image['main_url'];
            }

            // not found..
            return "";
        } // on exceptions return empty string
        catch (Exception $e) {
            return "";
        }
    }

    // report an exception to log.
    public static function reportException($from_func, $error)
    {
        try {
            $registry = Registry::getInstance();
            $log = $registry->get('log');
            $err_msg = 'NeoWize ['.$from_func.'] Caught exception: '.$error->getMessage();
            $log->write($err_msg);
        } catch (Exception $e) {
            // failing to report not much to do.....
        }
    }

}