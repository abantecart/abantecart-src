<?php echo $form['form_open']; ?>
<?php 
	foreach($form['fields'] as $field => $value) { 
		echo $value;
	}
?>
<div class="form-group action-buttons">
    <div class="col-md-12">
	    <a id="<?php echo $form['back']->text ?>" href="<?php echo $back; ?>" class="btn btn-default" title="<?php echo $form['back']->text ?>">
	    	<i class="fa fa-arrow-left"></i>
	    	<?php echo $form['back']->text ?>
	    </a>
	    <button id="<?php echo $form['submit']->name; ?>" class="btn btn-orange lock-on-click pull-right" title="<?php echo $form['submit']->name; ?>" type="submit">
	        <i class="fa fa-check"></i>
	        <?php echo $form['submit']->name; ?>
	    </button>
    </div>
</div>
</form>