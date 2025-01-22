<?php if ($search_form) { ?>
    <form id="<?php echo $search_form['form_open']->name; ?>"
          method="<?php echo $search_form['form_open']->method; ?>"
          name="<?php echo $search_form['form_open']->name; ?>" class="pull-left form-inline" role="form">
        <?php
        foreach ($search_form['fields'] as $f) { ?>
            <div class="btn-group">
                <?php echo $f; ?>
            </div>
            <?php
        }
        ?>
        <div class="btn-group">
            <button type="submit" class="btn btn-primary tooltips" title="<?php echo $button_filter; ?>">
                <?php echo $search_form['submit']->text ?>
            </button>
        </div>
        <div class="btn-group">
            <button type="reset" class="btn btn-default tooltips" title="<?php echo $button_reset; ?>">
                <i class="fa fa-refresh"></i>
            </button>
        </div>
    </form>
<?php   } ?>