<?php
echo $this->html->buildElement(
		array(
				'type' => 'modal',
				'id' => 'tip_modal',
				'modal_type' => 'lg',
				'title' => $text_tip,
				'content' => $tip_content, //html from common/tips.xml
				'footer' => '<button type="button" class="btn btn-default" data-dismiss="modal">
							    			 <i class="fa fa-times"></i> '.$text_close.'</button>'
		));
?>
<script type="application/javascript">
	$(window).load(function(){
		$('#tip_modal').modal('show');
	});
</script>

