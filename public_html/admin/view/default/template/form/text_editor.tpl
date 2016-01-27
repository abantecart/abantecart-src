<?php
//unique id
$wrapper_id = randomWord(6);
?>
<div id="<?php echo $wrapper_id ?>" class="text-editor">
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active">
			<a href="#text_<?php echo $wrapper_id; ?>"
			   aria-controls="text_<?php echo $wrapper_id; ?>"
			   role="tab"
			   data-toggle="tab"><?php echo $tab_text; ?></a>
		</li>
		<li role="presentation">
			<a href="#visual_<?php echo $wrapper_id; ?>"
			   aria-controls="visual_<?php echo $wrapper_id; ?>"
			   role="tab"
			   data-toggle="tab"><?php echo $tab_visual; ?></a>
		</li>
	</ul>

	<div class="common_content_actions pull-right">
		<div class="btn-group">
			<a title="<?php js_echo($button_add_media); ?>"
				data-original-title="<?php js_echo($button_add_media); ?>"
                href="#"
                class="add_media btn btn-white tooltips">
				<i class="fa fa-music fa-lg"> <?php echo $button_add_media; ?></i>
			</a>
		</div>
	</div>
	<!-- Tab panes -->
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="text_<?php echo $wrapper_id; ?>">
			<div class="toolbar text-toolbar col-xs-12">
				<div class="primary_content_actions pull-left">
					<div class="btn-group mr10">
						<a title=""
						   href="#"
						   class="btn btn-primary tooltips" data-original-title="">
							<i class="fa fa-plus"></i>
						</a>
					</div>
				</div>
			</div>
	        <textarea
			    class="form-control atext <?php echo $style ?>"
			    name="<?php echo $name ?>"
			    placeholder="<?php echo $placeholder; ?>"
			    id="<?php echo $id ?>"
			    data-orgvalue="<?php echo $ovalue ?>"
			    <?php echo $attr ?>
			    ><?php echo $value ?></textarea>

		</div>

		<div role="tabpanel" class="tab-pane" id="visual_<?php echo $wrapper_id ?>">
	        <textarea
			    name="<?php echo $name ?>"
			    disabled="disabled"
			    placeholder="<?php echo $placeholder; ?>"
			    id="text_editor_<?php echo $id ?>"
			    ><?php echo $value ?>
	        </textarea>
		</div>
		<?php if ($required == 'Y' || $multilingual){ ?>
			<span class="input-group-addon">
        <?php if ($required == 'Y'){ ?>
		    <span class="required">*</span>
	    <?php } ?>

				<?php if ($multilingual){ ?>
					<span class="multilingual"><i class="fa fa-flag"></i></span>
				<?php } ?>

        </span>
		<?php } ?>
	</div>

</div>


<script type="application/javascript">
	if (typeof tinymce == 'undefined') {
		var include = '<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/tinymce/tinymce.min.js"><\/script>';
		document.write(include);
	}
	$(document).ready(function () {
		tinymce.baseURL = "<?php echo $template_dir; ?>javascript/tinymce";
		var mcei = {
			theme: "modern",
			skin: "lightgray",
			language: "ru",
			formats: {
				alignleft: [{
					selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li",
					styles: {textAlign: "left"}
				}, {selector: "img,table,dl.wp-caption", classes: "alignleft"}],
				aligncenter: [{
					selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li",
					styles: {textAlign: "center"}
				}, {selector: "img,table,dl.wp-caption", classes: "aligncenter"}],
				alignright: [{
					selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li",
					styles: {textAlign: "right"}
				}, {selector: "img,table,dl.wp-caption", classes: "alignright"}],
				strikethrough: {inline: "del"}
			},
			relative_urls: false,
			remove_script_host: false,
			convert_urls: false,
			browser_spellcheck: true,
			fix_list_elements: true,
			entities: "38,amp,60,lt,62,gt",
			entity_encoding: "raw",
			keep_styles: false,
			cache_suffix: "abc-mce-433-20160114",
			preview_styles: "font-family font-size font-weight font-style text-decoration text-transform",
			end_container_on_empty_block: true,
			editimage_disable_captions: false,
			editimage_html5_captions: true,
			plugins: "charmap,colorpicker,hr,lists,media,paste,tabfocus,textcolor,fullscreen,link", //,wpautoresize,wpeditimage,wpemoji,wpgallery,wpdialogs,textpattern,wpview",
			//  content_css: "http://abolabo.hopto.org/wordpress/wp-includes/css/dashicons.min.css?ver=4.3.2,http://abolabo.hopto.org/wordpress/wp-includes/js/tinymce/skins/wordpress/wp-content.css?ver=4.3.2,https://fonts.googleapis.com/css?family=Noto+Sans%3A400italic%2C700italic%2C400%2C700%7CNoto+Serif%3A400italic%2C700italic%2C400%2C700%7CInconsolata%3A400%2C700&subset=latin%2Clatin-ext,http://abolabo.hopto.org/wordpress/wp-content/themes/twentyfifteen/css/editor-style.css,http://abolabo.hopto.org/wordpress/wp-content/themes/twentyfifteen/genericons/genericons.css",
			selector: '',
			resize: true,
			menubar: false,
			autop: true,
			indent: false,
			toolbar1: "undo,redo, bold,italic,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,spellchecker,dfw",
			toolbar2: "",
			//toolbar2: "formatselect,underline,alignjustify,forecolor,pastetext,removeformat,charmap,outdent,indent",
			toolbar3: "",
			toolbar4: "",
			tabfocus_elements: "content-html,save-post",
			body_class: "content post-type-post post-status-auto-draft post-format-standard locale-en-gb",
			autoresize_on: true,
			add_unload_trigger: false
		};

		$('#<?php echo $wrapper_id; ?> a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			var newtab_id = $(e.target).attr('aria-controls'), // newly activated tab
		        prevtab_id = $(e.relatedTarget).attr('aria-controls'); // previous active tab

			var textarea, value;
			textarea = $('#'+prevtab_id+ ' textarea');

			if(prevtab_id=='visual_<?php echo $wrapper_id?>'){
				$('#'+newtab_id+ ' textarea')
						.val( tinyMCE.activeEditor.getContent() )
						.removeAttr('disabled');
			}else{
				$('#'+newtab_id+ ' textarea')
						.val(textarea.val())
						.removeAttr('disabled');
				mcei.selector = 'textarea#'+$('#'+newtab_id+ ' textarea').attr('id');
				tinymce.init(mcei);
				if(tinyMCE.activeEditor!=null) {
					tinyMCE.activeEditor.setContent(textarea.val());
				}
			}

			//block previous textarea
			textarea.attr('disabled','disabled');
		});

		//event for addmedia button
		$('#<?php echo $wrapper_id; ?> a.add_media').on('click',function(){
			//get data container
			var id = $("#<?php echo $wrapper_id ?> ul.nav-tabs li.active>a").attr('aria-controls');
			var editor, cursorPosition;
			if( id == 'text_<?php echo $wrapper_id ?>'){
				editor = $('#<?php echo $id ?>');
				cursorPosition = editor.getCursorPosition();
			}else{
				editor = tinyMCE.activeEditor;
				cursorPosition = null;
			}
			openTextEditRLModal(editor,cursorPosition);
			return false;
		});
	});






</script>
