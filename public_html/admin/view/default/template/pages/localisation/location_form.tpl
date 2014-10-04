<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php if($location_id){?>
	<ul class="nav nav-tabs nav-justified nav-profile">
		<?php
			foreach ($tabs as $tab) {
				if($tab['active'] ){
					$classname = 'active';
				}else{
					$classname = '';
				}
		?>		<li class="<?php echo $classname; ?>"><a <?php echo ($tab['href'] ? 'href="' . $tab['href'] . '" ' : ''); ?>><strong><?php echo $tab['text']; ?></strong></a></li>
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
			<?php foreach ($form['fields'] as $name => $field) { ?>
			<?php
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
			<div class="input-group afield <?php echo $widthcasses; ?> <?php echo ($name == 'description' ? 'ml_ckeditor' : '')?>">
				<?php echo $field;
				if($name=='zone'){ ?>
					<div class="dl-horizontal">
						<input type="checkbox" value="1" id="zones_selectall">
						<label for="zones_selectall"><?php echo $text_select_all; ?></label>
					</div>
				<?php } ?>

			</div>
		    <?php if (!empty($error[$name])) { ?>
		    <span class="help-block field_err"><?php echo $error[$name]; ?></span>
		    <?php } ?>
		</div>
			<?php }  ?><!-- <div class="fieldset"> -->
	</div>
	<div class="panel-footer">
		<div class="row">
		   <div class="col-sm-6 col-sm-offset-3">
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









<script type="text/javascript"><!--
var zone_id = '<?php echo $zone_id; ?>';
jQuery(function ($) {

    getZones = function (country_id) {
        if (!country_id) {
            return false;
        }

        $.ajax(
            {
                url:'<?php echo $common_zone; ?>&country_id=' + country_id + '&zone_id=0',
                type:'GET',
                dataType:'json',
                success:function (data) {
                    result = data;
                    showZones(data);
                },
                error:function (req, status, msg) {
                }
            });
    }

    showZones = function (data) {
        var options = '';

        $.each(data['options'], function (i, opt) {
			if(i!=0){
            options += '<label for="check_' + i + '">'
				+'<div class="afield acheckbox"><span>'
				+'<input id="check_'+i+'" type="checkbox" value="'+i+'" name="zone_id[]" style="opacity: 0;" />'
				+'</span></div>' + opt.value + '</label>';
			}

        });

        $('div.scrollbox'). html(options).each(function(){
		$("div.scrollbox input").aform({triggerChanged: true, showButtons: false });
		});

    }
    if(!$('#cgFrm_zone_id').val()){
        getZones($('#cgFrm_country_id').val());
    }
    $('#cgFrm_country_id').change(function () {
        getZones($(this).val());
        $('#cgFrm_zone_id').val('').change();

    });
});


	$('#zones_selectall').click(function () {
		if (this.checked) {
			$('input[name*=\'zone_id\[\]\']').attr('checked', 'checked');
		} else {
			$('input[name*=\'zone_id\[\]\']').removeAttr('checked');
		}
	});

//--></script>
