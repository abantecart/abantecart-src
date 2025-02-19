<div class="store-hours mt-3">
    <div class="px-3 container-fluid container-xl">
        <h2 class="h2 heading-title"><?php echo $heading_title; ?></h2>
    </div>
    <div class="container-fluid container-xl">
<?php
if ($store_hours) {
    foreach ($store_hours as $weekDay => $hours) {
?>
        <div class="row align-items-start mt-3">
            <div class="col-6 text-black"><?php echo $hours['text']; ?>:</div>
            <?php if ($hours['open'] && $hours['closed']) { ?>
                <div class="col-6">
                     <?php echo $hours['open']; ?> : <?php echo $hours['closed']; ?>
                </div>
            <?php } else { ?>
                <div class="col-6"><?php echo $closed_text ?></div>
            <?php }  ?>
        </div>
<?php
	}
}
?>
		</div>
</div>