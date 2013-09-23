<?php
/**
 * @var $this APackageManager
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
if (!$this->session->data['package_info']['ftp']) {
	chmod(DIR_ROOT.'/index.php',0755);
} else {
	$ftp_user = $this->session->data['package_info']['ftp_user'];
	$ftp_password = $this->session->data['package_info']['ftp_password'];
	$ftp_port = $this->session->data['package_info']['ftp_port'];
	$ftp_host = $this->session->data['package_info']['ftp_host'];

	$fconnect = ftp_connect($ftp_host, $ftp_port);
	ftp_login($fconnect, $ftp_user, $ftp_password);
	ftp_pasv($fconnect, true);

	$index_file = $this->_ftp_find_app_root($fconnect).'index.php';
	ftp_chmod($fconnect,0755,$index_file);
	ftp_close($fconnect);
}
