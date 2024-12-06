<div class="history-modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title"><?php echo $title; ?></h4>
    </div>

    <div class="modal-body">
        <div class="panel-body panel-body-nopadding tab-content col-xs-12">
            <div class="form-group">
            <?php
            if ($result) {
                $tmp = '';
                foreach ($result as $k => &$row) {
                    $row['text_formated'] = nl2br($row['text']);
                    $row['text'] = html_entity_decode($row['text']);
            ?>
                <div class="row">
                    <div class="col-md-3">
                        <?php echo dateISO2Display(
                            $row['date_added'],
                            $this->language->get('date_format_short').' '.$this->language->get('time_format')
                        ); ?>
                    </div>
                    <div class="col-md-8" id="dd_<?php echo $row['hist_id']?>">
                        <code class="code_view">
                        <?php
                            echo $row['text_formated'];
                        ?>
                        </code>
                    </div>
                    <div class="col-md-1">
                        <a id="hist_<?php echo $row['hist_id']?>" class="btn btn-default pull-right">
                            <i class="fa fa-check"></i>
                        </a>
                    </div>
                </div>
                <hr>
            <?php }
            } else { ?>
                <div class="row">
                    <div class="col-md-12 center">
                        <?php echo $text_no_results; ?>
                    </div>
                </div>
            <?php
            }
            ?>
            </div>
        </div>

    </div>

    <script type="application/javascript">
        $(document).off('click','#hist_modal .panel-body a.btn');
        $(document).on('click','#hist_modal .panel-body a.btn',function(){
            const values = <?php
                echo json_encode(
                    array_column($result,'text','hist_id')
                )?>;
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
            } catch(e){}
            dest.val(setValue).change();
            $('#hist_modal').modal('hide');
        });

        function destroyHistoryModal(){
			$('#hist_modal .history-modal-content').remove();
			$('#hist_modal .panel-body').off();
        }
    </script>
</div>