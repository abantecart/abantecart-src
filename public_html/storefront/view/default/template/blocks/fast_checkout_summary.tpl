<div id="fast_checkout_summary_block" class=" mt-3"></div>

<script type="application/javascript">
	showLoading = function (modal_body) {
		modal_body.html('<div class="modal_loader" style="text-align: center"><i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i></div>');
	}

    <?php if ($summaryUrl) { ?>
	let loadFCBlockSummaryContent = function (urlQuery = '') {
		if ($('#fast_checkout_summary_block').html() == '') {
			//showLoading($('#fast_checkout_summary_block'))
		}
		$.ajax({
			url: '<?php echo $summaryUrl; ?>'+ urlQuery,
			type: 'GET',
			dataType: 'html',
			success: function (data) {
				$('#fast_checkout_summary_block').hide().html(data).fadeIn(1000)
                if(urlQuery.length>0){
                    loadPage();
                }
			}
		});
	}
    $(document).ready(function(){
        $('#fast_checkout_summary_block').on('reload', loadFCBlockSummaryContent);
        loadFCBlockSummaryContent();
    });
    <?php } ?>
</script>
