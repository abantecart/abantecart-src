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
<?php } ?>

<?php if ($error) { ?>
    <div class="alert alert-error alert-danger alert-dismissible" role="alert">
        <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>

<div class="row align-self-stretch">
    <div class="col-xl-6 mb-1">
        <div class="loginbox card m-1 newcustomer h-100">
            <?php if(!$this->getHookVar('register_box_html')){ ?>
            <div class="card-body d-flex flex-wrap">
                    <h4 class="mb-3 text-nowrap w-100"><?php echo $text_i_am_new_customer; ?></h4>
                    <h6 class="mb-3 w-100"><?php echo $text_checkout; ?></h6>
                    <?php
                    $formCss = "mt-auto mb-0 w-100";
                    $form1['form_open']->style .= $formCss;
                    echo $form1['form_open']; ?>
                    <fieldset class="w-100">
                        <div class="form-group mb-3">
                        <?php echo $form1[ 'register' ];?>
                        </div>
                    <?php if ($guest_checkout) { ?>
                        <div class="form-group mb-3">
                            <?php echo $form1[ 'guest' ];?>
                        </div>
                    <?php } ?>
                    </fieldset>
                    <div class="d-flex align-items-start">
                        <div class="mt-2">
                            <p class="m-2"><?php echo $text_create_account; ?></p>
                        </div>
                        <button type="submit" class="ms-auto text-nowrap btn btn-primary align-self-end"  title="<?php echo $form1['continue']->name ?>">
                            <i class="<?php echo $form1['continue']->icon; ?> fa"></i>
                            <?php echo $form1['continue']->name ?>
                        </button>
                    </div>
                    </form>
                </div>
            <?php }else{
                echo $this->getHookVar('register_box_html');
            }?>
        </div>
    </div>
    <div class="col-xl-6 mb-1">
        <div class="loginbox card m-1 returncustomer h-100">
        <?php if(!$this->getHookVar('login_box_html')){ ?>
        <div class="card-body d-flex flex-wrap">
                <h4 class="mb-3 text-nowrap w-100"><?php echo $text_returning_customer; ?></h4>
                <h6 class="mb-3 w-100"><?php echo $text_i_am_returning_customer; ?></h6>
                <?php
                $form2['form_open']->style .= $formCss;
                echo str_replace('novalidate','',$form2['form_open']); ?>
                    <fieldset class="w-100">
                        <div class="form-floating mb-3">
                            <?php
                            $form2['loginname']->no_wrapper = true;
                            $form2['loginname']->attr .= ' autocomplete="username email" required  aria-required="true"';
                            echo $form2['loginname'];
                            ?>
                            <label for="<?php echo $form2['loginname']->element_id; ?>">
                            <?php
                                echo $noemaillogin ? $entry_loginname : $entry_email_address;
                            ?>
                            </label>
                        </div>
                        <div class="form-floating mb-3">
                            <?php
                            $form2['password']->no_wrapper = true;
                            $form2['password']->attr .= ' autocomplete="current-password" aria-required="true" required';
                            echo $form2['password']?>
                            <label for="<?php echo $form2['password']->element_id; ?>"><?php echo $entry_password; ?></label>
                        </div>
                    </fieldset>
                    <div class="d-flex w-100 align-items-center">
                        <div id="rescue_links me-2 d-flex align-items-start">
                            <a class="text-nowrap me-2" href="<?php echo $forgotten_pass; ?>"><?php echo $text_forgotten_password; ?></a>
                            <?php if($noemaillogin) { ?>
                                <a class="text-nowrap me-auto" href="<?php echo $forgotten_login; ?>"><?php echo $text_forgotten_login; ?></a>
                            <?php } ?>
                        </div>
                        <button type="submit" class="ms-auto text-nowrap btn btn-primary"  title="<?php echo $form2['login_submit']->name ?>">
                            <i class="<?php echo $form2['login_submit']->{'icon'}; ?>"></i>
                            <?php echo $form2['login_submit']->name ?>
                        </button>
                    </div>
                </form>
            </div>
        <?php }else{
            echo $this->getHookVar('login_box_html');
        }?>
        <?php echo $this->getHookVar('login_extension'); ?>
        </div>
    </div>
</div>