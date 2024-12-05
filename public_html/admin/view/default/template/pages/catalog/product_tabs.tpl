<ul class="nav nav-tabs nav-justified nav-profile">
    <?php
    $basicTabs = array_merge([
                                 'general',
                                 'images',
                                 'options',
                                 'files',
                                 'relations',
                                 'promotions',
                                 'layout',
                             ], $additionalTabs);
    foreach ($basicTabs as $name) {
        $cssClass = $active == $name ? 'active' : (in_array($name, (array) $inactive) ? 'inactive' : '');
        ?>
        <li class="<?php echo $cssClass; ?>">
            <a href="<?php echo ${'link_'.$name}; ?>" title="<?php echo_html2view(${'title_'.$name}); ?>">
            <?php
                $text = $name == 'images' ? $tab_media : ${'tab_'.$name};
                $text = $name == 'options' ? $tab_option : $text;
                echo ($name == 'general' ? '<strong>' : '').
                    $text.($name == 'general' ? '</strong>' : ''); ?>
            </a>
        </li>
    <?php
    }
    echo $this->getHookVar('extension_tabs'); ?>
</ul>
