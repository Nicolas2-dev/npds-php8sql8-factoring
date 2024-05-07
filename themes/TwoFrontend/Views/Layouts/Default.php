<!DOCTYPE html>
<html lang="<?= isset($language) ? $language :Config::get('app.locale'); ?>">
<head>

    <title><?= isset($title) ? $title : __d('two_frontend', 'Page'); ?> - <?= Config::get('app.name'); ?></title>
    <?= $theme->head(false); ?>
    
<?php

?>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<?= $theme->header(); ?>

<?= $theme->rightBlock($pdst); ?>

<?php if(isset($admin_menu)) {
    echo $admin_menu;
} ?>

<?= $content; ?>

<?= $theme->leftBlock($pdst); ?>    

<?= $theme->footer(); ?>

</body>
</html>
