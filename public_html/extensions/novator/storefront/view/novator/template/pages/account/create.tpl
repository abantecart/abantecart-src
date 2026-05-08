<div class="row title">
    <div class="col-xl-12">
        <h1 class="h2 heading-title">
            <?php echo $heading_title; ?>
        </h1>
    </div>
</div>
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
<?php }

echo $form['form_open']; ?>
<p><?php echo $text_account_already; ?></p>
<?php
foreach($form['fields'] as $group => $fields){
    if($group == 'newsletter'){
        echo $this->getHookVar('customer_attributes');
    } ?>
<div class="card mb-4">
    <div class="card-body">
        <?php $groupName = current($fields)->field_group_name;
        if($groupName){ ?>
        <h5 class="border-bottom pb-3 mb-4"><?php echo $groupName; ?></h5>
        <?php
        }
        foreach ($fields as $fieldKey => $field) {
            if($field->type == 'hidden') {
                echo $field;
                continue;
            }?>
            <div class="row my-2">
                <label for="<?php echo $field->element_id ?>"
                       class="col-sm-3 col-form-label me-2"><?php echo $field->display_name; ?></label>
                <div class="col-sm-8 h-100">
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
</div>
<?php } ?>
<div class="ps-4 p-3 col-12 d-flex flex-wrap">
    <?php if ($text_agree) { ?>
        <div class="form-check-inline me-0 d-flex flex-nowrap text-nowrap align-items-center ">
            <?php
            $form['agree']->checked = false;
            $form['agree']->attr .= ' autocomplete="off" ';
            $form['agree']->label_text = $text_agree . '&nbsp;<a id="policyLink" href="' . $text_agree_href . '" ><b>' . $text_agree_href_text . '</b></a>';
            echo $form['agree']; ?>
        </div>
    <?php } ?>

    <button id="submit_button" type="submit"
            class="btn btn-primary ms-auto<?php echo $text_agree ? ' disabled' : ''; ?> mt-3 mt-md-0" role="button"
            title="<?php echo_html2view($form['continue']->name); ?>">
        <i class="bi bi-check"></i>
        <?php echo $form['continue']->name ?>
    </button>
</div>
</form>

<div id="privacyPolicyModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="privacyPolicyModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="privacyPolicyModalLabel"><?php echo $text_agree_href_text; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal"><?php echo $text_close; ?></button>
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

<?php
$googlePlacesScript = $this->getHookVar('google_places_script');
if ($googlePlacesScript) {
    echo $googlePlacesScript;
} else {
    include($this->templateResource('/template/common/google_places.js.tpl'));
}
?>
