<ul class="nav nav-tabs nav-justified nav-profile">
    <?php
    $groups = array_merge($groups, (array) $additionalTabs);
    foreach ($groups as $name) { ?>
        <li <?php
        echo($active == $name ? 'class="active"' : (in_array($name, (array) $inactive) ? 'class="inactive"' : '')) ?>>
            <a href="<?php
            echo ${'link_'.$name}; ?>" title="<?php
            echo_html2view(${'title_'.$name}); ?>">
                <?php
                $text = ${'tab_'.$name};
                echo ($name == 'general' ? '<strong>' : '').
                    $text.($name == 'general' ? '</strong>' : ''); ?>
            </a>
        </li>
    <?php
    }
    echo $this->getHookVar('extension_tabs'); ?>
</ul>
