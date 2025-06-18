
<!DOCTYPE html>
<html lang="<?= get_locale() ?>" data-theme="<?= cookie()->get('theme') ?? 'auto' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= csrf_meta() ?>
    <title><?= yield_section('title') ?? env('APP_NAME') ?></title>

    <link rel="apple-touch-icon" sizes="180x180" href="<?=asset_path('favicon/apple-touch-icon.png')?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?=asset_path('favicon/favicon-32x32.png')?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=asset_path('favicon/favicon-16x16.png')?>">
    <link rel="manifest" href="<?=asset_path('favicon/site.webmanifest')?>">
    
    
    <link href="<?=asset_path('lib/fontawesome/css/all.min.css')?>" rel="stylesheet" />
    <link href="<?=asset_path('lib/flatpickr/flatpickr.min.css')?>" rel="stylesheet" />
    <link href="<?=asset_path('css/admin.min.css?v='.time())?>" rel="stylesheet" />
    
    <?= yield_section('style') ?>
</head>
<body 
    data-controller="<?=yield_section('controller')?>" 
    data-action="<?=yield_section('action')?>"
>
    <!-- Header -->
    <?= include_view('admin.components.header') ?>

    <div class="no-root">
        <div class="no-root-inner no-container-3xl">
            <!-- Drawer -->
            <?= include_view('admin.components.drawer') ?>

            <main class="no-page">
                <div class="no-page-inner">
                    <!-- Contents -->
                    <?= yield_section('content') ?>
                </div>
            </main>

        </div>
        <!-- Root Inner -->
    </div>
    <!-- Root -->
    
    <script src="<?=asset_path('lib/flatpickr/flatpickr.min.js')?>"></script>
    <script src="<?=asset_path('lib/flatpickr/ko.js')?>"></script>

    <script src="<?=asset_path('js/admin.min.js?v='.time())?>"></script>
    <?= yield_section('script') ?>
</body>
</html>