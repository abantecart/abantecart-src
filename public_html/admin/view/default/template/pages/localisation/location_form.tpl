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

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
		</div>

		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>

	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
			<?php foreach ($form['fields'] as $name => $field) { ?>
			<?php
				//Logic to calculate fields width
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
		<div class="form-group <?php if (!empty($error[$name])) { echo "has-error"; } ?>">
			<label class="control-label col-sm-3 col-xs-12" for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
			<div class="input-group"><?php echo $field; ?></div>
			<?php
				if($name=='zone'){ ?>
					<div class="form-group">
						<div class="input-group col-sm-offset-3">
								<a class="btn btn-info btn-xs" onclick="selectAll();">
									<i class="fa fa-check-square-o fa-fw"></i>	<?php echo $text_select_all; ?>
								</a>
								<a class="btn btn-default btn-xs" onclick="unselectAll();">
									<i class="fa fa-square-o fa-fw"></i> <?php echo $text_unselect_all; ?>
								</a>
						</div>
					</div>

				<?php } ?>


		    <?php if (!empty($error[$name])) { ?>
		    <span class="help-block field_err"><?php echo $error[$name]; ?></span>
		    <?php } ?>
		</div>
			<?php }  ?><!-- <div class="fieldset"> -->
	</div>
	
	<div class="panel-footer col-xs-12">
		<div class="text-center">
			<button class="btn btn-primary lock-on-click">
			<i class="fa fa-save fa-fw"></i> <?php echo $form['submit']->text; ?>
			</button>
			<button class="btn btn-default" type="reset">
			<i class="fa fa-refresh fa-fw"></i> <?php echo $button_reset; ?>
			</button>
			<a class="btn btn-default" href="<?php echo $cancel; ?>">
			<i class="fa fa-arrow-left fa-fw"></i> <?php echo $form['cancel']->text; ?>
			</a>
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
				+'<input class="scrollbox" id="check_'+i+'" type="checkbox" value="'+i+'" name="zone_id[]" />'
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


	function selectAll() {
		$('input[name*=\'zone_id\[\]\']').attr('checked', 'checked');
		//$('#tables').find('.afield').addClass('checked');
	}
	function unselectAll() {
		$('input[name*=\'zone_id\[\]\']').removeAttr('checked');
		//$('#tables').find('.afield').removeClass('checked');
	}

//--></script>
