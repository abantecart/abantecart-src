//set mark about 3d party cookie
var abcDo = function(){
	abc_cookie_allowed = <?php echo  $allowed ? 'true' : 'false'; ?>;
	abc_token_value = '<?php echo $abc_token; ?>'
	//alert('3dParty Cookie allowed? ' + abc_cookie_allowed);
}
