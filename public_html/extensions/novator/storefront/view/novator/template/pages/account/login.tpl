

<div class="row title justify-content-center sec-heading-block text-center">
    <div class="col-xl-8">
        <h1 class="h2 heading-title mb-0">
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

<div class="row">
    <div class="col-md-6 p-3 newcustomer">
        <?php if(!$this->getHookVar('register_box_html')){ ?>
        <div class="loginbox card m-2">
            <div class="card-body">
                <h4 class="mb-3 text-nowrap w-100"><?php echo $text_i_am_new_customer; ?></h4>
                <h6 class="mb-3"><?php echo $text_checkout; ?></h6>
                <?php echo $form1[ 'form_open' ]; ?>
                <fieldset>
                    <div class="form-group mb-3">
                    <?php echo $form1[ 'register' ];?>
                    </div>
                <?php if ($guest_checkout) { ?>
                    <div class="form-group mb-3">
                        <?php echo $form1[ 'guest' ];?>
                    </div>
                <?php } ?>
                    <div class="d-flex align-items-start">
                        <div class="mt-2">
                            <p><?php echo $text_create_account; ?></p>
                        </div>
                        <button type="submit" class="float-end text-nowrap btn btn-primary"  title="<?php echo $form1['continue']->name ?>">
                            <i class="<?php echo $form1['continue']->icon; ?> fa"></i>
                            <?php echo $form1['continue']->name ?>
                        </button>
                    </div>
                </fieldset>
                </form>
            </div>
        </div>
        <?php }else{
            echo $this->getHookVar('register_box_html');
        }?>
    </div>

    <div class="col-md-6 p-3 returncustomer">
        <?php if(!$this->getHookVar('login_box_html')){ ?>
        <div class="loginbox card m-2">
            <div class="card-body">
                <h4 class="mb-3 text-nowrap w-100"><?php echo $text_returning_customer; ?></h4>
                <h6><?php echo $text_i_am_returning_customer; ?></h6>
                <?php echo $form2['form_open']; ?>
                    <fieldset >
                        <div class="form-floating mb-3">
                            <?php
                                $form2['loginname']->no_wrapper = true;
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
                            echo $form2['password']?>
                            <label for="<?php echo $form2['password']->element_id; ?>"><?php echo $entry_password; ?></label>
                        </div>
                        <div class="d-flex align-items-center">
                            <div id="rescue_links me-2 d-flex align-items-start">
                                <a class="text-nowrap me-2" href="<?php echo $forgotten_pass; ?>"><?php echo $text_forgotten_password; ?></a>
                                <?php if($noemaillogin) { ?>
                                    <a class="text-nowrap me-auto" href="<?php echo $forgotten_login; ?>"><?php echo $text_forgotten_login; ?></a>
                                <?php } ?>
                            </div>
                            <button type="submit" class="ms-auto float-end text-nowrap btn btn-primary"  title="<?php echo $form2['login_submit']->name ?>">
                                <i class="<?php echo $form2['login_submit']->{'icon'}; ?>"></i>
                                <?php echo $form2['login_submit']->name ?>
                            </button>
                        </div>
                    </fieldset>
                </form>
            </div>    
        </div>
        <?php }else{
            echo $this->getHookVar('login_box_html');
        }?>
    <?php echo $this->getHookVar('login_extension'); ?>
    </div>
</div>