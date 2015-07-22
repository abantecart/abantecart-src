//set mark about 3d party cookie
 (function(){
	abc_cookie_allowed = <?php echo  $allowed ? 'true' : 'false'; ?>;
	abc_token_value = '<?php echo $abc_token; ?>';
	var abc_cookie_value = encodeURIComponent('<?php echo $abc_token; ?>');
	document.cookie = "abantecart_token="+abc_cookie_value;
 })();
