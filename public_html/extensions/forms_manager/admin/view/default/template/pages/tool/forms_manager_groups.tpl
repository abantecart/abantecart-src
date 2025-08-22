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
                       title="Add New Field Group"
                       data-original-title="Add New Field Group"
                       data-target="#group_modal" data-toggle="modal">
                        <i class="fa fa-plus"></i>
                    </a>
                </div>
            </div>
            <?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
        </div>
        
        <div class="panel-body panel-body-nopadding tab-content col-xs-12">
            <form id="assign_fields_form" method="post" action="<?php echo $assign_fields_action; ?>">
                <input type="hidden" name="form_id" value="<?php echo $form_id; ?>">
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
                                <?php if (!empty($form_fields)) { ?>
                                    <?php foreach ($form_fields as $field) { ?>
                                        <tr>
                                            <td><?php echo $field['field_name_display']; ?></td>
                                            <td>
                                                <select class="form-control field-group-select" 
                                                        name="field_groups[<?php echo $field['field_id']; ?>]">
                                                    <option value=""><?php echo $text_no_group; ?></option>
                                                    <?php foreach ($available_groups as $group) { ?>
                                                        <option value="<?php echo $group['group_id']; ?>" 
                                                                <?php echo ($field['group_id'] == $group['group_id']) ? 'selected' : ''; ?>>
                                                            <?php echo $group['name']; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
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
                        <button class="btn btn-primary lock-on-click" type="submit">
                            <i class="fa fa-save fa-fw"></i> <?php echo $button_save; ?>
                        </button>
                        <a class="btn btn-default" href="<?php echo $groups_reset_url; ?>">
                            <i class="fa fa-refresh fa-fw"></i> <?php echo $button_reset; ?>
                        </a>
                    </div>
                </div>
            </form>
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
