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

class ControllerCommonSeoUrl extends AController
{
    protected $is_set_canonical = false;

    public $coreRoutes = [
        'product_id'      => 'pages/product/product',
        'path'            => 'pages/product/category',
        'manufacturer_id' => 'pages/product/manufacturer',
        'content_id'      => 'pages/content/content',
        'collection_id'   => 'pages/product/collection',
        'check_seo'       => 'pages/index/check_seo'
    ];

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        if (isset($this->request->get['_route_'])) {
            $parts = explode('/', $this->request->get['_route_']);
            $result = $this->db->query(
                "SELECT query, keyword
                 FROM " . $this->db->table('url_aliases') . "
                 WHERE keyword IN ('" . implode("','", array_map([$this->db, 'escape'], $parts)) . "')"
            );
            $seoQueries = array_column($result->rows, 'query', 'keyword');


            //Possible area for improvement. Only need to check last node in the path
            foreach ($parts as $part) {
                if ($seoQueries[$part]) {
                    //Note: query is a field containing area=id to identify location
                    parse_str($seoQueries[$part], $httpQuery);
                    $keys = $this->coreRoutes;
                    unset($keys['path']);
                    $keys = array_keys($keys);
                    foreach($keys as $paramName)
                    {
                        if ( isset($httpQuery[$paramName]) ) {
                            $this->request->get[$paramName] = $httpQuery[$paramName];
                            unset($httpQuery[$paramName]);
                        }
                    }

                    if ( isset($httpQuery['category_id']) ) {
                        /** @var ModelCatalogCategory $mdl */
                        $mdl = $this->loadModel('catalog/category');
                        if (!isset($this->request->get['path'])) {
                            $this->request->get['path'] = $mdl->buildPath($httpQuery['category_id']);
                        } else {
                            $this->request->get['path'] .= '_'.$httpQuery['category_id'];
                        }
                    }
                    // case for manually added pages
                    if (isset($httpQuery['rt'])) {
                        $this->request->get['rt'] = $httpQuery['rt'];
                        unset($httpQuery['rt']);
                        if(count($httpQuery)>1){
                            foreach($httpQuery as $n=>$v){
                                if(!isset($this->request->get[$n])){
                                    $this->request->get[$n] = $v;
                                }
                            }
                        }
                    }
                } else {
                    $this->request->get['rt'] = 'pages/error/not_found';
                }
            }

            foreach($this->coreRoutes as $key => $rt){
                if (isset($this->request->get[$key])) {
                    if($key == 'path' && isset($this->request->get['product_id'])) {
                        continue;
                    }
                    $this->request->get['rt'] = $rt;
                }
            }

            $this->extensions->hk_ProcessData($this, 'seo_url');
            if (isset($this->request->get['rt'])) {
                //build canonical seo-url
                if (sizeof($parts) > 1) {
                    end($parts);
                    $this->_add_canonical_url(
                        'url',
                        (HTTPS === true ? HTTPS_SERVER : HTTP_SERVER) . $parts[array_key_last($parts)]
                    );
                }

                $rt = $this->request->get['rt'];
                //remove pages prefix from rt for use in new generated urls
                if (substr($this->request->get['rt'], 0, 6) == 'pages/') {
                    $this->request->get['rt'] = substr($this->request->get['rt'], 6);
                }
                unset($this->request->get['_route_']);
                $this->_add_canonical_url();
                //Update router with new RT
                $this->router->resetController($rt);
                return $this->dispatch($rt, $this->request->get);
            }
        } else {
            $this->_add_canonical_url();
        }

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function _add_canonical_url(?string $mode = 'seo', ?string $url = '')
    {
        if ($this->is_set_canonical || !$this->config->get('enable_seo_url')) {
            return false;
        }
        if (!$url) {
            $method = $mode == 'seo' ? 'getSecureSEOURL' : 'getSecureURL';
            $get = $this->request->get;
            foreach($this->coreRoutes as $key => $rt){
                if (isset($get[$key])) {
                    $impactRt = str_replace("pages/","",$rt);
                    $httpQuery = [$key => $get[$key]];
                    //double seo keyword for content pages
                    if ($mode == 'seo' && $key == 'content_id') {
                        $httpQuery['parent_id'] = $this->getContentParentId($get[$key]);
                    }
                    $url = $this->html->{$method}($impactRt, '&' . http_build_query($httpQuery));
                    break;
                }
            }
        }

        if ($url) {
            $this->document->addLink(['rel' => 'canonical', 'href' => $url]);
            $this->is_set_canonical = true;
            return true;
        }
        return false;
    }

    protected function getContentParentId(int $contentId)
    {
        /** @var ModelCatalogContent $mdl */
        $mdl = $this->loadModel('catalog/content');
        $cntInfo = $mdl->getContent($contentId);
        if ($cntInfo['parent_content_id']) {
            return (int)$cntInfo['parent_content_id'];
        }
        return false;
    }
}
