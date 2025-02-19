
<div class="row">

<div class="col-md-4"> 
	<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo $option_data['language'][$language_id]['name']; ?></h3>
	</div>
	<div id="option_edit_form" class="panel-body panel-body-nopadding">
		<div class="form-group">
			<label class="heading col-sm-10"><?php echo $text_option_type; ?>: <?php echo $option_type; ?></label>
			<div class="input-group col-sm-2">
			<a class="pull-right btn btn-default tooltips" onclick="optionDelete('<?php echo $button_remove_option->href; ?>')" data-original-title="<?php echo $button_remove_option->text; ?>" data-confirmation="delete">
			 <i class="fa fa-trash-o"></i>
			 </a>
		    </div>
		</div>

		<?php
	    foreach ($fields['common'] as $name=>$field) { ?>
        <?php
            $entry = ${'entry_'.$name};
            if(!is_object($field)){ continue; }

            $widthCssClasses = getBSCssClass($field->style); ?>
			<div class="form-group <?php if($error[$name]) { echo "has-error"; } ?>">
				<label class="control-label col-md-6" for="<?php echo $field->element_id; ?>"><?php echo $entry; ?></label>
				<div class="input-group input-group-sm afield <?php echo $widthCssClasses; ?>">
					<?php echo $field;?>
				</div>
			    <?php if ($error[$name]) { ?>
			    <span class="help-block field_err"><?php echo $error[$name]; ?></span>
			    <?php } ?>
			</div>
		<?php } ?>
        <div class="clearfix">
            <a class="btn pull-right" data-toggle="collapse" data-target="#collapseAdvanced" aria-expanded="false" aria-controls="collapseAdvanced">
                <?php echo $text_advanced_settings; ?>&nbsp;<span class="caret"></span>
            </a>
        </div>
        <div class="collapse" id="collapseAdvanced">
            <div class="">
                <?php
                foreach ($fields['advanced'] as $name=>$field) { ?>
                    <?php
                    $entry = ${'entry_'.$name};
                    if(!is_object($field)){ continue; }
                    $widthCssClasses = getBSCssClass($field->style); ?>
                    <div class="form-group <?php if($error[$name]) { echo "has-error"; } ?>">
                        <label class="control-label col-md-6" for="<?php echo $field->element_id; ?>"><?php echo $entry; ?></label>
                        <div class="input-group input-group-sm afield <?php echo $widthCssClasses; ?>">
                            <?php echo $field;?>
                        </div>
                        <?php if ($error[$name]) { ?>
                            <span class="help-block field_err"><?php echo $error[$name]; ?></span>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>


	</div>
	<div class="panel-footer">
		<div class="center">
			 <button id="update_option" class="btn btn-primary">
			 <i class="fa fa-save"></i> <?php echo $button_save->text; ?>
			 </button>
			 &nbsp;
			 <a id="reset_option" class="btn btn-default" href="<?php echo $button_reset->href; ?>">
			     <i class="fa fa-refresh"></i> <?php echo $button_reset->text; ?>
			 </a>
		</div>
	</div>
</div>
</div>

<?php echo $update_option_values_form['open']; ?>
<div class="col-md-8"> 
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo $text_option_values; ?></h3>
	</div>
	<div class="panel-body panel-body-nopadding">
		<table id="option_values_tbl" class="table table_narrow">
			<thead>
				<tr>
					<?php if($with_default){?>
					<th class="left">
						<a href="#" title="Click to uncheck default value" class="uncheck tooltips">
							<?php echo $text_default; ?>&nbsp;&nbsp;<i class="fa fa-refresh"></i>
						</a>
					</th>
					<?php } ?>
					<?php foreach ($option_values_title as $entry) {
					 if ($option_data['element_type']==='U' && ($entry==='entry_option_value' || $entry==='entry_option_quantity' || $entry==='entry_track_option_stock')) {
					  continue;
					} ?>
					<th class="left"><?php echo ${$entry}; ?></th>
					<?php } ?>
					<th class="left"></th>
					<?php if ($selectable){?>
						<th class="left"></th>
					<?php }?>
				</tr>
			</thead>
		    <?php foreach ($option_values as $item) { ?>
		        <?php echo $item['row']; ?>
		    <?php } ?>

		</table>
	</div>
	<div class="panel-footer">
		<div class="center">
			<?php if (in_array($option_data['element_type'], $elements_with_options)) { ?>
			<a href="#" title="<?php echo $button_add?>" id="add_option_value" class="btn btn-success"><i class="fa fa-plus-circle fa-lg"></i></a>&nbsp;&nbsp;
			<?php } ?>
			<button type="submit" class="btn btn-primary lock-on-click  ">
			    <i class="fa fa-save"></i> <?php echo $button_save->text; ?>
			</button>
			<a id="reset_option" class="btn btn-default" href="<?php echo $button_reset->href; ?>">
			    <i class="fa fa-refresh"></i> <?php echo $button_reset->text; ?>
			</a>
		</div>
	</div>
</div>
</div>
</form>


<table style="display:none;" id="new_row_table">
	<?php echo $new_option_row ?>
</table>


</div>