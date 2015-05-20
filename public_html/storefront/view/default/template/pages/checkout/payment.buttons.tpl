<a href="<?php echo $back; ?>" class="btn btn-default mr10 pull-left" title="<?php echo $form['back']->text ?>">
	<i class="fa fa-arrow-left"></i>
	<?php echo $form['back']->text ?>
</a>
<button class="btn btn-orange pull-right ml10" title="<?php echo $form['continue']->name ?>" type="submit">
	<i class="fa fa-arrow-right"></i>
	<?php echo $form['continue']->name ?>
</button>
<?php if ($text_agree) { ?>
<label class="checkbox pull-right">
	<?php echo $form['agree']; ?>
	<?php echo $text_agree; ?> <a class="mr10" href="<?php echo $text_agree_href; ?>" onclick="openModalRemote('#returnPolicyModal', '<?php echo $text_agree_href; ?>'); return false;"><b><?php echo $text_agree_href_text; ?></b></a>
</label>


<div id="returnPolicyModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="returnPolicyModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="returnPolicyModalLabel"><?php echo $text_agree_href_text; ?></h3>
	</div>
	<div class="modal-body">
	</div>
	<div class="modal-footer">
		<button class="btn default" data-dismiss="modal" aria-hidden="true"><?php echo $text_close; ?></button>
	</div>
</div>
</div>  
</div>
<?php } ?>
