<?php
class ControllerResponsesExtensionBackToStock extends AController
{
    public function main(){
        $this->extensions->hk_InitData($this, __FUNCTION__);
        if($this->request->is_POST()){
            if($this->customer->isLogged()) {
                $request = $this->request->get;
                $user_id = $this->customer->getId();
                /** @var ModelExtensionBackToStock $bts */
                $bts = $this->loadModel('extension/back_to_stock');
             $queue = $bts->addToQueue($user_id,(int)$request['product_id']);
             if($queue == true){
                 $this->log->write('isqueue');
                    $this->load->library('json');
                 $this->response->setOutput(AJson::encode('Success'));//add array success
             }else{
                 $error = new AError('');

                 $error->toJSONResponse(
                     'NO_PERMISSIONS_402',
                     [
                         'error_text'  => sprintf($this->language->get('error_permission_modify'), 'listing_grid/product'),
                         'reset_value' => true,
                     ]
                 );
             }
            }
        }
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}