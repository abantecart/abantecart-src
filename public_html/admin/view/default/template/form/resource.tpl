<input type="hidden" name="<?php echo $name; ?>" data-orgvalue="<?php echo $resource_path; ?>" value="<?php echo $resource_path; ?>" id="<?php echo $id; ?>"/>
<input type="hidden" name="<?php echo $name; ?>_resource_id" value="<?php echo $resource_id; ?>" id="<?php echo $id; ?>_resource_id"/>

<div id="<?php echo $wrapper_id; ?>" class="row reslibrary type_blocks"></div>
<script type="text/javascript">
	$(document).ready(function(){
		try{
			loadSingle('<?php echo $rl_type; ?>', '<?php echo $wrapper_id; ?>', '<?php echo $resource_id; ?>', '<?php echo $id; ?>');
		}catch(e){
			console.log('js-function "loadSingle" not found in the page scope or something another syntax error');
		}
	});
</script>