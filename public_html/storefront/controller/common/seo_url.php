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

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        if (isset($this->request->get['_route_'])) {
            $parts = explode('/', $this->request->get['_route_']);
            //Possible area for improvement. Only need to check last node in the path
            foreach ($parts as $part) {
                $query = $this->db->query("SELECT query
											FROM ".$this->db->table('url_aliases')."
											WHERE keyword = '".$this->db->escape($part)."'");
                //Add caching of the result.
                if ($query->num_rows) {
                    //Note: query is a field containing area=id to identify location
                    $url = explode('=', $query->row['query']);

                    if ($url[0] == 'product_id') {
                        $this->request->get['product_id'] = $url[1];
                    }

                    if ($url[0] == 'category_id') {
                        if (!isset($this->request->get['path'])) {
                            $this->request->get['path'] = $url[1];
                        } else {
                            $this->request->get['path'] .= '_'.$url[1];
                        }
                    }

                    if ($url[0] == 'manufacturer_id') {
                        $this->request->get['manufacturer_id'] = $url[1];
                    }

                    if ($url[0] == 'content_id') {
                        $this->request->get['content_id'] = $url[1];
                    }

                    if ($url[0] == 'collection_id') {
                        $this->request->get['collection_id'] = $url[1];
                    }

                    if ($url[0] == 'check_seo') {
                        $this->request->get['check_seo'] = $url[1];
                    }
                } else {
                    $this->request->get['rt'] = 'pages/error/not_found';
                }
            }

            if (isset($this->request->get['product_id'])) {
                $this->request->get['rt'] = 'pages/product/product';
            } elseif (isset($this->request->get['path'])) {
                $this->request->get['rt'] = 'pages/product/category';
            } elseif (isset($this->request->get['manufacturer_id'])) {
                $this->request->get['rt'] = 'pages/product/manufacturer';
            } elseif (isset($this->request->get['content_id'])) {
                $this->request->get['rt'] = 'pages/content/content';
            } elseif (isset($this->request->get['collection_id'])) {
                $this->request->get['rt'] = 'pages/product/collection';
            } elseif (isset($this->request->get['check_seo'])) {
                $this->request->get['rt'] = 'pages/index/check_seo';
            }
            $this->extensions->hk_ProcessData($this, 'seo_url');
            if (isset($this->request->get['rt'])) {
                //build canonical seo-url
                if (sizeof($parts) > 1) {
                    $this->_add_canonical_url('url', (HTTPS === true ? HTTPS_SERVER : HTTP_SERVER).end($parts));
                }

                $rt = $this->request->get['rt'];
                //remove pages prefix from rt for use in new generated urls
                if (substr($this->request->get['rt'], 0, 6) == 'pages/') {
                    $this->request->get['rt'] = substr($this->request->get['rt'], 6);
                }
                unset($this->request->get['_route_']);
                $this->_add_canonical_url('seo');
                //Update router with new RT
                $this->router->resetController($rt);
                return $this->dispatch($rt, $this->request->get);
            }
        } else {
            $this->_add_canonical_url('seo');
        }

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function _add_canonical_url($mode = 'seo', $url = '')
    {
        if ($this->is_set_canonical || !$this->config->get('enable_seo_url')) {
            return false;
        }
        if (!$url) {
            $method = $mode == 'seo' ? 'getSecureSEOURL' : 'getSecureURL';
            $get = $this->request->get;
            if (isset($get['product_id'])) {
                $url = $this->html->{$method}('product/product', '&product_id='.$get['product_id']);
            } elseif (isset($get['path'])) {
                $url = $this->html->{$method}('product/category', '&path='.$get['path']);
            } elseif (isset($get['manufacturer_id'])) {
                $url = $this->html->{$method}('product/manufacturer', '&manufacturer_id='.$get['manufacturer_id']);
            } elseif (isset($get['content_id'])) {
                $url = $this->html->{$method}('content/content', '&content_id='.$get['content_id']);
            } elseif (isset($get['collection_id'])) {
                $url = $this->html->{$method}('product/collection', '&collection_id='.$get['collection_id']);
            }
        }

        if ($url) {
            $this->document->addLink(
                array(
                    'rel'  => 'canonical',
                    'href' => $url,
                )
            );
            $this->is_set_canonical = true;
            return true;
        }
        return false;
    }
}
