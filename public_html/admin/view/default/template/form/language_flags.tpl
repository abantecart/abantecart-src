<?php
if ($languages) {
    foreach ($languages as $language) {
        $style = $language['code'] == $language_code ? 'class="flag selected"' : 'class="flag"';   ?>
    <div class="language_box" style="margin:0;">
        <a <?php echo $style?> code="<?php echo $language['code']; ?>"
                               title="<?php echo $language['name']; ?>">
            <img src="<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>"/>
        </a>
    </div>
    <?php }
} ?>