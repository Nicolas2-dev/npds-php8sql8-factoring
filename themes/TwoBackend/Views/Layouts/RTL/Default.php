<!DOCTYPE html>
<html lang="<?= Config::get('app.locale'); ?>">
<head>
    <meta charset="utf-8">
    <title><?= isset($title) ? $title : __d('two_backend', 'Page'); ?> - <?= Config::get('app.name'); ?></title>
<?php

echo Asset::render('css', array(
    'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',
    'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
    asset_url('css/style.css', 'themes/two-backend'),
));

echo Asset::position('header', 'css');

echo Asset::position('header', 'js');

?>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

<div class="container">
    <?= $content; ?>
</div>

<footer class="footer">
    <div class="container-fluid">
        <div class="row" style="margin: 15px 0 0;">
            <div class="col-lg-12">
                <p class="text-muted pull-right">
                    <small><!-- DO NOT DELETE! - Statistics --></small>
                </p>
            </div>
        </div>
    </div>
</footer>

<?php

echo Asset::render('js', array(
    'https://code.jquery.com/jquery-1.12.4.min.js',
    'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'
));

echo Asset::position('footer', 'js');

?>

</body>
</html>
