<?php
$active_object = '';
$active_library = '';
if ($action == 'list_object') {
	$active_object = 'class="active"';
} else {
	$active_library = 'class="active"';
}
?>
	<div id="rl_container" data-current-url="<?php echo $full_url; ?>">
		<ul class="nav nav-tabs nav-justified nav-profile">
			<?php if (has_value($object_id)) { ?>
				<li id="object" data-rl-id="<?php echo $resource_id; ?>"
					data-type="<?php echo $type; ?>" <?php echo $active_object; ?>>
					<a class="ellipsis" href="#"><strong><i class="fa fa-bookmark fa-fw"></i> <?php echo $object_title." (".$object_name.")"; ?></strong></a>
				</li>
			<?php } ?>
			<li id="library" data-rl-id="<?php echo $resource_id; ?>"
				data-type="<?php echo $type; ?>" <?php echo $active_library; ?>>
				<a class="ellipsis" href="#"><span><i class="fa fa-book fa-fw"></i> <?php echo $heading_title; ?></span></a>
			</li>
		</ul>

		<?php
		$txt_link_resource = sprintf($this->language->get('text_map_to'),$object_title);
		$txt_unlink_resource = sprintf($this->language->get('text_unmap_from'),$object_title);
		?>

		<div class="tab-content rl-content">

			<ul class="reslibrary-options">
				<li>
					<form id="<?php echo $search_form->name; ?>" name="<?php echo $search_form->name; ?>"
						  action="<?php echo $current_url; ?>" class="form-inline" role="form">
						<div class="form-group">
							<div class="input-group input-group-sm">
								<?php echo $rl_types; ?>
							</div>
						</div>
						<div class="form-group">
							<div class="input-group input-group-sm">
								<?php echo $search_field_input; ?>
							</div>
						</div>
						<div class="form-group">
							<button class="btn btn-xs btn-primary btn_search"
									type="submit"><?php echo $button_go; ?></button>
						</div>
					</form>
				</li>
			<?php if($mode!='single'){?>
				<li>
					<div class="ckbox ckbox-default">
						<input type="checkbox" value="1" id="rl_selectall">
						<label for="rl_selectall"><?php echo $text_select_all; ?></label>
					</div>
				</li>
			<?php }?>
				<li>
					<a id="add_resource" data-type="<?php echo $type; ?>"
					   class="btn btn-xs btn-default add_resource tooltips"
					   data-original-title="<?php echo $button_add; ?>"><i class="fa fa-plus"></i></a>
				</li>
				<?php if (has_value($active_object)) { ?>
					<li>
						<a class="actionitem disabled rl_save_multiple" onclick="return false;" href="#"><i
									class="fa fa-save"></i></a>
					</li>
				<?php }
			if($mode!='single'){

				if($action=='list_object'){ ?>
					<li><a class="actionitem disabled rl_unlink_multiple tooltips" onclick="return false;" href="#"
						   data-original-title="<?php echo $txt_unlink_resource; ?>"><i class="fa fa-unlink"></i></a></li>
				<?php }else{ ?>
					<li><a class="actionitem disabled rl_link_multiple tooltips" onclick="return false;" href="#"
						   data-original-title="<?php echo $txt_link_resource; ?>"><i class="fa fa-link"></i></a></li>
				<?php } ?>
				<li>
					<a class="actionitem disabled rl_delete_multiple"
					   onclick="multi_action('delete'); return false;"
					   href="#"
					   data-confirmation="delete"
					   data-confirmation-text="<?php echo $text_confirm_delete; ?>">
						<i class="fa fa-trash-o"></i></a>
				</li>
				<?php
			}
				if ($form_language_switch) { ?>
					<li>
						<?php echo $form_language_switch; ?>
					</li>
				<?php } ?>
				<?php if (!empty ($help_url)) { ?>
					<li>
						<a class="btn btn-white btn-xs tooltips" href="<?php echo $help_url; ?>" target="new" title=""
						   data-original-title="Help">
							<i class="fa fa-question-circle fa-lg"></i>
						</a>
					</li>
				<?php } ?>
			</ul>

			<div class="row">
				<div class="col-sm-12">
					<div class="row reslibrary">
						<?php
						if (!is_array($rls) || !count($rls)) {
						?>
							<div class="col-md-12 text-center"><?php echo $text_no_results; ?></div>
						<?php
						} else {
						//list RL items
						foreach ($rls as $rl) {
							/*
							  [resource_id] =>
							  [name] =>
							  [title] =>
							  [description] =>
							  [resource_path] =>
							  [resource_code] =>
							  [mapped] => 1
							  [sort_order] => 0
							  [thumbnail_url] =>
							  [url] =>
							  [relative_url] =>
							  [mapped_to_current] =>
							  */
							?>
							<div class="col-xs-6 col-sm-2 col-md-2 document">
								<div class="thmb <?php if ($rl['mapped_to_current']) { echo "mapped"; } ?>"	data-rl-id="<?php echo $rl['resource_id']; ?>">
									<div class="ckbox ckbox-default" style="display: none;">
										<input class="checksign" type="checkbox" value="<?php echo $rl['resource_id']; ?>" id="check_<?php echo $rl['resource_id']; ?>">
										<label for="check<?php echo $rl['resource_id']; ?>"></label>
										<?php if (has_value($active_object)) { ?>
											<div class="rl_sort_order center ml10 mt10" title="sort order">
												<input type="text"
													   class="form-control input-sm"
													   placeholder="sort order"
													   size="5"
													   name="sort_order[<?php echo $rl['resource_id']; ?>]"
													   value="<?php echo $rl['sort_order']; ?>"/>
											</div>
										<?php } ?>
									</div>
									<div class="btn-group rl-group" style="display: none;">
										<button data-toggle="dropdown" class="btn btn-default dropdown-toggle rl-toggle" type="button"><span class="caret"></span></button>
										<ul role="menu" class="dropdown-menu rl-menu"
											data-rl-id="<?php echo $rl['resource_id']; ?>">
											<li><a class="resource_edit"
												   data-mode="<?php echo $mode; ?>"
												   data-rl-id="<?php echo $rl['resource_id']; ?>"
												   data-type="<?php echo $type; ?>"
												   href="#" onclick="return false;"><i class="fa fa-pencil"></i><?php echo $text_edit;?></a></li>
										<?php if( $mode!='single' ){?>
											<li>
												<?php if($action=='list_object' || $rl['mapped_to_current']){?>
												<a class="rl_unlink"
												   data-rl-id="<?php echo $rl['resource_id']; ?>"
												   data-type="<?php echo $type; ?>"
												   href="#" onclick="return false;"><i class="fa fa-link"></i><?php echo $txt_unlink_resource; ?></a>
												<?php }else{?>
													<a class="rl_link"
													   data-rl-id="<?php echo $rl['resource_id']; ?>"
													   data-type="<?php echo $type; ?>"
													   href="#" onclick="return false;"><i class="fa fa-link"></i><?php echo $txt_link_resource; ?></a>
												<?php } ?>
											</li>
										<?php } ?>
											<li><a class="rl_download"
												   data-rl-id="<?php echo $rl['resource_id']; ?>"
												   data-type="<?php echo $type; ?>"
												   href="#" onclick="return false;"><i class="fa fa-download"></i><?php echo $button_download;?></a></li>
										</ul>
									</div>
									<?php if ($rl['resource_code']) { ?>
										<div class="thmb-prev thmb-icon">
											<i class="fa fa-code fa-3"></i>
										</div>
									<?php } else { ?>
										<div class="thmb-prev">
											<a class="resource_edit tooltips"
											   data-mode="<?php echo $mode; ?>"
											   data-type="<?php echo $type; ?>"
											   data-rl-id="<?php echo $rl['resource_id']; ?>"
											   data-original-title="<?php echo $rl['name']; ?>" href="#">
												<img alt="" class="img-responsive"
													 src="<?php echo $rl['thumbnail_url']; ?>">
											</a>
										</div>
									<?php } ?>
									<h5 class="rl-title"><a class="resource_edit tooltips ellipsis"
															data-mode="<?php echo $mode; ?>"
															data-type="<?php echo $type; ?>"
															data-rl-id="<?php echo $rl['resource_id']; ?>"
															data-original-title="<?php echo $rl['name']; ?>"
															href="#"><?php echo $rl['name']; ?></a></h5>
									<?php if ($rl['date_added']) { ?>
										<small class="text-muted">Added: <?php echo $rl['date_added']; ?></small>
									<?php } ?>
								</div>
							</div>
						<?php
						}
						}
						?>

					</div>
				</div>
				<!-- col-sm-12 -->

				<?php if ($pagination_bootstrap) { ?>
					<div class="col-sm-12 rl_pagination">
						<div class="row">
							<div class="col-sm-1 form-inline">
								<div class="form-group">
									<div class="input-group input-group-sm dropup tooltips"
										 data-original-title="<?php echo $text_sort_order; ?>">
										<button data-toggle="dropdown" class="btn btn-default dropdown-toggle"
												type="button">
											<i class="fa fa-sort"></i>
										</button>
										<ul id="rl_list_sorting" role="menu" class="dropdown-menu">
											<li><a href="<?php echo $no_sort_url; ?>&sort=date_added&order=DESC"><i
															class="fa fa-sort-amount-desc"></i> <?php $text_sorting_date_desc; ?>
												</a></li>
											<li><a href="<?php echo $no_sort_url; ?>&sort=date_added&order=ASC"><i
															class="fa fa-sort-amount-asc"></i> <?php $text_sorting_date_asc; ?>
												</a></li>
											<li><a href="<?php echo $no_sort_url; ?>&sort=name&order=ASC"><i
															class="fa fa-sort-alpha-asc"></i> <?php $text_sorting_name_asc; ?>
												</a></li>
											<li><a href="<?php echo $no_sort_url; ?>&sort=name&order=DESC"><i
															class="fa fa-sort-alpha-desc"></i> <?php $text_sorting_name_desc; ?>
												</a></li>
											<?php if($action=='list_object'){ ?>
											<li><a href="<?php echo $no_sort_url; ?>&sort=sort_order&order=ASC"><i
															class="fa fa-sort-numeric-asc"></i> <?php $text_sorting_asc; ?>
												</a></li>
											<li><a href="<?php echo $no_sort_url; ?>&sort=sort_order&order=DESC"><i
															class="fa fa-sort-numeric-desc"></i> <?php $text_sorting_desc; ?>
												</a></li>
											<?php } ?>
										</ul>
									</div>
								</div>
							</div>
							<div class="col-sm-11 center form-inline pull-right">
								<?php echo $pagination_bootstrap; ?>
							</div>
						</div>
					</div>
				<?php } ?>


			</div>

		</div>

	</div><!-- <div class="tab-content"> -->

	</div>
