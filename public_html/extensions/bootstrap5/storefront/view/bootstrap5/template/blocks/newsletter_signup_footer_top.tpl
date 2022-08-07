<section id="newslettersignup footer-top" class="m-3 w-25">
    <h4><?php echo $heading_title; ?></h4>
    <div class="newsletter small lh-lg"><?php echo $text_signup; ?></div>
    <div class="pull-right">
        <?php echo $form_open;?>
            <div class="input-group">
                <?php foreach($form_fields as $field_name=>$field_value){?>
                <input type="hidden" name="<?php echo $field_name?>" value="<?php echo $field_value; ?>">
                <?php } ?>
                <input type="text" placeholder="<?php echo $text_subscribe; ?>" name="email" id="appendedInputButton" class="form-control">
                <span class="input-group-btn">
                    <button class="btn btn-primary" type="submit"><?php echo $button_subscribe;?></button>
                </span>
            </div>
        </form>
    </div>
</section>