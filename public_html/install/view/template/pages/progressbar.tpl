<script type="text/javascript">
<?php
/* to use this js you need js-wrappers:
1. progressbarFinish(); function will call when progress become 100%
2. progressbarStateError();  function will call when failed attempt of progress status more then 9
3. progressbarError(); function for handle error of long process
*/
?>

    var progressbar_percent;
    var asynchronous_percent;
    var progressbar_total;
    var func_id;
    var progressbar_skip = false;

    function startBar() {
        progressbar_percent = 0;
    }

    function start_asynchronous_work() {
        $.ajax({
            async:false,
            url:'<?php echo $url?>',
            type:"GET",
            data:"work=max",
            dataType:'json',
            success:function (response) {
                progressbar_total = response.total;
            }
        });
		asynchronous_percent = 0;
        run_asynchronous_work();
        update_asynchronous_progress();
    }

    function update_progress( percent ){
		progressbar_percent = percent;
		updateProgressbar(percent);
	}

    function run_asynchronous_work() {
        $.ajax({
            url:'<?php echo $url?>',
            type:"GET",
            data:"work=do",
            dataType:'JSON',
            success:function (response) {
                if (response.status != 100) {
                    progressbarError(response.errorText);
                    $.ajaxQ.abortAll();
                } else {
                    progressbarFinish();
                }
            },
            error:function (jqXHR, exception) {
                if (!progressbar_skip) {
                    var text = jqXHR.statusText + ": " + jqXHR.responseText;
                    text = jqXHR.responseText.length == 0 ? 'Connection failed.' : text;
                    progressbarError(text);
                    $.ajaxQ.abortAll();
                }
            }
        });
    }

    var pgb_state_error = 0; // errors count of progress status requests
    function update_asynchronous_progress() {
        if (pgb_state_error > 9) {
            progressbarStateError();
            $.ajaxQ.abortAll();
            return;
        }

        if (parseInt(asynchronous_percent) < parseInt(progressbar_total)) {
            $.ajax({
                url:'<?php echo $url?>',
                type:"GET",
                data:"work=progress",
                dataType:'json',
                success:function (response) {
                    if (response.prc == 'undefined') {
                        pgb_state_error++;
                        return;
                    }
                    asynchronous_percent = response.prc;
                    //culculate percent for asynchronous relatevely to main processbar percent
                    progressbar_percent = progressbar_percent + Math.floor(asynchronous_percent * progressbar_percent / progressbar_total)
                    update_progress (progressbar_percent);    
                    if (parseInt(asynchronous_percent) < parseInt(progressbar_total)) {
                        func_id = setTimeout("update_asynchronous_progress()", 100);
                    }
                },
                error:function () {
                    if (!progressbar_skip) {
                        pgb_state_error++;
                    }
                }
            });

        }
    }
    // all ajax calls stopper
    $.ajaxQ = (function () {
        var id = 0, Q = {};
        $(document).ajaxSend(function (e, jqx) {
            jqx._id = ++id;
            Q[jqx._id] = jqx;
        });
        $(document).ajaxComplete(function (e, jqx) {
            delete Q[jqx._id];
        });
        return {
            abortAll:function () {
                var r = [];
                $.each(Q, function (i, jqx) {
                    r.push(jqx._id);
                    jqx.abort();
                });
                return r;
            }
        };
    })();
</script>
