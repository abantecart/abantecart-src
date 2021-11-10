<?php /** @noinspection HttpUrlsUsage */

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2021 Belavier Commerce LLC

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

class ControllerResponsesEmbedGet extends AController
{
    /**
     * NOTE: main() is boot-up method
     */
    public function main()
    {
        $_get = $this->request->get;
        // if embedding disabled or enabled maintenance mode - return empty
        if (!$this->config->get('config_embed_status') || $this->config->get('config_maintenance')) {
            http_response_code(400);
            exit;
        }

        $this->extensions->hk_InitData($this, __FUNCTION__);

        if (HTTPS === true) {
            $this->view->assign('base', HTTPS_SERVER);
        } else {
            $this->view->assign('base', HTTP_SERVER);
        }

        $this->loadModel('catalog/product');
        $this->loadLanguage($this->language->getCurrentLanguage()['directory']);
        $remote_store_url = $this->config->get('config_url').$this->config->get('seo_prefix');
        $product_stores = $this->model_catalog_product->getProductStoresInfo( $_get['product_id'] ?? 0 );

        if ($product_stores && count($product_stores) == 1) {
            $remote_store_url = $product_stores[0]['store_url'];
        }
        $remote_store_url = str_replace(['http://', 'https://'], '//', $remote_store_url);
        $this->data['sf_base_url'] = $remote_store_url;
        $this->data['sf_js_embed_url'] = $remote_store_url.INDEX_FILE.'?rt=r/embed/js';
        $this->data['sf_css_embed_url'] = $remote_store_url
            .'storefront/view/'
            .$this->config->get('config_storefront_template')
            .'/stylesheet/embed.css';

        $this->data['homepage'] = HTTPS_SERVER;
        $this->data['params'] = $_get;
        unset($this->data['params']['rt']);
        if ($this->data['params']['curr']) {
            $this->data['params']['currency'] = $this->data['params']['curr'];
        }
        if ($this->data['params']['lang']) {
            $langObj = new ALanguage($this->registry, $this->data['params']['lang']);
            if ($langObj->getLanguageDetails($this->data['params']['lang'])) {
                $this->registry->set('language', $langObj);
            }
            $this->data['params']['language'] = $this->data['params']['lang'];
            $this->data['params']['direction'] = $this->language->get('direction');
        }
        $template = '';
        if ($_get['product_id'] ?? null) {
            $template = 'embed/get_product_embed_code.tpl';
        }elseif($_get['category_id'] ?? null) {
            $template = 'embed/get_category_embed_code.tpl';
        }elseif($_get['manufacturer_id'] ?? null) {
            $template = 'embed/get_manufacturer_embed_code.tpl';
        }elseif($_get['collection_id'] ?? null) {
            $template = 'embed/get_collection_embed_code.tpl';
        }

        if($template) {
            $this->view->setTemplate($template);
            $this->view->batchAssign($this->data);
            $this->processTemplate();
        }

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function oEmbed()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $url = html_entity_decode($this->request->get['url']);
        $query = parse_url($url, PHP_URL_QUERY);

        parse_str($query, $params);
        $h = (int)($params['height'] ?? 450);

        $this->data['output'] = [
            "type"             => "rich",
            "version"          => "1.0",
            "title"            => "Embedded Abantecart Widget",
            "provider_name"    => "AbanteCart",
            "provider_url"     => "http://www.abantecart.com/",
            "provider_version" => VERSION,
            'height'           => $h,
            'width'            => '100%',
            "html"             => '<iframe src="'.$url.'" height="'.$h.'" width="100%" frameborder="0" '
                                .'scrolling="auto" marginwidth="0" marginheight="0"></iframe>',
        ];

        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->response->addJSONHeader();
        $this->response->setOutput(json_encode($this->data['output']));
    }
}
