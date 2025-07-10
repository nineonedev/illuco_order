<!DOCTYPE html>
<html lang="<?= get_locale() ?>">
<head>
    <meta charset="UTF-8">
    <title><?= yield_section('title') ?? env('APP_NAME') ?></title>
    <?= yield_section('style') ?>
</head>
<body>
    <?= yield_section('content') ?>
</body>
</html>
