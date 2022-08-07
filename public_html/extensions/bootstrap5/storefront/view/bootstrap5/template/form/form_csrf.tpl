<?php
if (!empty($csrftoken)) {
	?>
	<input type="hidden" name="csrftoken" value="<?php echo $csrftoken; ?>"/>
	<input type="hidden" name="csrfinstance" value="<?php echo $csrfinstance; ?>"/>
	<?php
}
?>