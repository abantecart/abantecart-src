<script type="text/javascript">
<?php
    /* to use this js you need js-wrappers:
1. progressbarFinish(); function will call when progress become 100%
2. progressbarStateError();  function will call when failed attempt of progress status more then 9
3. progressbarError(); function for handle error of long process
*/
?>

    var progressbar_percent;
    var progressbar_total;
    var func_id;
    var progressbar_skip = false;

    function do_work() {
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

    function startBar() {
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

        do_work();
        progressbar_percent = 0;
        update_progress();
    }
    var pgb_state_error = 0; // errors count of progress status requests
    function update_progress() {
        if (pgb_state_error > 9) {
            progressbarStateError();
            $.ajaxQ.abortAll();
            return;
        }

        if (parseInt(progressbar_percent) < parseInt(progressbar_total)) {
            if (progressbar_percent == 0) {
                $("#progressbar").progressbar({
                    value:0
                });
            }
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
                    progressbar_percent = response.prc;
                    $("#progressbar").progressbar({
                        value:Math.floor(progressbar_percent * 100 / progressbar_total)
                    });

                    if (parseInt(progressbar_percent) < parseInt(progressbar_total)) {
                        func_id = setTimeout("update_progress()", 100);
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
