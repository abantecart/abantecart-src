<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="dropdown dropdown-toggle">
			    <a data-toggle="dropdown" href="#" class="btn btn-primary dropdown-toggle tooltips" title="<?php echo $button_add.$text_type; ?>" >
			    <i class="fa fa-plus fa-fw"></i>  
			    <span class="caret"></span></a>
			    <ul class="dropdown-menu " role="menu">
			    	<?php foreach($banner_types as $in){ ?>
			    		<li><a href="<?php echo $in['href'] ?>" ><?php echo $in['icon']; ?><?php echo $in['text']; ?></a></li>
			    	<?php } ?>
			    </ul>
			</div>
		</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>

	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<?php echo $listing_grid; ?>
	</div>

</div>