<?php foreach ($form['fields'] as $name => $field) { ?>
	<tr class="subform">
		<td><?php echo $form[ 'text' ][$name]; ?></td>
		<td class="<?php echo ($name=='description'? 'ml_ckeditor': 'ml_field'); ?>">
		<?php if($name=='rl'){ ?>
		<div id="rl_<?php echo $banner_id?>" class="add_resource" style="margin-top: 10px;"><?php echo  $field.'</div>';
		}else{ echo $field; } ?>
		<?php if (!empty($error[$name])) { ?>
				<div class="field_err"><?php echo $error[$name]; ?></div>
		<?php } ?>
		</td>
	</tr>
<?php }   ?>
