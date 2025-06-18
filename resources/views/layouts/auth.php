
<!DOCTYPE html>
<html lang="<?= get_locale() ?>" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= csrf_meta() ?>
    <title><?= yield_section('title') ?? env('APP_NAME') ?></title>
    
    <link rel="apple-touch-icon" sizes="180x180" href="<?=asset_path('favicon/apple-touch-icon.png')?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?=asset_path('favicon/favicon-32x32.png')?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=asset_path('favicon/favicon-16x16.png')?>">
    <link rel="manifest" href="<?=asset_path('favicon/site.webmanifest')?>">
    
    <link rel="stylesheet" href="<?=asset_path('lib/fontawesome/css/all.min.css')?>"/>
    <link rel="stylesheet" href="<?=asset_path('css/admin.min.css?v='.time())?>"/>
    
    <?= yield_section('style') ?>
</head>
<body 
    data-controller="<?=yield_section('controller')?>" 
    data-action="<?=yield_section('action')?>"
>
    <main class="no-auth">
        <?= yield_section('content') ?>
    </main>
    
    <script src="<?=asset_path('js/admin.min.js?v='.time())?>"></script>
    <?= yield_section('script') ?>
</body>
</html>