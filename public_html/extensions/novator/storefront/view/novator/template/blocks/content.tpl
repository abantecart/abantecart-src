<div class="px-3 list-group list-group-flush">
    <h1 class="h2 heading-title"><?php echo $heading_title; ?></h1>
    <?php echo $this->getHookVar('pre_contents');
    foreach ($contents as $content) { ?>
       <a class="list-group-item list-group-item-action" href="<?php echo $content['href']; ?>"><?php echo $content['title']; ?></a>
	<?php }
    echo $this->getHookVar('post_contents'); ?>
    <a class="list-group-item list-group-item-action" href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a>
    <a class="list-group-item list-group-item-action" href="<?php echo $sitemap; ?>"><?php echo $text_sitemap; ?></a>
</div>