<?php echo $form['form_open']; ?>
  <?php echo $form['xml'] .$form['signature']; ?>

	<div class="form-group action-buttons">
	    <div class="col-md-12">
	    	<button class="btn btn-orange pull-right" title="<?php echo $form['submit']->name ?>" type="submit">
	    	    <i class="fa fa-check"></i>
	    	    <?php echo $form['submit']->name; ?>
	    	</button>
	    	<a id="<?php echo $form['back']->name ?>" href="<?php echo $form['back']->href; ?>" class="btn btn-default mr10" title="<?php echo $form['back']->text ?>">
	    	    <i class="fa fa-arrow-left"></i>
	    	    <?php echo $form['back']->text ?>
	    	</a>
	    </div>
	</div>

</form>