<div class="modal-content description-history">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <h4 class="modal-title"><?php echo $title; ?></h4>
  </div>
  
  <div class="tab-content">
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<div class="form-group">
		<?php
		if ($result) {
			$tmp = '';
			foreach ($result as $k => $row) {
                ?>
            <dl class="dl-horizontal">
                <dt><?php echo dateISO2Display(
                        $row['date_added'],
                        $this->language->get('date_format_short').' '.$this->language->get('time_format')
                    ); ?></dt>
                <dd id="dd_<?php echo $row['hist_id']?>">
                    <?php
                    echo $this->html->buildElement(
                            [
                                'type' => 'textarea',
                                'name' => 'hist_area_'.$row['hist_id'],
                                'value' => html_entity_decode($row['text']),
                            ]
                    );
                    ?>

                    <a id="hist_<?php echo $row['hist_id']?>" class="btn btn-default pull-right mt10">grab value!</a>
                </dd>
            </dl>
                <hr>
        <?php }
        }
		?>
		</div>
  	</div>
  	
	<div class="panel-footer col-xs-12">
		<div class="center">
			<button class="btn btn-default" data-dismiss="modal">
				<i class="fa fa-times fa-fw"></i> <?php echo $text_close; ?>
			</button>
		</div>
	</div>
  	
  </div>
</div>

<script type="application/javascript">
    $(document).on('click','.description-history a.btn',function(){
        const values = <?php echo json_encode(array_column($result,'text','hist_id'))?>;
        let id = $(this).attr('id').replace('hist_','');
        let setValue = values[id];
        const dest = $('#<?php echo $elm_id?>');
        if(!dest){
            alert('Cannot find destination form-element (#<?php echo $elm_id?>)!');
            return false;
        }
        try{
            let mceeditor = tinymce.get('text_editor_<?php echo $elm_id?>');
            mceeditor.setContent(setValue);
        }catch(e){}
        dest.val(setValue).change();
        $('#hist_modal').modal('hide');
    });
</script>