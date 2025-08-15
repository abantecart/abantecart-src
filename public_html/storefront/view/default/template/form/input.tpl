<?php if(!$no_wrapper){?>
    <div class="input-group h-100">
<?php }
if($icon){?>
    <div class="input-group-text"><?php echo $icon; ?></div>
<?php }?>
    <input type="<?php echo $type ?>"
           name="<?php echo $name ?>"
           id="<?php echo $id ?>"
           value="<?php echo $value ?>"
           placeholder="<?php echo $placeholder ?>"
           class="form-control <?php echo $style; ?>" <?php
         echo $attr;
         echo $regexp_pattern ? ' pattern="'.htmlspecialchars($regexp_pattern, ENT_QUOTES, 'UTF-8').'"':'';
         echo $error_text ? ' title="'.htmlspecialchars($error_text, ENT_QUOTES, 'UTF-8').'"':'';
         if ( $required ) { echo ' required'; }
         if($list){ echo ' list="'.$id.'_list"'; }
         ?>/>
    <?php
        if($list){ ?>
        <datalist id="<?php echo $id.'_list'?>">
            <?php foreach((array)$list as $l) {
                echo '<option value="' . htmlspecialchars($l, ENT_QUOTES, 'UTF-8') . '">';
            }?>
        </datalist>
        <?php }
    if($type=='password'){ ?>
        <div class="input-group-text">
            <a href="Javascript:void(0);"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
        </div>
        <script type="application/javascript">
            $(document).ready(function() {
                const pwdwrp = $("#<?php echo $id ?>").parent();
                const pwd = $('#<?php echo $id ?>');
                pwdwrp.find("a").on('click', function(event) {
                    event.preventDefault();
                    if(pwd.attr("type") === "text"){
                        pwd.attr('type', 'password').focus();
                        pwdwrp.find('i')
                            .addClass( "fa-eye-slash" )
                            .removeClass( "fa-eye" );
                    }else if($('#<?php echo $id ?>').attr("type") === "password"){
                        pwd.attr('type', 'text').focus();
                        pwdwrp.find('i')
                            .removeClass( "fa-eye-slash" )
                            .addClass( "fa-eye" );
                    }
                });
            });</script>
    <?php }

    if ( $required ) { ?>
        <span class="input-group-text text-danger">*</span>
    <?php }
    if($type == 'password'){ ?>
        <div class="pwdhelp text-dark fw-bold form-text d-none w-100">
            <?php echo $this->language->get('warning_capslock')?>
        </div>
    <?php }
    if(!$no_wrapper){?>
    </div>
<?php } ?>
