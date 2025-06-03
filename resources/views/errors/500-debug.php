<?php extend('layouts.auth') ?>
<?php section('title') ?>500 서버 오류<?php end_section() ?>
<?php section('content') ?>
<div class="no-debug-wrapper">
    <div class="no-debug-header">
        <div class="no-debug-title"><?= escape($exception) ?></div>
        <div class="no-debug-message"><?= escape($message) ?></div>
    </div>

    <div class="no-debug-location">
        <span><?= escape($file) ?></span>
        <span class="no-debug-line">Line <?= escape((string)$line) ?></span>
    </div>

    <div class="no-debug-snippet">
        <?php foreach ($snippet as $codeLine): ?>
            <div class="no-code-line<?= $codeLine['highlight'] ? ' active' : '' ?>">
                <span class="no-line-num"><?= $codeLine['number'] ?></span>
                <span class="no-code"><?= htmlentities($codeLine['code']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="no-debug-trace">
        <?php
        $lines = explode("\n", $trace);
        foreach ($lines as $i => $traceLine):
            $highlight = strpos($traceLine, $file) !== false ? ' highlight' : '';
        ?>
            <div class="no-trace-line<?= $highlight ?>">
                <span class="no-trace-num"><?= $i + 1 ?>.</span>
                <span class="no-trace-text"><?= htmlentities($traceLine) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php end_section() ?>
