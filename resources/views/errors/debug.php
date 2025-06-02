<?php extend('layouts.auth') ?>
<?php section('title') ?>
500 서버 오류
<?php end_section() ?>
<?php section('content') ?>
<div class="no-section-xl no-auth-layout">
    <div class="no-error-debug">
        <h1 class="no-error-title">🚨 에러 발생!</h1>

        <div class="no-error-block">
            <strong>오류 메시지:</strong>
            <p><?= $message ?></p>
        </div>

        <div class="no-error-block">
            <strong>파일:</strong>
            <p><?= $file ?> <span class="error-line">Line <?= $line ?></span></p>
        </div>

        <div class="no-error-block">
            <strong>스택 트레이스:</strong>
        <div class="no-error-trace">
            <div class="no-error-trace__code">
            <?php
            $lines = explode("\n", $trace);
            foreach ($lines as $i => $traceLine) : 
                $highlight = strpos($traceLine, "$file") !== false ? ' highlight' : '';
            ?>
                <div class="trace-line <?=$highlight?>">
                    <span class="line-number">(<?=$i + 1?>)</span>
                <?=htmlentities($traceLine)?>
                </div>;
            <?php endforeach; ?>
            </div>
        </div>
        </div>
    </div>
</div>
<?php end_section() ?>
