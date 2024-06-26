<script src="//cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.js"
        integrity="sha512-RCgrAvvoLpP7KVgTkTctrUdv7C6t7Un3p1iaoPr1++3pybCyCsCZZN7QEHMZTcJTmcJ7jzexTO+eFpHk4OCFAg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.css" integrity="sha512-YdYyWQf8AS4WSB0WWdc3FbQ3Ypdm0QCWD2k4hgfqbQbRCJBEgX0iAegkl2S1Evma5ImaVXLBeUkIlP6hQ1eYKQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<?php if(!$no_wrapper){?>
<div class="input-group h-100">
<?php } ?>
    <input type="<?php echo $type ?>" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value; ?>" <?php echo $attr; ?> class="form-control <?php echo $style; ?>" <?php if ( $required ) { echo 'required'; }?>/>
    <span class="input-group-text ">
    <?php echo $required ? '<i class="bi fa-birthday-cake "></i><span class="ms-3 text-danger">*</span>' : '<i class="bi fa-birthday-cake"></i>'; ?>
    </span>
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
<?php if(!$no_wrapper){?>
    </div>
<?php } ?>