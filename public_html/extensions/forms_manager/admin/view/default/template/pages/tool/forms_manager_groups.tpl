<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>
<div id="content" class="tab-content">
    <?php echo $tabs; ?>
    <div class="panel panel-default">
        <div class="panel-heading col-xs-12">
            <div class="pull-left">
                <a class="btn btn-white tooltips back-to-grid mr10"
                   href="<?php echo $list_url; ?>" data-toggle="tooltip"
                   data-original-title="<?php echo_html2view($text_back_to_list); ?>">
                    <i class="fa fa-arrow-left fa-lg"></i>
                </a>
                <div class="btn-group mr10 toolbar">
                    <a class="btn btn-primary tooltips" href="#"
                       title="<?php echo_html2view($text_add_new_group); ?>"
                       data-original-title="<?php echo_html2view($text_add_new_group); ?>"
                       data-target="#group_modal" data-toggle="modal">
                        <i class="fa fa-plus"></i>
                    </a>
                </div>
            </div>
            <?php
            /** @see public_html/admin/view/default/template/common/content_buttons.tpl */
            include($tpl_common_dir . 'content_buttons.tpl'); ?>
        </div>
        <div class="panel-body panel-body-nopadding tab-content col-xs-12">
            <div class="row">
                <?php if (!$form['form_groups']) { ?>
                    <div class="panel-footer col-xs-12">
                        <div class="text-center">
                            <?php
                            echo $text_no_group;
                            ?>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="col-xs-12 col-md-6">
                        <h3 class="control-label"><?php echo $text_groups; ?></h3>
                        <?php echo $form['form_open']; ?>
                        <input type="hidden" name="remove_groups" value=""/>
                        <table class="table table-narrow">
                            <thead>
                            <tr>
                                <th><?php echo $column_text_id; ?></th>
                                <th><?php echo $entry_group_name; ?></th>
                                <th><?php echo $entry_group_description; ?></th>
                                <th><?php echo $text_sort_order; ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($form['form_groups'] as $group) { ?>
                                <tr id="group_<?php echo $group['group_id']; ?>" class="value">
                                    <td><?php
                                        $group['group_txt_id']->style = 'col-sm-2';
                                        echo $group['group_txt_id']; ?></td>
                                    <td class="center"><?php echo $group['name']; ?></td>
                                    <td class="center"><?php echo $group['description']; ?></td>
                                    <td><?php
                                        $group['sort_order']->style = 'col-2';
                                        echo $group['sort_order'];
                                        ?>
                                    </td>
                                    <td>
                                        <a class="remove btn btn-danger-alt" title="<?php echo $button_remove; ?>">
                                            <i class="fa fa-minus-circle"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <div class="panel-footer col-xs-12">
                            <div class="text-center">
                                <?php
                                $form['submit']->style = 'btn btn-primary lock-on-click';
                                $form['submit']->icon .= 'fa fa-save fa-fw';
                                echo $form['submit'];
                                $form['reset']->icon .= 'fa fa-refresh fa-fw';
                                echo $form['reset'];
                                ?>
                            </div>
                        </div>
                        </form>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <?php echo $form2['form_open']; ?>
                        <h3 class="control-label"><?php echo $text_assign_fields_to_groups; ?></h3>
                        <div class="form-group">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th><?php echo $column_field_name; ?></th>
                                        <th><?php echo $entry_group_name; ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if ($form2['fields']) {
                                        foreach ($form2['fields'] as $field) {
                                            if($priorValue != $field->value){ ?>
                                                <tr>
                                                    <td colspan="2" class="center">
                                                        <b><?php echo ucfirst($group_list[$field->value]['group_txt_id'])?></b>
                                                    </td>
                                                </tr>
                                            <?php }
                                            $priorValue = $field->value;?>
                                            <tr>
                                                <td><?php echo $field->display_name; ?></td>
                                                <td>
                                                    <?php
                                                    echo $field;
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php
                                        } ?>
                                    <?php } else { ?>
                                        <tr>
                                            <td colspan="2" class="text-center"><?php echo $text_no_fields; ?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="panel-footer col-xs-12">
                            <div class="text-center">
                                <?php
                                $form2['submit']->style = 'btn btn-primary lock-on-click';
                                $form2['submit']->icon .= 'fa fa-save fa-fw';
                                echo $form2['submit'];
                                $form2['reset']->icon .= 'fa fa-refresh fa-fw';
                                echo $form2['reset'];
                                ?>
                            </div>
                        </div>
                        </form>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>


<?php
$new_group_form['groups']->style .= ' mb20 ';
$modal_content = '<div class="add-option-modal" >
			<div class="panel panel-default">
			    <div id="collapseTwo" >
			    	' . $new_group_form['form_open'] . '
			    	<div class="panel-body panel-body-nopadding">
			    		' . $new_group_form['groups'] . '
			    		<div class="mt10 new-group-wrp" style="display:none;">
			    			<div id="new">
                                <div class="form-group ">
                                    <label class="control-label col-sm-3 col-xs-12" for="' . $new_group_form['fields']['group_name']->element_id . '">
                                        ' . $new_group_form['fields']['group_name']->display_name . '
                                    </label>
                                    <div class="input-group afield ">
                                        ' . $new_group_form['fields']['group_name'] . '
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3 col-xs-12" for="' . $new_group_form['fields']['group_description']->element_id . '">
                                        ' . $new_group_form['fields']['group_description']->display_name . '
                                    </label>
                                    <div class="input-group afield ">
                                        ' . $new_group_form['fields']['group_description'] . '
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3 col-xs-12" for="' . $new_group_form['fields']['sort_order']->element_id . '">
                                        ' . $new_group_form['fields']['sort_order']->display_name . '
                                    </label>
                                    <div class="input-group afield ">
                                        ' . $new_group_form['fields']['sort_order'] . '
                                    </div>
                                </div>
                            </div>
			    		</div>
			    	</div>
			    	<div class="panel-footer">
			    		<div class="row">
			    		   <div class="center">
			    			 <button type="button" class="btn btn-default" data-dismiss="modal">' . $button_cancel . '</button>
                             <button type="button" class="btn btn-primary" id="save_group_btn">' . $button_save . '</button>
			    		   </div>
			    		</div>
			    	</div>
			    	</form>
			    </div>
			</div>
		</div>';

echo $this->html->buildElement(
        [
                'type'       => 'modal',
                'id'         => 'group_modal',
                'modal_type' => 'lg',
                'title'      => $text_add_new_group,
                'content'    => $modal_content
        ]
);
?>
<script type="text/javascript">
    let removeGroups = [];
    $(document).ready(function () {
        $('#formGroupFrm').on('click', 'a.remove', function () {
            let row = $(this).closest('tr');
            let groupId = row.attr('id').replace('group_', '');

            if (row.hasClass('danger')) {
                row.removeClass('danger');
                removeGroups = removeGroups.filter(id => id !== groupId);
            } else {
                row.addClass('danger');
                removeGroups.push(groupId);
            }
            $('[name=remove_groups]').val(removeGroups.join(','));
        });

        // Handle add group modal
        $('#save_group_btn').on('click', function () {
            var formData = $('#modalGroupFrm').serialize();
            formData += '&form_id=<?php echo $form_id; ?>';

            $.ajax({
                url: '<?php echo $groups_response_url; ?>',
                type: 'POST',
                data: formData,
                dataType: 'html',
                async: false,
                success: function () {
                    location = location;
                }
            });
        });


        $(document).on('change', '#modalGroupFrm_group_id', function () {
            const current_id = $(this).val();
            if (current_id !== 'new') {
                $(".new-group-wrp").hide();
            } else {
                $(".new-group-wrp").show();
            }
        });
        $('#modalGroupFrm_group_id').change();

    });
</script>
