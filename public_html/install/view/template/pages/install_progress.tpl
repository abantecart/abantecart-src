<?php echo $header; ?>
<div id="stepbar">
    <div class="tl">
        <div class="tr">
            <div class="tc"></div>
        </div>
    </div>
    <div class="cl">
        <div class="cr">
            <div class="cc">
                <div class="heading">Installation Steps:</div>
                <div class="step">1: License</div>
                <div class="step">2: Compatibility Check</div>
                <div class="step_current">3: Configuration</div>
                <div class="step">4: Data Load</div>
                <div class="step">5: Finished</div>
            </div>
        </div>
    </div>
    <div class="bl">
        <div class="br">
            <div class="bc"></div>
        </div>
    </div>
</div>
<?php if (!empty($error[ 'warning' ])) { ?>
<div class="warning"><?php echo $error[ 'warning' ]; ?></div>
<?php } ?>

<div class="main_content">
    <div class="contentBox">
        <div class="cbox_tl">
            <div class="cbox_tr">
                <div class="cbox_tc">
                    <div class="heading">
                        Configuration
                    </div>
                </div>
            </div>
        </div>
        <div class="cbox_cl">
            <div class="cbox_cr">
                <div class="cbox_cc">

                    <p id="hint">Configuring Your AbanteCart database...</p>

                    <div class="section">
                        <table width="100%">
                            <tr id="progress">
                                <td width="100%" align="center">
                                    <div id="progressbar"></div>
                                    <div id="process_skip" style="align: center; display:none;">
                                        <a class="button" href="Javascript:void(0);">
                                            <img src="<?php echo $template_dir; ?>image/skip_button.png"
                                                 alt="Continue and skip data load" border="0"/>
                                        </a>
                                    </div>
                                    <div id="process_info"></div>
                                </td>
                            </tr>
                            <tr id="error" style="display: none;">
                                <td class="warning" width="100%"></td>
                            </tr>
                        </table>
                    </div>

                </div>
            </div>
        </div>
        <div class="cbox_bl">
            <div class="cbox_br">
                <div class="cbox_bc"></div>
            </div>
        </div>
    </div>
</div>

<?php echo $progressbar_scripts; ?>

<script language="JavaScript">
    var messages = { 3:'Writing configuration file ...',
        4:'Installing Default Language. This might take a moment ... ',
        error:'Sorry, but error occurred during installation:</br>'
    };
    var step = 2;
    var stop = false;
    function install() {

        $.ajax({    type:'POST',
            dataType:'JSON',
            url:'<?php echo $url; ?>&runlevel=' + step,
            success:function (response) {
                if (response.ret_code == 50) {
                    step = 3;
                    $('#hint').html(messages[step]);
                    if (!stop) {
                        install();
                    }
                } else if (response.ret_code == 100) {
                    step = 4;
                    $('#hint').html(messages[step]);
                    install();
                } else if (response.ret_code == 150) {
                    $('#process_skip').show();
                    startBar();
                } else {
                    showError(response);
                }
            },
            error:function (jqXHR, exception) {
                showError(jqXHR.statusText + ": " + jqXHR.responseText);
            }
        });
    }
    // progressbar js-wrappers
    function progressbarError(text) {
        showError(text);
    }
    function progressbarFinish() {
        window.location = '<?php echo $redirect; ?>';
    }
    function showError(response) {
        $('#process_info, #progressbar').hide();
        $('#error').show();
        var text = messages.error;
        text += response;
        $('#hint').html('Error');
        $('#error > td').html(text);
    }

    $('#process_skip .button').click(
            function () {
                progressbar_skip = true;
                progressbarFinish();

            }
    );

    $(function () {
        $("#progressbar").progressbar({
            value:5
        });
    });


    $(document).ready(function () {
        install();
    });

</script>
<?php echo $footer; ?>