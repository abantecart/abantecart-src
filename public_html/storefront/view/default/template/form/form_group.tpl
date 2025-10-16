<?php $groupName = $group['name'];
if($groupName){ ?>
    <h4><?php echo $groupName; ?></h4>
    <?php
} ?>
<div class="card mb-4">
    <div class="card-body">
        <?php
        foreach ($group['fields'] as $field_name => $field) { ?>
            <div class="mb-3">
                <?php echo $fields_html[$field]; ?>
                <span class="help-block text-danger"><?php echo ${'error_'.$field_name}; ?></span>
            </div>
        <?php } ?>
    </div>
</div>