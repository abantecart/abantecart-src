<?php if ( !$page['restricted'] || $allow_clone) { ?>
<?php echo $change_layout_form; ?>
<div class="layout_controls">
	<?php echo $change_layout_select; ?>
	<button class="btn btn_standard" type="submit"><?php echo $change_layout_button; ?></button>
	<div id="layout_hidden_fields"><?php echo $form_hidden; ?></div>
</div>
</form>
<?php } ?>

<?php echo $form_begin; ?>
<div class="page_wrapper">
	<div id="header_block">
		<div id="header_top_block">
			<?php
			if ($header_boxes) {
				foreach ($header_boxes as $selectbox) {
					echo '<div class="section">' . $selectbox . '</div>';
				}
			} ?>
			<div class="block_buttons" style="margin-right: 10px;">
				<a onclick="createBlock(1)" class="btn_standard"><?php echo $header_create_block; ?></a>
			</div>
		</div>
		<div id="header_bottom_block">
			<?php
			if ($header_bottom) {
				foreach ($header_bottom as $selectbox) {
					echo '<div class="section">' . $selectbox . '</div>';
				}
			} ?>
			<div class="block_buttons">
				<a onclick="createBlock(2)" class="btn_standard"><?php echo $header_bottom_create_block; ?></a>
				<a onclick="addBlock('blocks[2][children][]');"
				   class="btn_standard"><?php echo $header_bottom_addbox; ?></a>
			</div>
		</div>
	</div>

	<div id="main_content_block">
		<div id="left_block" class="<?php echo $main_left_status == '0' ? 'block_off' : ''; ?>">
			<div class="block_status align_right">
				<?php echo $main_left_statusbox; ?>
			</div>
			<?php
			if ($main_left_boxes) {
				foreach ($main_left_boxes as $selectbox) {
					echo '<div class="section">' . $selectbox . '</div>';
				}
			}?>
			<div class="block_buttons">
				<a onclick="createBlock(3)" class="btn_standard"><?php echo $main_left_create_block; ?></a>
				<a onclick="addBlock('blocks[3][children][]');"
				   class="btn_standard"><?php echo $main_left_addbox; ?></a>
			</div>
		</div>

		<div id="right_block" class="<?php echo $main_right_status == '0' ? 'block_off' : ''; ?>">
			<div class="block_status align_right">
				<?php echo $main_right_statusbox; ?>
			</div>
			<?php
			if ($main_right_boxes) {
				foreach ($main_right_boxes as $selectbox) {
					echo '<div class="section">' . $selectbox . '</div>';
				}
			}?>
			<div class="block_buttons">
				<a onclick="createBlock(6)" class="btn_standard"><?php echo $main_left_create_block; ?></a>
				<a onclick="addBlock('blocks[6][children][]');"
				   class="btn_standard"><?php echo $main_right_addbox; ?></a>
			</div>
		</div>

		<div id="content_block">
			<div id="content_top_block">
				<?php
				if ($main_top_boxes) {
					foreach ($main_top_boxes as $selectbox) {
						echo '<div class="section">' . $selectbox . '</div>';
					}
				} ?>

				<div class="block_buttons">
					<a onclick="createBlock(4)" class="btn_standard"><?php echo $main_top_create_block; ?></a>
					<a onclick="addBlock('blocks[4][children][]');"
					   class="btn_standard"><?php echo $main_top_addbox; ?></a>
				</div>
			</div>

			<div id="content_center_block"><?php echo $page['content']; ?></div>

			<div id="content_bottom_block">
				<?php
				if ($main_bottom_boxes) {
					foreach ($main_bottom_boxes as $selectbox) {
						echo '<div class="section">' . $selectbox . '</div>';
					}
				} ?>
				<div class="block_buttons">
					<a onclick="createBlock(5)" class="btn_standard"><?php echo $main_bottom_create_block; ?></a>
					<a onclick="addBlock('blocks[5][children][]');"
					   class="btn_standard"><?php echo $main_bottom_addbox; ?></a>
				</div>
			</div>
		</div>

		<div class="clr_both"></div>
	</div>

	<div id="footer_block">
		<div id="footer_top_block">
			<?php
			if ($footer_top) {
				foreach ($footer_top as $selectbox) {
					echo '<div class="section">' . $selectbox . '</div>';
				}
			} ?>
			<div class="clr_both"></div>
			<div class="block_buttons">
				<a onclick="createBlock(7)" class="btn_standard"><?php echo $footer_top_create_block; ?></a>
				<a onclick="addBlock('blocks[7][children][]');"
				   class="btn_standard"><?php echo $footer_top_addbox; ?></a>
			</div>
		</div>
		<div id="footer_bottom_block">
			<?php
			if ($footer_boxes) {
				foreach ($footer_boxes as $selectbox) {
					echo '<div class="section">' . $selectbox . '</div>';
				}
			} ?>
			<div class="block_buttons" style="margin-right: 10px;">
				<a onclick="createBlock(8)" class="btn_standard"><?php echo $footer_create_block; ?></a>
			</div>
			<div class="clr_both"></div>
		</div>
	</div>
	<br/>

	<div class="buttons align_center">
		<a class="btn_standard" href="javascript:history.go(0);"><?php echo $form_reset; ?></a>
		<button type="submit" class="btn_standard"><?php echo $form_submit; ?></button>
	</div>

</div>
<div id="layout_hidden_fields"><?php echo $form_hidden; ?></div>
</form>
<script type="text/javascript"><!--
	jQuery(function ($) {

		$('.block_selector').on('change', function () {
			var block_id = $(this).val();
			var elm = $(this).parents('.aform');
			destroy_popover(elm);
			if (!block_id) {
				return false;
			}
			load_popover(elm, block_id);
			return true;
		});

		$('.block_selector').parents('.aform').mouseover(function (curr) {
			var that = $(this).find('select');
			var block_id = $(that).val();
			var elm = $(that).parents('.aform');
			if (!block_id) {
				return false;
			}
			$('.popover').hide();
			load_popover(elm, block_id);
			return true;
		});

	});

	var load_popover = function(elm, block_id) {
		var template = '';
		var page_id = '';
		var layout_id = '';
		if ($('#layout_template').val()) {
			template = $('#layout_template').val();
		}
		if ($('input[name="page_id"]').val()) {
			page_id = $('input[name="page_id"]').val();
		}
		if ($('input[name="layout_id"]').val()) {
			layout_id = $('input[name="layout_id"]').val();
		}

		if (elm.attr('data-toggle') !== 'undefined' && elm.attr('data-toggle') == 'popover') {
			elm.popover('show');
			return true;
		}

		$.ajax({
			url: '<?php echo $block_info_url; ?>',
			type: 'GET',
			dataType: 'json',
			data: 'block_id=' + block_id + '&template=' + template + '&page_id=' + page_id + '&layout_id=' + layout_id,
			success: function (data) {
				build_popover(elm, data);
			},
			error: function (error) {
				alert(error.responseText);
			}
		});
	}

	var build_popover = function(elm, data) {
		elm.attr('data-toggle', 'popover');
		elm.popover({
			trigger: 'manual',
			html: true,
			placement: 'top',
			delay: { show: 100, hide: 900 },
			title: data.title,
			content: function () {
				var html = data.description;
				if (data.allow_edit == 'true') {
					html += '<br/><center>' + data.block_edit_brn + '</center>';
				}
				return html;
			}
		}).click(function (e) {
					e.preventDefault();
				}).on("mouseenter",function () {
					var _this = this;
					$(this).popover("show");
					$(this).siblings(".popover").on("mouseleave", function () {
						$(_this).popover('hide');
					});
				}).on("mouseleave", function () {
					var _this = this;
					setTimeout(function () {
						if (!$(".popover:hover").length) {
							$(_this).popover("hide")
						}
					}, 200);
				});
		elm.popover('show');
	}

	var destroy_popover = function(elm) {
		elm.removeAttr('data-placement');
		elm.removeAttr('data-toggle');
		elm.popover('destroy');
		elm.data('popover', false);
	}


	var createBlock = function(parent_block_id) {
		var url = '<?php echo $new_block_url;?>&parent_block_id=';
		this.window.location = url + parent_block_id;
	}
	--></script>