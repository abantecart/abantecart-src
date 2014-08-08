<div id="rl_container">
	<ul class="nav nav-tabs nav-justified nav-profile">
	<li id="resource" data-id="<?php echo $resource_id; ?>" data-type="<?php echo $type; ?>" class="active"><a href="#"><strong><?php echo $resource['name']; ?></strong></a></li>
<?php if(has_value($object_id)) { ?>	
	<li id="object" data-id="<?php echo $resource_id; ?>" data-type="<?php echo $type; ?>"><a href="#"><strong><?php echo $object_title; ?></strong></a></li>
<?php } ?>	
	<li id="library" data-id="<?php echo $resource_id; ?>" data-type="<?php echo $type; ?>"><a href="#"><span><?php echo $heading_title; ?></span></a></li>
	</ul>

 <div class="tab-content rl-content">

	<ul class="reslibrary-options">
		<li>
			<form id="<?php echo $search_form->name; ?>" name="<?php echo $search_form->name; ?>" action="<?php echo $current_url; ?>" class="form-inline" role="form">
			<div class="form-group">
				<div class="input-group input-group-sm"> 
				<?php echo $rl_types; ?>
				</div>
			</div>         	
			<div class="form-group">
				<div class="input-group input-group-sm">  
				<?php echo $search_field_input; ?>     
				</div>  	    
         	</div>    
			<div class="form-group">
				<button class="btn btn-xs btn-primary btn_search" type="submit"><?php echo $button_go; ?></button>
			</div>         	
        	</form>
        </li>	
        <li>
          <a id="add_resource" class="btn btn-xs btn-default add_resource tooltips" data-original-title="<?php echo $button_add; ?>"><i class="fa fa-plus"></i></a>
        </li>
        <li>
          <a class="itemopt rl_download" href="" onclick="false;"><i class="fa fa-download"></i></a>
        </li>
        <li>
          <a class="itemopt rl_delete" href="" onclick="false;"><i class="fa fa-trash-o"></i></a>
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
        <div class="col-sm-6 col-xs-12">

		    <div class="resource_image"></div>

	        <table class="files resource-details" cellpadding="0" cellspacing="0">
	        <tr>
	            <td><?php echo $text_mapped_to; ?></td>
	            <td class="mapped"></td>
	        </tr>
	        <tr id="do_map_info">
	            <td><?php echo $text_map; ?></td>
	            <td>
	                <?php if ($mode != 'url') { ?>
	                <a class="btn_action resource_unmap" id="map_this_info"><span class="icon_s_save">&nbsp;<span
	                    class="btn_text"><?php echo $button_select_resource; ?></span></span></a>
	                <?php } else { ?>
	                <a class="btn_action resource_unmaps use" id="map_this_info" rel="1"><span
	                    class="icon_s_save">&nbsp;<span
	                    class="btn_text"><?php echo $button_select_resource; ?></span></span></a>
	                <?php } ?>
	            </td>
	        </tr>
	    	</table>
        </div><!-- col-sm-6 -->

        <div class="col-sm-6 col-xs-12">
        <table class="files resource-details" cellpadding="0" cellspacing="0">
            <tr>
                <td colspan="2" class="sub_title"><?php echo $text_edit_resource ?></td>
            </tr>
            <tr>
                <td></td>
                <td class="message"></td>
            </tr>
            <tr>
                <td><?php echo $text_resource_code; ?></td>
                <td><?php echo $field_resource_code;?></td>
            </tr>
            <tr>
                <td><?php echo $text_name; ?></td>
                <td><?php echo $field_name; ?></td>
            </tr>
            <tr>
                <td><?php echo $text_title; ?></td>
                <td><?php echo $field_title; ?></td>
            </tr>
            <tr>
                <td><?php echo $text_description; ?></td>
                <td><?php echo $field_description; ?></td>
            </tr>
        </table>
        </div><!-- col-sm-6 -->
	</div>
      
	<div class="panel-body panel-body-nopadding">
	</div>
		
	<div class="panel-footer">
		<div class="row">
		   <div class="center">
		     <button class="btn btn-primary">
		     <i class="fa fa-save"></i> <?php echo $form['submit']->text; ?>
		     </button>&nbsp;
		     <a class="btn btn-default" href="<?php echo $cancel; ?>">
		     <i class="fa fa-refresh"></i> <?php echo $form['cancel']->text; ?>
		     </a>
		   </div>
		</div>
	</div>
	</form>
	
 </div><!-- <div class="tab-content"> -->

</div>