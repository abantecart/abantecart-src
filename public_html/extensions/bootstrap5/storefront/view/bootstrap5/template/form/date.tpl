<div class="input-group">
    <input type="<?php echo $type ?>" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value; ?>" <?php echo $attr; ?> class="form-control <?php echo $style; ?>" <?php if ( $required ) { echo 'required'; }?>/>
    <?php if ( $required ) { ?>
    <span class="input-group-text text-danger">*</span>
    <?php } ?>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#<?php echo $id ?>').datepicker({dateFormat: <?php js_echo($dateformat) ?> });
            <?php if ( $highlight == 'pased' ) : ?>
            var startdate = $('#<?php echo $id ?>').val();
            if ((new Date(startdate).getTime() < new Date().getTime())) {
                $('#<?php echo $id ?>').closest('.afield').addClass('focus');
            }
            <?php endif; ?>
            <?php if ( $highlight == 'future' ){ ?>
            var startdate = $('#<?php echo $id ?>').val();
            if ((new Date(startdate).getTime() > new Date().getTime())) {
                $('#<?php echo $id ?>').closest('.afield').addClass('focus');
            }
            <?php } ?>
        });
    </script>
</div>