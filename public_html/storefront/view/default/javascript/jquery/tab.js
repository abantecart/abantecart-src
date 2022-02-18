$.tabs = function(selector, start) {
	$(selector).each(function(i, element) {
		$($(element).attr('rel')).css('display', 'none');
		
		$(element).click(function() {
			$(selector).each(function(i, element) {
				$(element).removeClass('selected');
				
				$($(element).attr('rel')).css('display', 'none');
			});
			
			$(this).addClass('selected');
			
			$($(this).attr('rel')).css('display', 'block');
		});
	});
	
	if (!start) {
		start = $(selector + ':first').attr('rel');
	}

	$(selector + '[rel=\'' + start + '\']').trigger('click');
};