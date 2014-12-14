<?php foreach ( $types as $type ) { ?>
<div class="panel panel-default panel_<?php echo $type['type_name']; ?>" id="panel_<?php echo $type['type_name']; ?>">
	<div class="panel-heading">
	  <div class="panel-btns">
	    <a class="minimize" href="">−</a>
	  </div>
	  <h3 class="panel-title"><a class="tab_<?php echo $type['type_name']; ?>" id="tab_<?php echo $type['type_name']; ?>"><?php echo ${'text_type_'.$type['type_name']}; ?></a></h3>
	</div>	
	<div class="panel-body panel-body-nopadding" id="type_<?php echo $type['type_name']; ?>" style="display:none">
		<div class="row reslibrary type_blocks">
		<?php //resource library list insearted here  ?>
		</div>
	</div>
</div>
<?php } ?>