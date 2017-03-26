<?php /* Footer */ ?>
	<footer>
		<?php /* footer blocks placeholder */ ?>
		<section class="footersocial">
			<h4 class="hidden">&nbsp;</h4>

			<div class="container-fluid">
				<div class="col-md-3">
					<?php echo ${$children_blocks[0]}; ?>
				</div>
				<div class="col-md-3">
					<?php echo ${$children_blocks[1]}; ?>
				</div>
				<div class="col-md-3">
					<?php echo ${$children_blocks[2]}; ?>
				</div>
				<div class="col-md-3">
					<?php echo ${$children_blocks[3]}; ?>
				</div>
			</div>
		</section>

		<section class="footerlinks">
			<h2 class="hidden">&nbsp;</h2>

			<div class="container-fluid">
				<div class="pull-left">
					<?php echo ${$children_blocks[4]}; ?>
				</div>
				<div class="pull-right">
					<?php echo ${$children_blocks[5]}; ?>
				</div>
			</div>
		</section>

		<section class="copyrightbottom align_center">
			<h2 class="hidden">&nbsp;</h2>

			<div class="container-fluid">
				<div class="pull-left mt5">
					<?php echo ${$children_blocks[6]}; ?>
				</div>
				<div class="pull-right align_center">
					<?php echo $text_project_label ?>
					<br/>
					<?php echo $text_copy; ?>
				</div>
				<div class="pull-right mr20 mt5">
					<?php echo ${$children_blocks[7]}; ?>
				</div>
			</div>
		</section>
		<a id="gotop" href="#">Back to top</a>
	</footer>


	<div id="msgModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close callback-btn" data-dismiss="modal"
					        aria-hidden="true">&times;</button>
					<h3 class="hidden">&nbsp;</h3>
				</div>
				<div class="modal-body">
				</div>
			</div>
		</div>
	</div>
