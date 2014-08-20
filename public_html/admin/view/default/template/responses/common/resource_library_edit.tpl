<div id="rl_container">
	<ul class="nav nav-tabs nav-justified nav-profile">
	<li class="active" id="resource" data-id="<?php echo $resource_id; ?>" data-type="<?php echo $type; ?>"><a class="widthM400 ellipsis" href="#"><strong><?php echo $resource['name']; ?></strong></a></li>
<?php if(has_value($object_id)) { ?>	
	<li id="object" data-id="<?php echo $resource_id; ?>" data-type="<?php echo $type; ?>"><a class="widthM400 ellipsis" href="#"><strong><?php echo "$object_title ($object_name)"; ?></strong></a></li>
<?php } ?>	
	<li id="library" data-id="<?php echo $resource_id; ?>" data-type="<?php echo $type; ?>"><a class="widthM400 ellipsis" href="#"><span><?php echo $heading_title; ?></span></a></li>
	</ul>

<?php 
	$txt_link_resource = "Link to " . $object_title;
	$txt_unlink_resource = "Unlink from " . $object_title;
?>

 <div class="tab-content rl-content">

	<ul class="reslibrary-options">
        <li>
          <a id="add_resource" class="btn btn-xs btn-default add_resource tooltips" data-original-title="<?php echo $button_add; ?>"><i class="fa fa-plus"></i></a>
        </li>
        <li>
          <a class="itemopt rl_download" href="" onclick="false;"><i class="fa fa-download"></i></a>
        </li>
        <?php if ( $resource['mapped_to_current'] ) { ?>
        <li>
		  <a class="itemopt rl_unlink tooltips" onclick="false;" href="" data-original-title="<?php echo $txt_unlink_resource; ?>">
		      <i class="fa fa-unlink"></i>
		  </a>
        </li>    
        <?php } else if(has_value($object_id)) { ?>
        <li>
		  <a class="itemopt rl_link tooltips" onclick="false;" href="" data-original-title="<?php echo $txt_link_resource; ?>">
		      <i class="fa fa-link"></i>	
		  </a>
        </li>    
        <?php } ?>        
        <li>
          <a class="itemopt rl_delete" href="" onclick="false;" data-confirmation="delete"><i class="fa fa-trash-o"></i></a>
        </li>
        <?php if( $form_language_switch ) { ?>
        <li>
			    <?php echo $form_language_switch; ?>
        </li>    
        <?php } ?>
        <?php if (!empty ($help_url)) { ?>
        <li>
			<a class="btn btn-white btn-xs tooltips" href="<?php echo $help_url; ?>" target="new" title="" data-original-title="Help">
			<i class="fa fa-question-circle"></i>
			</a>
        </li>    
        <?php } ?>
        
	</ul>

	<?php echo $edit_form_open;?>
	<div class="row">
        <div class="col-sm-6 col-xs-12 form-horizontal form-bordered">
			<?php if (!empty ($resource['resource_code'])) { ?>
			<div class="form-group <?php echo (!empty($error['resource_code']) ? "has-error" : ""); ?>">
			    <label class="control-label" for="'.$field->element_id.'"><?php echo $text_resource_code; ?></label>
			    <div class="input-group afield col-sm-12">
			    	<?php echo $field_resource_code;?>
			    </div>
		    </div>
        	<?php } else { ?>			
		    <div class="resource_image center">
		    <a target="_preview" href="<?php echo $rl_get_preview; ?>&resource_id=<?php echo $resource['resource_id']; ?>&language_id=<?php echo $resource['language_id']; ?>" title="<?php echo $text_preview; ?>">
		    	<img src="<?php echo $resource['thumbnail_url']; ?>" title="<?php echo $resource['title']; ?>"/>
		    </a>
		    </div>
			<div class="form-group">
			    <div class="col-sm-12">
			    	<button class="btn btn-primary btn-block">Replace File</button>
			    </div>			    
			</div>			    
        	<?php } ?>		 
        	  				
			<div class="form-group">
			    <label class="col-sm-6 control-label"><?php echo $text_mapped_to; ?></label>
			    <div class="col-sm-3">
			    	<div class="btn-group maped_resources">
			    <?php 
			    if (is_array($resource['resource_objects'])) {
			    	$total_cnt = 0;
			    ?>
			    	<div class="dropdown-menu dropdown-menu-sm pull-right">
			    <?php		
			    	foreach ( $resource['resource_objects'] as $obj_area => $items ) {
			    ?>		
			    		<h5 class="title"><?php echo $obj_area; ?></h5>
			    		<ul class="dropdown-list dropdown-list-sm">
			    <?php							
			    		foreach ( $items as $item) {
			    			$total_cnt++;
			    ?>		
			    		<li>
			    			<a href="<?php echo $item['url']; ?>" target="_new" data-object-id="<?php echo $item['object_id']; ?>">
			    			<?php echo $item['name']; ?>
			    			</a>
			    		</li>								

			    <?php														
			    		}	
			    ?>		
			    		</ul>
			    <?php																											
			    	}
			    ?>
			    	</div>	
			    <?php																											
			    }
			    ?>
			    	<button data-toggle="dropdown" class="btn btn-default btn-xs dropdown-toggle" href="">
			    		<i class="fa fa-external-link fa-lg"></i>&nbsp;
			    		<span class="caret"></span>&nbsp;
			    		<span class="badge"><?php echo $total_cnt; ?></span>&nbsp;
			    	</button>
			    	</div>
			    </div>
			    
			    <?php if ( $resource['mapped_to_current'] ) { ?>
			    <div class="col-sm-3">						
		        	<a class="btn btn-default btn-xs rl_unlink tooltips" data-original-title="<?php echo $txt_unlink_resource; ?>">
		        	    <i class="fa fa-unlink fa-lg"></i>
			    	</a>
			    </div>
			    <?php } else if(has_value($object_id)) {  ?>	
			    <div class="col-sm-3">						
		        	<a class="btn btn-default btn-xs rl_link tooltips" data-original-title="<?php echo $txt_link_resource; ?>">
		        	    <i class="fa fa-link fa-lg"></i>	
			    	</a>
			    </div>	
			    <?php } ?>	
			    
			</div>
			    							
        </div><!-- col-sm-6 -->

        <div class="col-sm-6 col-xs-12">
			<h3 class="panel-title"><?php echo $text_edit_resource ?></h3>

			<?php if ($mode == 'new') { ?>
			<div class="form-group">
				<div class="input-group afield col-sm-12"> 
				<?php echo $rl_types; ?>
				</div>
			</div>         	
			<?php } else { ?>
				<?php echo $field_resource_id; ?>
				<?php echo $field_type; ?>
			<?php } ?>
			        
			<div class="form-group <?php echo (!empty($error['name']) ? "has-error" : ""); ?>">
			    <label class="control-label" for="'.$field->element_id.'"><?php echo $text_name; ?></label>
			    <div class="input-group afield col-sm-12">
			    	<?php echo $field_name;?>
			    </div>
			</div>

			<div class="form-group <?php echo (!empty($error['title']) ? "has-error" : ""); ?>">
			    <label class="control-label" for="'.$field->element_id.'"><?php echo $text_title; ?></label>
			    <div class="input-group afield col-sm-12">
			    	<?php echo $field_title;?>
			    </div>
			</div>

			<div class="form-group <?php echo (!empty($error['description']) ? "has-error" : ""); ?>">
			    <label class="control-label" for="'.$field->element_id.'"><?php echo $text_description; ?></label>
			    <div class="input-group afield col-sm-12">
			    	<?php echo $field_description;?>
			    </div>
			</div>
             
        </form>
        </div><!-- col-sm-6 -->
	</div>
      
	<div class="panel-body panel-body-nopadding">
	</div>
		
	<div class="panel-footer">
		<div class="row">
		   <div class="center">
		     <button class="btn btn-primary rl_save">
		     <i class="fa fa-save"></i> <?php echo $form['submit']->text; ?>
		     </button>&nbsp;
		     <a class="btn btn-default rl_reset" href="<?php echo $cancel; ?>">
		     <i class="fa fa-refresh"></i> <?php echo $form['cancel']->text; ?>
		     </a>
		   </div>
		</div>
	</div>
	
 </div><!-- <div class="tab-content"> -->

</div>