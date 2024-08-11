<section id="newslettersignup">
    <div class="newsletter small lh-lg text-light"><?php echo $text_signup; ?></div>
    <div>
        <?php echo $form_open;?>
            <div class="input-group">
                <?php foreach($form_fields as $field_name=>$field_value){?>
                <input type="hidden" name="<?php echo $field_name?>" value="<?php echo $field_value; ?>">
                <?php } ?>
                <input type="text" placeholder="<?php echo_html2view($text_subscribe); ?>" name="email" id="appendedInputButton" class="form-control">
                <span class="input-group-btn">
                    <button class="btn btn-primary" type="submit"><?php echo $button_subscribe;?></button>
                </span>
            </div>
        </form>
    </div>
</section>
