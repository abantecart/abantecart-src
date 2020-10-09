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
            if ($extension_info['date_added']) { ?>
                <div class="col-md-2"><?php echo $text_date_added . ': <br/><b>' . $extension_info['date_added']; ?></b></div>
            <?php
            }
            //Licence key if present
            if ($extension_info['license']) { ?>
                <div class="col-md-2"><?php echo $text_license . ': <br/><b>' . $extension_info['license']; ?></b></div>
            <?php
                //expiration date if present
                if ($extension_info['license_expires']) { ?>
                    <div class="col-md-2"><?php echo $text_license_expires
                            . ': '. $get_support_button .'<br/><b>' . $extension_info['license_expires']; ?></b></div>
                <?php
                }
            }
            if ( $upgrade_button ) { ?>
                <div><a class="btn btn-primary" href="<?php echo $upgrade_button->href ?>"><?php echo $upgrade_button->text ?></a></div>
            <?php } ?>
            <?php echo $this->getHookVar('extension_summary_item'); ?>
        </div>
    </div>
</div>