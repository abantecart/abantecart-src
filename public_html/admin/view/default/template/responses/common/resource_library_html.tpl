<?php foreach ( $types as $type ) { ?>
<div class="panel panel-default">
	<div class="panel-heading">
	  <div class="panel-btns">
	    <a class="minimize" href="">−</a>
	  </div>
	  <h3 class="panel-title"><a id="tab_<?php echo $type['type_name']; ?>"><?php echo ${'text_type_'.$type['type_name']}; ?></a></h3>
	</div>	
	<div class="panel-body panel-body-nopadding" id="type_<?php echo $type['type_name']; ?>" style="display:none">
		<div class="row reslibrary type_blocks">
	
	
		</div>
	</div>
</div>

<?php } ?>
<div id="confirm_unmap_dialog" title="<?php echo $confirm_unmap_title ?>" style="display:none">
    <?php echo $text_confirm_unmap ?>
</div>
<div id="confirm_del_dialog" title="<?php echo $confirm_del_title ?>" style="display:none">
    <?php echo $text_confirm_del ?>
</div>