<div id="newslettersignup" class="newsletter-block pt-4">
    <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <h4 class="text-white text-center"><?php echo $heading_title; ?></h4>
                    <p class="text-light small text-center mb-4"><?php echo $text_signup; ?></p>
                    <div class="pull-right">
                        <?php echo $form_open;?>
                            <div class="input-group">
                                <?php foreach($form_fields as $field_name=>$field_value){?>
                                <input type="hidden" name="<?php echo $field_name?>" value="<?php echo $field_value; ?>">
                                <?php } ?>
                                <input class="form-control rounded text-white shadow-none border-0" type="text" placeholder="<?php echo $text_subscribe; ?>" name="email" id="appendedInputButton">
                                <span class="input-group-btn">
                                    <button class="btn btn-warning ms-2 rounded" type="submit"><?php echo $button_subscribe;?></button>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    </div>
</div>