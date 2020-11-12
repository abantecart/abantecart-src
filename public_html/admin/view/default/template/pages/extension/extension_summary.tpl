<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title"><?php echo $extension_info['name']; ?></h4>
    </div>
    <div class="panel-body panel-body-nopadding">
        <div class="row">
            <div class="col-md-1">
                <img class="extension-summary-icon" src="<?php echo $extension_info['icon'] ?>" alt="<?php echo $extension['name'] ?>"/>
            </div>
            <?php if ($extension_info['version']) { ?>
                <div class="col-md-1"><?php echo $text_version . ': <br/><b>' . $extension_info['version']; ?></b></div>
            <?php }
            if ($extension_info['installed']) { ?>
                <div class="col-md-2"><?php echo $text_installed_on . ': <br/><b>' . $extension_info['installed']; ?></b></div>
            <?php
            }
            //Licence key if present
            //expiration date if present
            if ($extension_info['support_expiration'] && $extension_info['support_expiration_int'] !== 0) {
                if($extension_info['support_expiration_int']<time()){ ?>
                    <div class="col-md-2"><b><?php echo $text_support_expired; ?></b></div><?php
                }else{ ?>
                <div class="col-md-2"><?php echo $text_support_expiration
                        . ':<br/><b>' . $extension_info['support_expiration']; ?></b></div>
                <?php }?>
            <?php
            }
            if ( $upgrade_button ) { ?>
                <div class="col-md-1"><a class="btn btn-primary" href="<?php echo $upgrade_button->href ?>"><?php echo $upgrade_button->text ?></a></div>
            <?php }
            if($extension_info['license']){ ?>
            <div class="col-md-2"><?php echo $text_license . ': <br/><b>' . $extension_info['license']; ?></b></div>
            <?php }
            if($get_support_button){ ?>
            <div class="col-md-2"><?php echo $get_support_button; ?></div>
            <?php } ?>
            <?php echo $this->getHookVar('extension_summary_item'); ?>
        </div>
    </div>
</div>