<?php echo $form['form_open'];
    //NOTE: do not use enctype="multipart/form-data" for form tag here!
    foreach($form['fields'] as $field){
        echo $field;
    } ?>
    <div class="form-group action-buttons">
        <div class="col-md-12">
            <button class="btn btn-primary lock-on-click"
                    title="<?php echo_html2view($form['submit']->name); ?>"
                    type="submit">
                <i class="fa fa-check"></i>
                <?php echo $form['submit']->name; ?>
            </button>
        </div>
    </div>
</form>