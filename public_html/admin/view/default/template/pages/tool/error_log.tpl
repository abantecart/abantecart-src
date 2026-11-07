<?php
include($tpl_common_dir . 'action_confirm.tpl');
?>
<div class="tab-content">
    <div class="panel-heading">
        <div class="primary_content_actions pull-left">
            <?php
            if ($download_btn) { ?>
                <div class="input-group-prepend">
                    <?php
                    $download_btn->style = 'btn btn-info';
                    $download_btn->icon = 'fa fa-download';
                    echo $download_btn;
                    ?>
                </div>
                &nbsp;
                <?php 
            } ?>
                <div id="logfile_selector" class="btn-group mr10 toolbar">
                    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                        <i class="fa fa-file-text-o"></i>
                        <?php echo html2view($current_logfile); ?> <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                    <?php
                    $logfile_list = (array)$logfile_list;
                    //root directory files first
                    $rootFiles = (array)$logfile_list[''];
                    unset($logfile_list['']);
                    $html = '';
                    foreach ($rootFiles as $logfile => $fileTitle) {
                        $html .= '<li' . ($logfile == $current_logfile ? ' class="disabled"' : '') . '>'
                            . '<a href="' . $main_url . '&filename=' . urlencode($logfile) . '" title="' . html2view($fileTitle) . '">'
                            . html2view($fileTitle)
                            . '</a></li>';
                    }
                    //subdirectories with files
                    foreach ($logfile_list as $subDir => $files) {
                        $selectedChild = false;
                        $childrenList = '<ul class="dropdown-menu">';
                        foreach ((array)$files as $logfile => $fileTitle) {
                            $cssClass = '';
                            if ($logfile == $current_logfile) {
                                $cssClass = ' class="disabled"';
                                $selectedChild = true;
                            }
                            $childrenList .= '<li' . $cssClass . '>'
                                . '<a href="' . $main_url . '&filename=' . urlencode($logfile) . '" title="' . html2view($fileTitle) . '">'
                                . html2view($fileTitle)
                                . '</a></li>';
                        }
                        $childrenList .= '</ul>';
                        $html .= '<li class="dropdown-submenu' . ($selectedChild ? ' selected-parent' : '') . '">'
                            . '<a href="javascript:void(0)" class="d2d-dropdown"><i class="fa fa-folder-o"></i> '
                            . html2view(rtrim($subDir, '/'))
                            . '</a>'
                            . $childrenList
                            . '</li>';
                    }
                    echo $html; ?>
                    </ul>
                </div>
            <?php
            if ($clear_btn) { ?>
                <div class="btn-group">
                    <?php 
                    $clear_btn->style = 'btn btn-danger';
                    $clear_btn->icon = 'fa fa-trash';
                    echo $clear_btn;?>
                </div>
            <?php
            } ?>
        </div>
    </div>

    <div class="panel-body panel-body-nopadding">
        <div class="error-log">
            <?php
            if ($log) { ?>
                <table class="table table-striped">
                    <?php
                    foreach ($log as $line) { ?>
                        <tr>
                            <td><?php
                                echo nl2br($line); ?></td>
                        </tr>
                    <?php
                    } ?>
                </table>
            <?php
            } else { ?>
                <div class="text-center">
                    <h1><i class="fa fa-thumbs-o-up fa-lg"></i></h1>
                </div>
            <?php
            } ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('#download').on('click', function (e) {
        e.preventDefault();
        const filename = <?php echo json_encode((string)$current_logfile); ?>;
        if (filename) {
            location = '<?php echo $download_url; ?>&filename=' + encodeURIComponent(filename);
        } else {
            warning_alert('Please select a file to download');
        }
    });
</script>
