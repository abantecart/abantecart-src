<a href="<?php echo $back; ?>" class="btn mr10 pull-left" title="<?php echo $form['back']->text ?>">
	<i class="icon-arrow-left"></i>
	<?php echo $form['back']->text ?>
</a>
<button class="btn btn-orange pull-right ml10" title="<?php echo $form['continue']->name ?>" type="submit">
	<i class="icon-arrow-right"></i>
	<?php echo $form['continue']->name ?>
</button>
<?php if ($text_agree) { ?>
<label class="checkbox pull-right mr10">
	<?php echo $text_agree; ?><a href="<?php echo $text_agree_href; ?>" onclick="openModal(); return false;"><b><?php echo $text_agree_href_text; ?></b></a>

	<?php echo $form['agree']; ?>
</label>


<div id="returnPolicyModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="returnPolicyModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="returnPolicyModalLabel"><?php echo $text_agree_href_text; ?></h3>
	</div>
	<div class="modal-body">
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo $text_close; ?></button>
	</div>
</div>
<script type="text/javascript">
	function openModal(){
	$('#returnPolicyModal').modal({remote: '<?php echo $text_agree_href; ?>'});
	}
</script>

<?php } ?>
