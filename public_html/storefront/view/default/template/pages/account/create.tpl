<h1 class="ms-3 my-2 heading-title ">
    <i class="fa fa-id-card me-2"></i>
    <?php echo $heading_title; ?>
</h1>
<?php if ($success) { ?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php }
if ($error_warning) { ?>
    <div class="alert alert-error alert-danger alert-dismissible" role="alert">
        <?php echo $error_warning; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>

<div class="container">
    <?php
    echo $form['form_open']; ?>
    <p><?php echo $text_account_already; ?></p>
    <?php
    foreach($form['fields'] as $group => $fields){
        if($field->type == 'hidden') {
            echo $field;
            continue;
        }
        if($group == 'newsletter'){
            echo $this->getHookVar('customer_attributes');
        } ?>
        <div class="ps-4 border p-3 mb-4">
            <?php $groupName = current($fields)->field_group_name;
            if($groupName){ ?>
                <h4><?php echo $groupName; ?></h4>
                <?php
            }
            foreach ($fields as $fieldKey => $field) {?>
                <div class="row mb-3">
                    <label for="<?php echo $field->element_id ?>"
                           class="col-sm-4 col-form-label me-2"><?php echo $field->display_name; ?></label>
                    <div class="col-sm-7 h-100">
                        <?php
                        if(in_array($fieldKey,['password','password_confirm'])){
                            $field->attr .= ' role="password" ';
                        }
                        echo $field; ?>
                        <span class="help-block text-danger"><?php echo ${'error_' . $fieldKey}; ?></span>
                    </div>
                </div>
                <?php
            }?>
        </div>
<?php } ?>
    <div class="ps-4 p-3 col-12 d-flex flex-wrap">
        <?php if ($text_agree) { ?>
           <div class="form-check-inline me-0 d-flex flex-nowrap text-nowrap align-items-center ">
               <?php
               $form['agree']->checked = false;
               $form['agree']->label_text = $text_agree.'&nbsp;<a id="policyLink" href="'.$text_agree_href.'"><b>'. $text_agree_href_text . '</b></a>';
               echo $form['agree']; ?>
           </div>
        <?php } ?>
        <button id="submit_button" type="submit"
                role="button"
                class="btn btn-primary ms-auto<?php echo $text_agree ? ' disabled' : ''; ?> mt-3 mt-md-0"
                title="<?php echo_html2view($form['continue']->name); ?>">
            <i class="fa fa-check"></i>
            <?php echo $form['continue']->name ?>
        </button>
    </div>
    </form>
</div>

<div id="privacyPolicyModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="privacyPolicyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="privacyPolicyModalLabel"><?php echo $text_agree_href_text; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $text_close; ?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        const agreeCheckbox = $('#<?php echo ControllerPagesAccountCreate::formTxtId?>_agree');
        if (agreeCheckbox.length) {
            agreeCheckbox.on('click', function () {
                if ($(this).is(':checked')) {
                    $('#submit_button').removeClass('disabled');
                } else {
                    $('#submit_button').addClass('disabled');
                }
            });
        }

        $('#policyLink').on('click', (e) => {
            e.preventDefault();
            openModalRemote('#privacyPolicyModal',<?php js_echo($text_agree_href);?>);
        })
    });
</script>
