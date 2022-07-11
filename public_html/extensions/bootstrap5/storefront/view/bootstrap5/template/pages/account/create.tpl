<h1 class="ms-3 my-2 heading-title ">
    <i class="fa fa-id-card me-2"></i>
    <?php echo $heading_title; ?>
</h1>

<?php if ($success) { ?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>

<?php if ($error_warning) { ?>
    <div class="alert alert-error alert-danger alert-dismissible" role="alert">
        <?php echo $error_warning; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>

<div class="container">
    <?php
    echo $form['form_open']; ?>
    <p><?php echo $text_account_already; ?></p>
    <h4><?php echo $text_your_details; ?></h4>
    <div class="ps-4 border p-3 mb-4">
        <?php
            foreach ($form['fields']['general'] as $field_name => $field) {
                //todo: remove this in the next major release
                if($field_name == 'loginname'){ continue;} ?>
                <div class="mb-3 row">
                    <label for="<?php echo $field->element_id?>" class="text-nowrap col-sm-2 col-form-label me-2"><?php echo ${'entry_'.$field_name}; ?></label>
                    <div class="col-sm-9 h-100">
                        <?php echo $field; ?>
                        <span class="help-block text-danger"><?php echo ${'error_'.$field_name}; ?></span>
                    </div>
                </div>
        <?php
            }
        ?>
    </div>

	<h4><?php echo $text_your_address; ?></h4>
	<div class="ps-4 border p-3 mb-4">
		<?php
			foreach ($form['fields']['address'] as $field_name=>$field) {?>
                <div class="mb-3 row">
                    <label for="<?php echo $field->element_id?>" class="text-nowrap col-sm-2 col-form-label me-2"><?php echo ${'entry_'.$field_name}; ?></label>
                    <div class="col-sm-9 h-100">
                        <?php echo $field; ?>
                        <span class="help-block text-danger"><?php echo ${'error_'.$field_name}; ?></span>
                    </div>
                </div>
		<?php
			}
		?>
	</div>

    <h4><?php echo $text_login_details; ?></h4>
    <div class="ps-4 border p-3 mb-4">
        <?php if (isset($form['fields']['general']['loginname'])) { ?>
            <div class="mb-3 row">
                <label for="<?php echo $field->element_id?>" class="text-nowrap col-sm-2 col-form-label me-2"><?php echo $entry_loginname; ?></label>
                <div class="col-sm-9 h-100">
                    <?php
                    $form['fields']['general']['loginname']->attr .= ' role="username" ';
                    echo $form['fields']['general']['loginname']; ?>
                    <span class="help-block text-danger"><?php echo $error_loginname; ?></span>
                </div>
            </div>
        <?php } ?>
        <div class="mb-3 row">
            <label for="<?php echo $field->element_id?>" class="text-nowrap col-sm-2 col-form-label me-2"><?php echo $entry_password; ?></label>
            <div class="col-sm-9 h-100">
                <?php
                $form['fields']['password']['password']->attr .= ' role="password" ';
                echo $form['fields']['password']['password']; ?>
                <span class="help-block text-danger"><?php echo $error_password; ?></span>
            </div>
        </div>
        <div class="mb-3 row">
            <label for="<?php echo $field->element_id?>" class="text-nowrap col-sm-2 col-form-label me-2"><?php echo $entry_confirm; ?></label>
            <div class="col-sm-9 h-100">
        <?php
                $form['fields']['password']['confirm']->attr .= ' role="password" ';
                echo $form['fields']['password']['confirm']; ?>
                <span class="help-block text-danger"><?php echo $error_confirm; ?></span>
            </div>
        </div>
    </div>

    <?php echo $this->getHookVar('customer_attributes'); ?>

    <h4><?php echo $text_newsletter; ?></h4>
    <div class="ps-4 border p-3 ">
        <div class="row align-items-center">
            <label for="<?php echo $field->element_id?>" class="text-nowrap col-5 col-sm-2 col-form-label me-2"><?php echo $entry_newsletter; ?></label>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2 h-100">
                <?php echo $form['fields']['newsletter']['newsletter']; ?>
            </div>
            <?php if ($form['fields']['newsletter']['captcha']) { ?>
            <div class="col-12 col-md-12 col-lg-7 h-100 mt-3 my-sm-0    ">
                <?php echo $form['fields']['newsletter']['captcha']; ?>
            </div>
            <?php } ?>
        </div>
    </div>

    <div class="ps-4 p-3 col-12 d-flex flex-wrap">
        <?php if ($text_agree) { ?>
           <div class="d-flex flex-nowrap text-nowrap align-items-center ">
               <?php
               $form['agree']->style .= 'me-3';
               $form['agree']->checked = false;
               $form['agree']->attr .= ' onclick="$(\'#submit_button\').toggle();" autocomplete="off" ';
               echo $form['agree']; ?>
               <label for="<?php echo $form['agree']->element_id?>">
                   <?php echo $text_agree; ?></label>&nbsp;<a href="<?php echo $text_agree_href; ?>" onclick="openModalRemote('#privacyPolicyModal','<?php echo $text_agree_href; ?>'); return false;"><b><?php echo $text_agree_href_text; ?></b></a>
           </div>
        <?php } ?>

        <button id="submit_button" type="submit"
                role="button" data-bs-toggle="button"
                style="display:none;"
                onclick="$('#AccountFrm').submit();"
                class="btn btn-primary ms-auto lock-on-click"
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
    $(document).ready(function(){
        <?php $cz_url = $this->html->getURL('common/zone', '&zone_id='. $zone_id); ?>
        $('#AccountFrm_country_id').change( function(){
            $('select[name=\'zone_id\']').load('<?php echo $cz_url;?>&country_id=' + $(this).val());
        });
        $('select[name=\'zone_id\']').load('<?php echo $cz_url;?>&country_id='+ $('#AccountFrm_country_id').val());
    });
</script>