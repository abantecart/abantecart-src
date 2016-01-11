<?php 
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
class ControllerResponsesCommonResource extends AController {
	    
  	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->getImageThumbnail();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

    public function getImageThumbnail(){

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

	    $r = new AResource('image');
        $result = $r->getResource($this->request->get['resource_id']);


	    if(!$result){
		    $error = new AError('Resource with ID #'.$this->request->get['resource_id'].' not found!');
		    $error->toLog();
		    header('HTTP/1.1 404 Not Found');
		    exit;
	    }

        if ($result) {
            if (!empty($result['resource_code']) || $result['type_name'] != 'image' ) {
	            $error = new AError('Requested resource with ID #'.$this->request->get['resource_id'].' is not image!');
                $error->toLog();
                header('HTTP/1.1 406 Not Acceptable');
                exit;
            }


            $file_path = $result['resource_path'];
            if(!is_file(DIR_RESOURCE . $r->getTypeDir() . $file_path)){
	            $error = new AError('File '.DIR_RESOURCE . $r->getTypeDir() . $file_path.' of resource with ID #'.$this->request->get['resource_id'].' not found on disk!');
                $error->toLog();
                header('HTTP/1.1 404 Not Found');
                exit;
            }
            //if format of file is ICO - do not resize it.
            if($this->request->get['width'] && pathinfo($result['resource_path'], PATHINFO_EXTENSION)!='ico'){
                $width = (int)$this->request->get['width'];
                $height = (int)$this->request->get['height'];
                $this->load->model('tool/image');
                $file_path = $this->model_tool_image->resize($file_path, $width, $height, $result['name'], 'path');
            }else{
                $file_path = DIR_RESOURCE . $r->getTypeDir() . $file_path;
            }

            if (file_exists($file_path) && ($fd = fopen($file_path, "r"))) {
                $fsize = filesize($file_path);
                header('Content-Type: '.getMimeType($file_path));
                header('Content-Disposition: filename="' . $result['name'] . '"');
                header('Content-Transfer-Encoding: binary');
                header('Cache-Control: public');
	            header( "Last-Modified: ".gmdate("D, d M Y H:i:s", filemtime($file_path))." GMT");
	            header( "Expires: ". date("r",time() + (60*60*24)));
	            header( "Pragma: public" );
                header('Content-Length: '.$fsize);
                ob_end_clean();
                flush();
                readfile($file_path);
                exit;

            }else{
                $this->response->setOutput('Resource not found!');
            }

        }
    }
}