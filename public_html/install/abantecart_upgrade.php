<?php
/**
 * @var $this AController
 */
// do account page as separate layout for html5 core template
$layout_manager = new ALayoutManager('default_html5');
$layout_manager->loadXML(array('file' => pathinfo(PHP_SELF,PATHINFO_DIRNAME).'/upgrade_layout.xml'));
// bugfix of column meanings
$balances = $this->db->query("SELECT customer_id, debit, credit FROM ".$this->db->table('customer_transactions'));
foreach($balances->rows as $row){
	$sql = "UPDATE ".$this->db->table('customer_transactions')."
			SET credit = ".$row['debit'].",
				debit = ".$row['credit']."
			WHERE customer_id='".$row['customer_id']."';";
	$this->db->query($sql,true); // do safe update
}
