<h1 class="heading1">
  <span class="maintext"><i class="fa fa-frown"></i> <?php echo $heading_title; ?></span>
  <span class="subtext"></span>
</h1>

<div class="contentpanel">

	<?php echo $text_error; ?>
	
	<div class="container-fluid">
	    	<div class="col-md-4 mt20 mb20">
	    		<?php 
	    			if (empty($continue)){
	    				$continue = $button_continue->href;
	    			} 
	    		?>	    	
	    		<a href="<?php echo $continue; ?>" class="btn btn-default mr10" title="<?php echo $button_continue->text ?>">
	    		    <i class="fa fa-arrow-right"></i>
	    		    <?php echo $button_continue->text ?>
	    		</a>
	    	</div>	
	</div>

</div>