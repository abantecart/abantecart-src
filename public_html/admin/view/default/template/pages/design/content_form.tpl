<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php if ($tabs) { ?>
	<ul class="nav nav-tabs nav-justified nav-profile">
		<?php
		foreach($tabs as $tab){?>
		<li <?php echo ($tab['active'] ? 'class="active"' : '') ?>>
			<a href="<?php echo $tab['href'] ? $tab['href'] : 'Javascript:void(0);'; ?>"><span><?php echo $tab['text']; ?></span></a>
		</li>
		<?php } ?>
		<?php echo $this->getHookVar('extension_tabs'); ?>
	</ul>
<?php } ?>

<div class="tab-content">
	<div class="panel-heading">
			<div class="pull-right">
			    <div class="btn-group mr10 toolbar">
                    <?php if (!empty ($help_url)) : ?>
                    <a class="btn btn-white tooltips" href="<?php echo $help_url; ?>" target="new" data-toggle="tooltip" title="" data-original-title="Help">
                    <i class="fa fa-question-circle fa-lg"></i>
                    </a>
                    <?php endif; ?>
			    </div>
                <?php echo $form_language_switch; ?>
			</div>
	</div>

	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding">
		<label class="h4 heading"><?php echo $form_title; ?></label>
			<?php foreach ($form['fields'] as $name => $field) {
				//Logic to cululate fileds width
				$widthcasses = "col-sm-7";
				if ( is_int(stripos($field->style, 'large-field')) ) {
					$widthcasses = "col-sm-7";
				} else if ( is_int(stripos($field->style, 'medium-field')) || is_int(stripos($field->style, 'date')) ) {
					$widthcasses = "col-sm-5";
				} else if ( is_int(stripos($field->style, 'small-field')) || is_int(stripos($field->style, 'btn_switch')) ) {
					$widthcasses = "col-sm-3";
				} else if ( is_int(stripos($field->style, 'tiny-field')) ) {
					$widthcasses = "col-sm-2";
				}
				$widthcasses .= " col-xs-12";
			?>
		<div class="form-group <? if (!empty($error[$name])) { echo "has-error"; } ?>">
			<label class="control-label col-sm-3 col-xs-12" for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
			<div id="field_<?php echo $name; ?>" class="input-group afield <?php echo $widthcasses; ?> <?php echo ($name == 'description' ? 'ml_ckeditor' : '')?>">
				<?php
				if($name=='sort_order'){ ?>
				<ul class="list-unstyled">
					<?php
					foreach($field as $s){ ?>
						<li class="col-sm-12 col-xs-12">
							<div class="row">
								<div class="col-sm-3"><?php echo $s['label']; ?>:</div>
								<div class="col-sm-3"><?php echo $s['field']?></div>
							</div>
						</li>
				<?php	} ?>
					</ul>
				<?php
				}else{
					echo $field;
				}
				?>
			</div>
		    <?php if (!empty($error[$name])) { ?>
		    <span class="help-block field_err"><?php echo $error[$name]; ?></span>
		    <?php } ?>
		</div>
			<?php }  ?><!-- <div class="fieldset"> -->
	</div>

	<div class="panel-footer">
		<div class="row">
		   <div class="col-sm-6 col-sm-offset-4 center">
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


<script type="text/javascript">
 $(document).ready(function(){
		CKEDITOR.replace('contentFrm_content', {
			height: '400px',
			filebrowserBrowseUrl: false,
			filebrowserImageBrowseUrl: '<?php echo $rl; ?>',
			filebrowserWindowWidth: '920',
			filebrowserWindowHeight: '520',
			language: '<?php echo $language_code; ?>'
		});

		$('#contentFrm_generate_seo_keyword').click(function () {
			var seo_name = $('#contentFrm_title').val().replace('%', '');
			$.get('<?php echo $generate_seo_url;?>&seo_name=' + seo_name, function (data) {
				$('#contentFrm_keyword').val(data).change();
			});
			return false;
		});
		var sort_order_clone = $('#field_sort_order').find('li').first().clone();



		$('#contentFrm_parent_content_id').change(function () {
			var old_values = {};
			var that = this;
			var old_keys = $('#field_sort_order').find('input[name^=sort_order]').map(function () {
				return this.name.replace('sort_order[', '').replace(']', '')
			}).get();
			var old_vals = $('#field_sort_order').find('input[name^=sort_order]').map(function () {
				return this.value;
			}).get();
			for (var k in old_keys) {
				var name = old_keys[k];
				old_values[name] = old_vals[k];
			}

			var values = $(that).val();
			var html = '';

			$('#field_sort_order').find('ul').html('');
			for (var k in values) {
				var temp_clone = sort_order_clone;
				temp_clone.find('input').attr('name', 'sort_order\[' + values[k] + '\]').attr('id', 'contentFrm_sort_order\[' + values[k] + '\]');

				if (old_values[values[k]]) {
					temp_clone.find('input').attr('value', old_values[values[k]].replace(/[^0-9]/g, ''));
				}

				temp_clone.find('div.row>div').first().html($(this).find('option:selected[value=' + values[k] + ']').text() + ':');

				$('#field_sort_order').find('ul').append('<li>'+temp_clone.html() + '</li');

			}
			$('#field_sort_order').find('input').aform( { triggerChanged: true, showButtons: 'false' } ).change();
		});

		$('#field_sort_order').find('input[name^=sort_order]').keyup(function () {
			$(this).val($(this).val().replace(/[^0-9]/g, ''));
		});
 });
</script>
