<?php echo $form['form_open']; ?>
<?php //NOTE: do not use enctype="multipart/form-data" for form tag here!
foreach($form['fields'] as $field){
	echo $field;
}
?>
<div class="form-group action-buttons">
	<div class="col-md-12">
		<button class="btn btn-orange pull-right lock-on-click" title="<?php echo $form['submit']->name ?>" type="submit">
			<i class="fa fa-check"></i>
			<?php echo $form['submit']->name; ?>
		</button>
		<a id="<?php echo $form['back']->name ?>" href="<?php echo $form['back']->href; ?>" class="btn btn-default mr10"
		   title="<?php echo $form['back']->text ?>">
			<i class="fa fa-arrow-left"></i>
			<?php echo $form['back']->text ?>
		</a>
	</div>
</div>

</form>