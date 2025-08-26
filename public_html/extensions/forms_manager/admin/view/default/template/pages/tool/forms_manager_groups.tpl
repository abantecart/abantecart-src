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
                <div class="col-xs-12 col-md-5">
                    <h3 class="control-label"><?php echo $text_groups; ?></h3>
                    <?php echo $form['form_open'];?>
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
                                        <tr id="group_<?php echo $group['group_id'];?>" class="value">
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
                                echo  $form['submit'];
                                $form['reset']->icon .= 'fa fa-refresh fa-fw';
                                echo  $form['reset'];
                                ?>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-xs-12 col-md-5">
                    <?php echo $form2['form_open'];?>
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
                                    <?php if ($form2['fields']) { ?>
                                        <?php foreach ($form2['fields'] as $field) { ?>
                                            <tr>
                                                <td><?php echo $field->display_name; ?></td>
                                                <td>
                                                    <?php
                                                       echo $field;
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
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
                                echo  $form2['submit'];
                                $form2['reset']->icon .= 'fa fa-refresh fa-fw';
                                echo  $form2['reset'];
                                ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Add Group Modal -->
<div class="modal fade" id="group_modal" tabindex="-1" role="dialog" aria-labelledby="groupModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="groupModalLabel"><?php echo $text_add_new_group; ?></h4>
            </div>
            <div class="modal-body">
                <form id="add_group_form">
                    <div class="form-group">
                        <label for="group_name"><?php echo $entry_group_name; ?> *</label>
                        <input type="text" class="form-control" id="group_name" name="group_name" required>
                    </div>
                    <div class="form-group">
                        <label for="group_description"><?php echo $entry_group_description; ?></label>
                        <textarea class="form-control" id="group_description" name="group_description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="sort_order"><?php echo $entry_sort_order; ?></label>
                        <input type="number" class="form-control" id="sort_order" name="sort_order" value="0">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $button_cancel; ?></button>
                <button type="button" class="btn btn-primary" id="save_group_btn"><?php echo $button_save; ?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    let removeGroups = [];
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
    $(document).ready(function() {
    // Handle add group modal
    $('#save_group_btn').on('click', function() {
        var formData = $('#add_group_form').serialize();
        formData += '&form_id=<?php echo $form_id; ?>';
        
        $.ajax({
            url: '<?php echo $groups_response_url; ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            async: false,
            success: function (html) {
                //Reset changed values marks
                resetAForm($("input, checkbox, select", '#add_group_form'));
                success_alert(html, true);
                location.reload();
            }
        });
    });
    
    // Reset modal form when closed
    $('#group_modal').on('hidden.bs.modal', function() {
        $('#add_group_form')[0].reset();
    });

});
</script>
