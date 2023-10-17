<?php


if (! defined ( 'DIR_CORE' )) {
 header ( 'Location: static_pages/' );
}

class ExtensionBackToStockNotification extends Extension {
 public function onControllerPagesProductProduct_UpdateData(){
     //test is enable
     /** @var ControllerPagesProductProduct  $that */
     $that = $this->baseObject;
     if($that->customer->isLogged())
     {
         $request = $that->request->get;
         $key = [];
         if (has_value($request['key'])) {
             $key = explode(':', $request['key']);
             $product_id = (int) $key[0];
         } elseif (has_value($request['product_id'])) {
             $product_id = (int) $request['product_id'];
         } else {
             $product_id = 0;
         }
         /** @var ModelExtensionBackToStock $bts */
         $bts =$that->load->model('extension/back_to_stock');
         $user_id =$that->customer->getId();
         $queue = $bts->getQueueByUserProductId($user_id,$product_id);
         if(!$queue){
         if(!$that->data['can_buy']) {
             $btn = $that->html->buildElement([
                 'type' => 'button',
                 'text' => 'Notify me',
                 'id' => 'bts',
                 'href' => $that->html->getSecureURL(''),
                 'title' => 'Notify me',
                 'style' => 'btn-outline-secondary',
                 'icon' => 'fa fa-bullhorn'
             ]);
             $that->view->addHookVar('buttons', $btn);
         }
         }else{
             $btn = $that->html->buildElement([
                 'type' => 'button',
                 'text' => 'Wait for notification',
                 'title' => 'Wait for notification',
                 'style' => 'btn-outline-secondary',
                 'icon' => 'fa fa-bullhorn'
             ]);
             $that->view->addHookVar('buttons', $btn);
         }



     }
 }

 public function onControllerPagesCatalogProduct_UpdateData(){
     $that = $this->baseObject;
         if($that->data['quantity']>0){
             $request = $that->request->get;
             $key = [];
             if (has_value($request['key'])) {
                 $key = explode(':', $request['key']);
                 $product_id = (int) $key[0];
             } elseif (has_value($request['product_id'])) {
                 $product_id = (int) $request['product_id'];
             } else {
                 $product_id = 0;
             }
             /** @var ModelExtensionBackToStock $bts */
             $bts =$that->load->model('extension/back_to_stock');
             $queues = $bts->getQueueByProdId($product_id);
             if($queues) {
                 foreach ($queues as $queue) {
                     if ($queue['user_id']) {
                         /** @var ModelUserUser $user */
                         $userMd = $that->load->model('user/user');
                         $user = $userMd->getUserById($queue['user_id']);
                         if ($user) {
                             $subject = $that->config->get('store_name')
                                 .' '
                                 .sprintf(
                                     $that->language->get('email_subject'),
                                     strip_tags($user['first_name'])
                                 );
                             $data = $subject;
                             $send = $bts->_send_email($user['email'],$product_id,$data);
                             if($send == true){
                                $bts->deleteQueueByUserId($queue['user_id'],$product_id);
                             }
                         }
                     }
                 }
             }
         }

 }
}
