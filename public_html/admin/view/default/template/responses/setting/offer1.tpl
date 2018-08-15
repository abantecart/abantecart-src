<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="offer1_title" class="modal-header">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
	<h4 class="modal-title"></h4>
</div>

<div id="setting_form" class="tab-content">
	<?php echo $form['form_open']; ?>
	<div id="offer1_content" class="panel-body panel-body-nopadding">
		<script type="application/javascript">
			try{
				$(document).ready(function(){
					$.get('<?php echo $offer1_url?>', function(data){
						if(data.length<1){
							$('#offer1_next').click();
							return false;
						}
						$('#offer1_content').html(data.html);
						$('#offer1_title h4').html(data.title);
					}, 'JSON'
					).error(function() {
						$('#offer1_next').click();
					});
				});
			}catch (e) {
				//go to next step
				$('#offer1_next').click();
			}
		</script>

	</div>
	<div class="panel-footer">
		<div class="row">
		   <div class="center">
		    <?php if ($back) { ?>
			<div class="btn-group">
			    <a class="btn btn-white step_back" href="<?php echo $back; ?>">
			        <i class="fa fa-arrow-left"></i> <?php echo $button_back; ?>
			    </a>
			</div>		    
		    <?php } ?>
			<button id="offer1_next" class="btn btn-primary">
				<i class="fa fa-save"></i> <?php echo $text_next; ?> <i class="fa fa-arrow-right"></i>
			</button>
		   </div>
		</div>
	</div>
	</form>
</div>

<?php include('quick_start_js.tpl'); ?>