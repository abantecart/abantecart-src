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
	<?php echo $text_agree; ?><a class="thickbox" href="<?php echo $text_agree_href; ?>"><b><?php echo $text_agree_href_text; ?></b></a>
	<?php echo $form[ 'agree' ]; ?>
	</label>
<?php } ?>
