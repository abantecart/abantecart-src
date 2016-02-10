<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>
<div class="tab-content">
	<div class="panel-heading">
		<div class="primary_content_actions pull-left">
			<div class="btn-group mr10 toolbar">
			</div>
		</div>
		
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>

	<div class="panel-body panel-body-nopadding">

	<?php foreach ( $types as $type ) { 
		$title = ${'text_type_'.$type['type_name']};
		if(!$title) {
			$title = ucfirst($type['type_name']);
		}
		switch($type['type_name']){
		    case 'image':
		    	$icon = 'fa-file-image-o';
		    break;
		    case 'audio':
		    	$icon = 'fa-file-audio-o';
		    break;
		    case 'video':
		    	$icon = 'fa-file-movie-o';
		    break;
		    case 'pdf':
		    	$icon = 'fa-file-pdf-o';
		    break;
		    case 'archive':
		    	$icon = 'fa-file-archive-o';
		    break;
		    case 'download':
		    	$icon = 'fa-download';
		    break;
		    default:
		    	$icon = 'fa-file';
		}
	?>
	<div class="panel panel-default panel_<?php echo $type['type_name']; ?>" id="panel_<?php echo $type['type_name']; ?>">
		<div class="panel-heading">
		  <div class="panel-btns">
		    <a class="minimize" href="">−</a>
		  </div>
		  <h3 class="panel-title">
		  	<a href="#" data-type="<?php echo $type['type_name']; ?>" data-mode="list_all" class="tab_<?php echo $type['type_name']; ?> resource_edit" id="tab_<?php echo $type['type_name']; ?>">
		  	<i class="fa <?php echo $icon; ?> fa-fw"></i> 
		  	<?php echo $title; ?>
		  	</a>
		  	<p class="inblock">&nbsp;&nbsp;<?php echo $text_rl_latest; ?></p>
		  	<p class="inblock">
		  	<a href="#" data-type="<?php echo $type['type_name']; ?>" data-mode="list_all" class="tab_<?php echo $type['type_name']; ?> resource_edit" id="tab_<?php echo $type['type_name']; ?>">
		  	<i class="fa fa-folder-open fa-fw"></i> 
		  	</a>		  	
		  	</p>
		  	<span class="pull-right">
				<a class="tooltips" href="<?php echo $rl_types_url.'&rl_type='.$type['type_name']; ?>"
					   data-toggle="modal"
					   data-target="#rl_types_modal"
					   title="<?php echo $button_edit; ?>">
					<i class="fa fa-gears"></i>
				</a>		  	
				&nbsp;
		  	</span>
		  </h3>
		</div>	
		<div class="panel-body panel-body-nopadding" id="type_<?php echo $type['type_name']; ?>" style="display:none">
			<div class="row reslibrary type_blocks">
			<?php //resource library list insearted here  ?>
			</div>
		</div>
	</div>
	<?php } ?>

	</div>
</div><!-- <div class="tab-content"> -->

<?php echo $resources_scripts; ?>

<?php
	echo $this->html->buildElement(
		array(	'type' => 'modal',
				'id' => 'rl_types_modal',
				'modal_type' => 'lg',
				'data_source' => 'ajax'));
?>