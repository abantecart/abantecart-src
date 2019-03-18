<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" xml:lang="<?php echo $lang; ?>" <?php echo $this->getHookVar('hk_html_attribute'); ?>>
<head><?php	echo $head; ?></head>
<body>
<div class="container-fixed">
	<div class="contentpanel" style="margin-top: 5%">
		<h3 class=" col-sm-5 col-sm-offset-4"><i class="fa fa-spinner fa-pulse"></i> <?php echo $text_message; ?></h3>
	</div>
</div>
	<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/bootstrap.min.js'); ?>" defer></script>
	<script language="JavaScript" type="application/javascript">
		var timeout = 30;
		(function worker() {
			if(timeout <=0){
				location = '<?php echo $success_url;?>';
				return;
			}
			timeout -=5;
			$.ajax({
				url: '<?php echo $test_url;?>',
				success: function (data) {
					console.log(data);
					if(data['result'] == true) {
						$('h3').html('..redirecting');
						location = '<?php echo $success_url;?>';
						return false;
					}
				},
				complete: function () {
					// Schedule the next request when the current one's complete
					setTimeout(worker, 5000);
				}
			});
		})();
	</script>

</body></html>