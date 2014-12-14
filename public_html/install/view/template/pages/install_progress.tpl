<?php echo $header; ?>
<div class="row">
	<div class="col-md-12">
	<div class="panel panel-default">

	<ul class="nav nav-tabs" role="tablist">
	  <li class="disabled"><a href="#" onclick="return false;">1: License</a></li>
	  <li class="disabled"><a href="#" onclick="return false;">2: Compatibility Validation</a></li>
	  <li class="disabled"><a href="#" onclick="return false;">3: Configuration</a></li>
	  <li class="active"><a href="#" onclick="return false;">4: Data Load</a></li>
	  <li class="disabled"><a href="#" onclick="return false;">5: Finished</a></li>
	</ul>

	<div class="panel-heading">
	<h2><i class="fa fa-gear fa-spin fa-fw"></i> Data Configuration <small>Provide setting below</small></h2>
	</div>
	
	<div class="panel-body panel-body-nopadding">
	
		<?php if (!empty($error[ 'warning' ])) { ?>
		<div class="warning alert alert-error alert-danger"><?php echo $error[ 'warning' ]; ?></div>
		<?php } ?>
		<div id="error" style="display: none;" class="warning alert alert-error alert-danger"></div>
	
		<label class="h5 heading">Saving settings and configuring AbanteCart database...</label>

		<div id="progressbar" class="progress">
		  <div class="progress-bar progress-bar-striped active"  role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
		    <span class="process_info">0% Complete</span>
		  </div>
		</div>

	</div>
	
	<div class="panel-footer">
		<div class="form-inline form-group">
		<div id="process_skip" style="text-align: center; display:none;">
			<a class="btn btn-default button" href="Javascript:void(0);">
			Continue and skip data load <i class="fa fa-arrow-right fa-fw"></i>
			</a>
		</div>
		<div id="process_back" style="text-align: center; display:none;">
			<a class="btn btn-default" onclick="window.history.back()"><i class="fa fa-arrow-left"></i> Back</a>
		</div>
		</div>
	</div>
	
	</div>
	
	</div>
	
</div>

<?php echo $progressbar_scripts; ?>

<script type="text/javascript">
    var messages = { 
    	3:'Writing configuration file ...',
        4:'Loading demo data ... ',
        5:'Installing Default Language. This might take a moment ... ',
        error:'<b>Following error has occurred during installation:</b></br>'
    };
    var stop = false;
    function install(step) {
        $.ajax({
        	type:'POST',
            dataType:'JSON',
            url:'<?php echo $url; ?>&runlevel=' + step,
            success:function (response) {
                if (response.ret_code == 50) {
                    next_step = 3;
                    update_progress(20);    
                    updateProgressbarText(messages[next_step]);
                    if (!stop) {
                    	//run next step
                        install(next_step); 
                    }
                } else if (response.ret_code == 100) {
                    next_step = 4;
					//next 2 steps can be skipped
                    $('#process_skip').show();
                    update_progress(40);    
                    updateProgressbarText(messages[next_step]);
                    //run next step
                    install(next_step);
                } else if (response.ret_code == 150) {
                    next_step = 5;
                    update_progress(60);
                    updateProgressbarText(messages[next_step]);
                    //run next step
                    install(next_step);
                } else if (response.ret_code == 200) {
                	//all done need to wait for splited background process to finish
                	start_asynchronous_work();
                } else {
                    showError(response);
                }
            },
            error:function (jqXHR, exception) {
                showError(jqXHR.statusText + ": " + jqXHR.responseText);
            }
        });
    }
    //update progress bar percent
    function updateProgressbar(percent) {
    	$("#progressbar .progress-bar").attr('aria-valuenow', percent);
    	$("#progressbar .progress-bar").css( "width", percent+"%" );
	}    
    //update progress bar percent
    function updateProgressbarText(text) {
    	$("#progressbar .process_info").html(text);
	}    
    // progressbar js-wrappers
    function progressbarError(text) {
        showError(text);
    }
    function progressbarFinish() {
        window.location = '<?php echo $redirect; ?>';
    }
    function showError(response) {
        $('#progressbar').hide();
        $('#process_skip').hide();
        $('#process_back').show();
        $('#error').show();
        var text = messages.error;
        text += response;
        $('#error').html(text);        
    }

    $('#process_skip .button').click( function () {
		progressbar_skip = true;
		progressbarFinish();

	});

	//start the process 
    $(document).ready(function () {
    	startBar();
        install(2);
    });

</script>
<?php echo $footer; ?>