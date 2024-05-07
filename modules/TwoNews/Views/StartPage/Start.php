<?php if ($central) { ?>
    <?= $central; ?>
<?php } else { ?>
    <?php if (isset($edito)) { ?>
        <?= $edito; ?>
    <?php } ?>

    <?php if (isset($news)) { ?>
        <?= $news; ?>
    <?php } ?>
<?php } ?>
