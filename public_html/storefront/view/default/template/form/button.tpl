<a id="<?php echo $id; ?>" class="btn_standard">
	<span <?php echo ($style ? 'class="'.$style.'"':''); ?> title="<?php echo $text ?>" <?php echo $attr ?>>
		<span><?php echo $text ?></span>
	</span>
</a>
<?php
if($href){ ?>
<script type="text/javascript">
	$('#<?php echo $id;?>').click(function() { location = '<?php echo $href; ?>'; });
</script>
<?php } ?>