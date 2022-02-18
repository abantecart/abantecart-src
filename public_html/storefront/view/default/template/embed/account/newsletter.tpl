<?php echo $head; ?>
<h1 class="heading1">
  <span class="maintext"><i class="fa fa-bullhorn"></i> <?php echo $heading_title; ?></span>
  <span class="subtext"></span>
</h1>

<div class="contentpanel">
	<?php echo $form['form_open']; ?>
	
	<div class="registerbox form-horizontal">
		<fieldset>
			<div class="form-group">
				<label class="control-label col-sm-4"><?php echo $entry_newsletter; ?></label>
				<div class="input-group col-sm-4">
				    <?php echo $form['newsletter']; ?>
				</div>
			</div>		
		</fieldset>
	</div>

	<?php echo $this->getHookVar('newsletter_edit_sections'); ?>
	
	<div class="form-group">
	    <div class="col-md-12">
	    	<button class="btn btn-orange pull-right" title="<?php echo $form['continue']->name ?>" type="submit">
	    	    <i class="<?php echo $form['continue']->{'icon'}; ?> fa"></i>
	    	    <?php echo $form['continue']->name ?>
	    	</button>
	    	<a href="<?php echo $back; ?>" class="btn btn-default mr10" title="<?php echo $form['back']->text ?>">
	    	    <i class="<?php echo $form['back']->{'icon'}; ?>"></i>
	    	    <?php echo $form['back']->text ?>
	    	</a>
	    </div>
	</div>
	</form>
</div>
<?php echo $footer; ?>