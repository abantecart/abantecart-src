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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerResponsesExtensionbannerManagerChart extends AController {
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
		$this->loadLanguage('banner_manager/banner_manager');
		
		$data = array();
		
		$data['viewed'] = array();
		$data['clicked'] = array();
		$data['xaxis'] = array();
		
		$data['viewed']['label'] = $this->language->get('column_viewed');
		$data['clicked']['label'] = $this->language->get('column_clicked');
		
		if (isset($this->request->get['range'])) {
			$range = $this->request->get['range'];
		} else {
			$range = 'month';
		}
		$banner_id = (int)$this->request->get['banner_id'];
		if(!$banner_id){ return null;}
		switch ($range) {
			case 'day':
				for ($i = 0; $i < 24; $i++) {
					$sql = "SELECT `type`, COUNT(`type`) AS cnt
							FROM " . $this->db->table("banner_stat") . " 
							WHERE (DATE(`time`) = DATE(NOW()) AND HOUR(`time`) = '" . (int)$i . "')
								AND banner_id = '".$banner_id."'
							GROUP BY `type`, HOUR(`time`)
							ORDER BY `time` ASC, `type`\n";
					$query = $this->db->query($sql);
					if($query->num_rows){
						foreach($query->rows as $row){
							$type = $row['type']=='1' ? 'viewed' : 'clicked';
							$data[$type]['data'][]  = array($i, $row['cnt']);
						}
					}else{
						$data['viewed']['data'][]  = array($i, 0);
						$data['clicked']['data'][]  = array($i, 0);
					}

					$data['xaxis'][] = array($i, date('H', mktime($i, 0, 0, date('n'), date('j'), date('Y'))));
				}
				$data['xaxisLabel'] = $this->language->get('text_hours');
				break;
			case 'week':
				$date_start = strtotime('-' . date('w') . ' days'); 
				
				for ($i = 0; $i < 7; $i++) {
					$date = date('Y-m-d', $date_start + ($i * 86400));

					$sql = "SELECT `type`, COUNT(`type`) AS cnt
							FROM " . $this->db->table("banner_stat") . " 
							WHERE DATE(`time`) = '" . $this->db->escape($date) . "'
								AND banner_id = '".$banner_id."'
							GROUP BY `type`, DATE(`time`)
							ORDER BY `type`\n";
					$query = $this->db->query($sql);
					if($query->num_rows){
						foreach($query->rows as $row){
							$type = $row['type']=='1' ? 'viewed' : 'clicked';
							$data[$type]['data'][]  = array($i, $row['cnt']);
						}
					}else{
						$data['viewed']['data'][]  = array($i, 0);
						$data['clicked']['data'][]  = array($i, 0);
					}
					$data['xaxis'][] = array($i, date('D', strtotime($date)));
				}
				$data['xaxisLabel'] = $this->language->get('text_weeks');
				break;
			default:
			case 'month':
				for ($i = 1; $i <= date('t'); $i++) {
					$date = date('Y') . '-' . date('m') . '-' . $i;

					$sql = "SELECT `type`, COUNT(`type`) AS cnt
							FROM " . $this->db->table("banner_stat") . " 
							WHERE DATE(`time`) = '" . $this->db->escape($date) . "'
								AND banner_id = '".$banner_id."'
							GROUP BY `type`, DAY(`time`)
							ORDER BY `type`\n";
					$query = $this->db->query($sql);
					if($query->num_rows){
						foreach($query->rows as $row){
							$type = $row['type']=='1' ? 'viewed' : 'clicked';
							$data[$type]['data'][]  = array($i, $row['cnt']);
						}
					}else{
						$data['viewed']['data'][]  = array($i, 0);
						$data['clicked']['data'][]  = array($i, 0);
					}
					
					$data['xaxis'][] = array($i, date('j', strtotime($date)));
				}
				$data['xaxisLabel'] = $this->language->get('text_days');
				break;
			case 'year':
				for ($i = 1; $i <= 12; $i++) {

					$sql = "SELECT `type`, COUNT(`type`) AS cnt
							FROM " . $this->db->table("banner_stat") . " 
							WHERE YEAR(`time`) = '" . date('Y') . "' AND MONTH(`time`) = '" . $i . "'
								AND banner_id = '".$banner_id."'
							GROUP BY `type`, MONTH(`time`)
							ORDER BY `type`\n";
					$query = $this->db->query($sql);

					if($query->num_rows){
						foreach($query->rows as $row){
							$type = $row['type']=='1' ? 'viewed' : 'clicked';
							$data[$type]['data'][]  = array($i, $row['cnt']);
						}
					}else{
						$data['viewed']['data'][]  = array($i, 0);
						$data['clicked']['data'][]  = array($i, 0);
					}

					$data['xaxis'][] = array($i, date('M', mktime(0, 0, 0, $i, 1, date('Y'))));
				}
				$data['xaxisLabel'] = $this->language->get('text_months');
				break;	
		}

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($data));
	}
}
