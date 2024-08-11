<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2023 Belavier Commerce LLC

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

/**
 * Class APage
 *
 * @property ARouter $router
 * @property ALayout $layout
 */
final class APage
{
    /**
     * @var Registry
     */
    protected $registry;
    protected $pre_dispatch = [];
    protected $error;
    private $pade_id;
    private $recursion_limit = 0;

    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    public function __destruct()
    {
        $this->pade_id = '';
    }

    public function __get($key)
    {
        return $this->registry->get($key);
    }

    public function __set($key, $value)
    {
        $this->registry->set($key, $value);
    }

    public function addPreDispatch($dispatch_rt)
    {
        $this->pre_dispatch[] = new ADispatcher($dispatch_rt, ["instance_id" => "0"]);
    }

    public function build($dispatch_rt)
    {
        $dispatch = '';
        $this->recursion_limit = 0;

        foreach ($this->pre_dispatch as $pre_dispatch) {
            /**
             * @var ADispatcher $pre_dispatch
             */
            $result = $pre_dispatch->dispatch();
            //Processing has finished, Example: we have cache generated. 
            if ($result == 'completed') {
                return;
            } else {
                if ($result) {
                    //Something happened. Need to run different dispatcher
                    $dispatch_rt = $result;
                    //Rule exception for SEO_URL. DO not break with pre_dispatch for SEO_URL 
                    if ($pre_dispatch->getController() != 'common/seo_url') {
                        break;
                    }
                }
            }
        }

        //Process dispatcher in while in case we have new dispatch back
        while ($dispatch_rt && $dispatch_rt != 'completed') {
            //Process main level controller			
            // load page layout
            if ($this->layout) {
                //filter in case we have pages set already
                $dispatch_rt = preg_replace('/^(pages)\//', '', $dispatch_rt);
                $dispatch_rt = 'pages/'.$dispatch_rt;
                //get controller only part. Layout needs only controller path
                $this->pade_id = $this->layout->buildPageData(
                    $this->router->getController(),
                    $this->router->getMethod()
                );
                //add controller and a child to parent page controller
                $this->layout->addChildFirst(0, $dispatch_rt, 'content', $dispatch_rt.'.tpl');
                $dispatch_rt = "common/page";
            }
            //Do the magic
            $dispatch = new ADispatcher($dispatch_rt, ["instance_id" => "0"]);
            $dispatch_rt = $dispatch->dispatch();
        }

        unset($dispatch);
    }

}
