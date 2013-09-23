<?php
/**
 * @var $this AController
 */
// do account page as separate layout for html5 core template
$file = $package_tmpdir . $package_dirname . '/upgrade_layout.xml';
$layout = new ALayoutManager();
$layout->loadXml(array('file' => $file));
// bugfix of column names meanings
$balances = $this->db->query("SELECT customer_transaction_id, debit, credit FROM ".$this->db->table('customer_transactions'));
foreach($balances->rows as $row){
	$sql = "UPDATE ".$this->db->table('customer_transactions')."
			SET credit = ".$row['debit'].",
				debit = ".$row['credit']."
			WHERE customer_transaction_id='".$row['customer_transaction_id']."';";
	$this->db->query($sql,true); // do safe update
}

// clear text definitions for admin
$this->db->query("DELETE FROM ".$this->db->table('language_definitions')." WHERE `section` = 1");

chmod(DIR_ROOT.'/index.php',0755);
