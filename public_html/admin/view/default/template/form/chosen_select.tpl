<?php //NOTE: For multivalue, need to pass attribute multiple="multiple" ?>
<select id="<?php echo $id ?>" name="<?php echo $name ?>" data-placeholder="<?php echo_html2view($placeholder); ?>"
		class="chosen-select form-control aselect <?php echo ($style ?: ''); ?>"
        data-orgvalue="<?php echo_html2view(implode(',',$value)); ?>"
		<?php
        echo $attr;
        echo str_contains((string)$attr,'multiple') ? 'style="display: none;"' : '' ?>>
<?php
if(is_array($options)){
	foreach ( $options as $v => $text ) {
	$check_id = preg_replace('/[^a-zA-Z0-9_]/', '', $id . $v);
	//special case for chosen
	if( is_array($text) ) {
		$text = $text['name'];
	}
?>
    <option id="<?php echo $check_id ?>" value="<?php echo $v ?>" <?php echo (in_array($v, $value) ? ' selected="selected" ':'') ?>
            data-orgvalue="<?php echo (in_array($v, $value) ? 'true':'false') ?>"
            <?php echo (in_array($v, (array)$disabled_options) ? ' disabled="disabled" ':''); ?>>
        <?php echo $text ?>
    </option>
<?php }
} ?>
</select>

<?php if ( $required || $help_url ) { ?>
	<div class="input-group-addon">
        <?php if ( $required) { ?>
            <span class="required">*</span>
        <?php }
        if ($help_url) { ?>
        <span class="help_element">
            <a href="<?php echo $help_url; ?>" target="new"><i class="fa fa-question-circle fa-lg"></i></a>
        </span>
	<?php } ?>
	</div>
<?php }
//for chosen we populate HTML into options
if(str_contains((string)$style,'chosen')) { ?>
<script type="text/javascript">
    $(document).ready(function () {
        <?php
        if(is_array($options)){
            foreach ( $options as $v => $text ) {
                if (is_array($text)) {
                    $check_id = preg_replace('/[^a-zA-Z0-9_]/', '', $id . $v);
                    $img = $text['image'];
                    if($text['url']){
                        $img = '<a title="'.html2view($text['name']).'" onclick="Javascript: window.open(\''. $text['url'].'\');">'. $text['image'].'</a>';
                    }
                ?>

                $('#<?php echo $check_id ?>').html( <?php js_echo($img); ?>);
                $('#<?php echo $check_id ?>').append('<span class="hide_text"> <?php js_echo($text['name']); ?></span>');
        <?php
                }
            }
        } ?>
        let $select = $("#<?php echo $id ?>").chosen({'width':'100%'});
        <?php if($sortable){ ?>
        let $chosenContainer = $select.next('.chosen-container');
        let $choicesList = $chosenContainer.find('.chosen-choices');
        $choicesList.sortable({
            items: 'li.search-choice',
            placeholder: 'ui-state-highlight search-choice',
            forcePlaceholderSize: true,
            tolerance: 'pointer',

            stop: function(event, ui) {
                let reorderedValues = [];
                $choicesList.find('li.search-choice').each(function() {
                    let optionIndex = $(this).find('.search-choice-close').data('option-array-index');
                    let value = $select.find('option').eq(optionIndex).val();
                    reorderedValues.push(value);
                });
                reorderSelectOptions($select, reorderedValues);
            }
        });
        function reorderSelectOptions($select, valueOrder) {
            let $options = $select.find('option');
            $options.sort(function(a, b) {
                let indexA = valueOrder.indexOf($(a).val());
                let indexB = valueOrder.indexOf($(b).val());
                if (indexA === -1) return 1;
                if (indexB === -1) return -1;

                return indexA - indexB;
            });
            $select.empty().append($options).trigger('chosen:updated');
        }
        <?php } ?>
    });

</script>
<?php }
//for chosen we populate data from ajax
if ( $ajax_url ) { ?>
<!-- Ajax Product Sector with Chosen (MultiValue lookup element) -->
<script type="text/javascript">
	$(document).ready(function () {
		$("#<?php echo $id ?>").ajaxChosen(
            {
                type: 'POST',
                url: '<?php echo $ajax_url; ?>',
                dataType: 'json',
                jsonTermKey: "term",
                data: {
                    'exclude': $("#<?php echo $id ?>").chosen().val(),
                    'filter': '<?php echo $filter_params; ?>'
                },
                keepTypingMsg: "<?php echo $text_continue_typing; ?>",
                lookingForMsg: "<?php echo $text_looking_for; ?>",
                minTermLength: 2
            },
            function (data) {
                var results = [];
                $.each(data, function (i, val) {
                    var html = '', css = '';
                    if (val.hasOwnProperty('image')) {
                        let img = val.image;
                        if (val.hasOwnProperty('url')) {
                            img = '<a title="' + val.name + '" onclick="Javascript: window.open(\'' + val.url + '\');">' + img +'</a>';
                        }
                        html += img;
                        css = 'hide_text';
                    }
                    html += '<span class="' + css + '"> ' + val.name;
                    if (val.meta) {
                        html += '&nbsp;(' + val.meta + ')';
                    }
                    html += '</span>';
                    <?php // process custom html-attributes for "option"-tag
                    $oa = '';
                    if ($option_attr) {
                        $i = 0;
                        $k = [];
                        foreach ($option_attr as $attr_name) {
                            $k[] = "'".$i."': {name: '".$attr_name."', value: val.".$attr_name." }";
                            $i++;
                        }
                        $oa = implode(', ', $k);
                    } ?>
                    results.push({value: val.id, text: html, option_attr: {<?php echo $oa;?>}});
                });
                return results;
            }
        );
	});
</script>
<?php } ?>