<h1 class="heading1">
  <span class="maintext"><i class="icon-frown"></i> <?php echo $heading_title; ?></span>
  <span class="subtext"></span>
</h1>

<div class="container-fluid">

	<?php echo $text_error; ?>
	
	<div class="control-group">
	    <div class="controls">
	    	<div class="span4 mt20 mb20">
	    		<?php 
	    			if (empty($continue)){
	    				$continue = $button_continue->href;
	    			} 
	    		?>	    	
	    		<a href="<?php echo $continue; ?>" class="btn mr10" title="<?php echo $button_continue->text ?>">
	    		    <i class="icon-arrow-right"></i>
	    		    <?php echo $button_continue->text ?>
	    		</a>
	    	</div>	
	    </div>
	</div>

</div>