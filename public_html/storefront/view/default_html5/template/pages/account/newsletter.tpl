<h1 class="heading1">
  <span class="maintext"><i class="icon-bullhorn"></i> <?php echo $heading_title; ?></span>
  <span class="subtext"></span>
</h1>

<div class="container-fluid">
	<?php echo $form['form_open']; ?>
	
	<div class="registerbox form-horizontal">
		<fieldset>
			<div class="control-group">
				<label class="control-label"><?php echo $entry_newsletter; ?></label>
				<div class="controls">
				    <?php echo $form['newsletter']; ?>
				</div>
			</div>		
		</fieldset>
	</div>

	<?php echo $this->getHookVar('newsletter_edit_sections'); ?>
	
	<div class="control-group">
	    <div class="controls">
	    	<div class="span4 mt20 mb20">
	    		<button class="btn btn-orange pull-right" title="<?php echo $form['continue']->name ?>" type="submit">
	    		    <i class="<?php echo $form['continue']->{'icon'}; ?> icon-white"></i>
	    		    <?php echo $form['continue']->name ?>
	    		</button>
	    		<a href="<?php echo $back; ?>" class="btn mr10" title="<?php echo $form['back']->text ?>">
	    		    <i class="<?php echo $form['back']->{'icon'}; ?>"></i>
	    		    <?php echo $form['back']->text ?>
	    		</a>
	    	</div>	
	    </div>
	</div>
	</form>
</div>