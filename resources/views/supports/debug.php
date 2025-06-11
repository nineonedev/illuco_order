<?php
// ...위 코드 생략 (코드 미리보기 세팅 동일)...
$codeLine = $details['line'] ?? $e->getLine();
$codeFile = $details['file'] ?? $e->getFile();
$codePreview = [];
$startLine = max(1, $codeLine - 7);
$endLine = $codeLine + 7;

if (is_readable($codeFile)) {
    $lines = @file($codeFile);
    if ($lines) {
        for ($i = $startLine; $i <= min(count($lines), $endLine); $i++) {
            $codePreview[$i] = rtrim($lines[$i-1], "\r\n");
        }
    }
}
?>
<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title><?= isset($code) ? "DEBUG #$code" : 'DEBUG ERROR' ?></title>
    <style>
        :root {
            --bg-main: #1e1e2e;
            --bg-panel: #1a1b26;
            --code-bg: #16161e;
            --border-accent: #2d2f4a;
            --accent-blue: #7aa2f7;
            --accent-yellow: #e0af68;
            --accent-orange: #ff9e64;
            --accent-green: #9ece6a;
            --accent-red: #f7768e;
            --accent-purple: #bb9af7;
            --fg-light: #dcdfe4;
            --fg-label: #c0caf5;
            --line-hl: #2a2f4a;
            --line-num: #5c6370;
            --hl-stripe: rgba(255, 100, 100, 0.1);
            --font-mono: 'JetBrains Mono', 'Fira Code', Consolas, monospace;
            --shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
        }

        body {
            margin: 0;
            padding: 0;
            background: var(--bg-main);
            font-family: var(--font-mono);
            color: var(--fg-light);
        }

        .no-fallback-debug-container {
            max-width: 1000px;
            margin: 60px auto;
            padding: 40px 32px;
            background: var(--bg-panel);
            border-radius: 12px;
            box-shadow: var(--shadow);
        }

        .no-fallback-debug-title {
            font-size: 2.4em;
            color: var(--accent-red);
            margin-bottom: 12px;
        }

        .no-fallback-debug-section {
            margin-bottom: 42px;
        }

        .no-fallback-debug-label {
            font-weight: bold;
            color: var(--fg-label);
            margin-right: 4px;
        }

        .no-fallback-debug-message {
            color: var(--accent-red);
            font-weight: bold;
        }

        .no-fallback-debug-exception-type {
            color: var(--accent-orange);
            font-weight: bold;
        }

        .no-fallback-debug-code-pos {
            color: var(--accent-yellow);
        }

        .no-fallback-debug-codebox {
            background: var(--code-bg);
            border: 1px solid var(--border-accent);
            border-radius: 8px;
            overflow-x: auto;
            margin-top: 12px;
        }

        .no-fallback-debug-code {
            font-family: var(--font-mono);
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
        }

        .no-fallback-debug-code-line {
            white-space: pre;
            display: flex;
            padding: 2px 0 2px 12px;
            transition: background 0.3s ease;
        }

        .no-fallback-debug-code-highlight {
            background-color: var(--hl-stripe);
            color: var(--accent-red);
            font-weight: bold;
        }

        .no-fallback-debug-code-linenum {
            width: 48px;
            text-align: right;
            padding-right: 16px;
            user-select: none;
            color: var(--line-num);
        }

        .no-fallback-debug-section-title {
            font-size: 1.3em;
            margin-bottom: 10px;
            color: var(--accent-blue);
            border-bottom: 1px solid var(--border-accent);
            padding-bottom: 4px;
        }

        .no-fallback-debug-trace-stack {
            background: var(--code-bg);
            padding: 16px;
            border-radius: 8px;
            border: 1px solid var(--border-accent);
            font-size: 13.5px;
            overflow-x: auto;
            color: var(--fg-light);
        }

        .no-fallback-debug-trace-stack-item {
            margin-bottom: 10px;
            line-height: 1.5;
        }

        .no-fallback-debug-trace-stack-item b {
            color: var(--accent-purple);
        }

        .no-fallback-debug-meta-item,
        .no-fallback-debug-server-item {
            background: #1a1b28;
            border-left: 4px solid var(--accent-blue);
            padding: 12px 16px;
            font-size: 13px;
            margin-bottom: 8px;
            border-radius: 6px;
            word-break: break-word;
            color: var(--fg-light);
        }

        .no-fallback-debug-server-item b {
            color: var(--accent-green);
        }

        a {
            color: var(--accent-blue);
            text-decoration: underline;
        }

        a:hover {
            color: var(--accent-green);
        }

        @keyframes blink {
        0% { background-color: var(--hl-stripe); }
        50% { background-color: transparent; }
        100% { background-color: var(--hl-stripe); }
        }

        .no-fallback-debug-code-highlight {
            animation: blink 1.5s ease-in-out infinite;
        }

    </style>
</head>
<body>
    <div class="no-fallback-debug-container">
        <h1 class="no-fallback-debug-title">예외 발생 (DEBUG)</h1>
        <div class="no-fallback-debug-section">
            <div><span class="no-fallback-debug-label">에러 코드:</span> <?= htmlspecialchars($code ?? 'unknown') ?></div>
            <div><span class="no-fallback-debug-label">예외 타입:</span>
                <span class="no-fallback-debug-exception-type"><?= htmlspecialchars($details['exception'] ?? get_class($e)) ?></span>
            </div>
            <div><span class="no-fallback-debug-label">메시지:</span>
                <b class="no-fallback-debug-message"><?= htmlspecialchars($details['message'] ?? ($e->getMessage() ?? '')) ?></b>
            </div>
            <div><span class="no-fallback-debug-label">파일:</span>
                <span class="no-fallback-debug-code-pos"><?= htmlspecialchars($codeFile) ?></span>
            </div>
            <div><span class="no-fallback-debug-label">라인:</span>
                <span class="no-fallback-debug-code-pos"><?= htmlspecialchars($codeLine) ?></span>
            </div>
        </div>
        <?php if (!empty($codePreview)): ?>
            <div class="no-fallback-debug-section">
                <div class="no-fallback-debug-section-title">문제 발생 코드 (<?= htmlspecialchars($codeFile) ?>)</div>
                <div class="no-fallback-debug-codebox">
                    <pre class="no-fallback-debug-code"><?php
                        foreach ($codePreview as $lineNum => $line):
                            $isHighlight = ($lineNum == $codeLine);
                    ?><div class="no-fallback-debug-code-line<?= $isHighlight ? ' no-fallback-debug-code-highlight' : '' ?>">
                            <span class="no-fallback-debug-code-linenum"><?= $lineNum ?></span><?= htmlspecialchars($line) ?>
                        </div><?php
                        endforeach;
                    ?></pre>
                </div>
            </div>
        <?php endif; ?>

        <!-- 트레이스 call stack 추가 -->
        <div class="no-fallback-debug-section">
            <div class="no-fallback-debug-section-title">Call Stack (트레이스)</div>
            <div class="no-fallback-debug-trace-stack">
                <?php
                $traceArray = $e->getTrace();
                if (!empty($traceArray)):
                    foreach ($traceArray as $idx => $trace):
                        $func = '';
                        if (isset($trace['class'])) $func .= $trace['class'] . ($trace['type'] ?? '') . $trace['function'];
                        else $func .= $trace['function'] ?? '';
                        $file = $trace['file'] ?? '';
                        $line = $trace['line'] ?? '';
                ?>
                    <div class="no-fallback-debug-trace-stack-item">
                        <b>#<?= $idx ?>:</b>
                        <?= $func ?>
                        <?php if ($file): ?>
                            <span style="color:var(--accent-2);"> in <?= htmlspecialchars($file) ?>:<?= $line ?></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach;
                else: ?>
                    <div class="no-fallback-debug-trace-stack-item">트레이스 정보 없음</div>
                <?php endif; ?>
            </div>
        </div>
        <!-- /트레이스 call stack -->

        <?php if (!empty($meta)): ?>
            <div class="no-fallback-debug-section">
                <div class="no-fallback-debug-section-title">메타 데이터</div>
                <?php foreach ($meta as $k => $v): ?>
                    <p class="no-fallback-debug-meta-item"><b><?= htmlspecialchars($k) ?>:</b> <?= htmlspecialchars(print_r($v, true)) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SERVER)): ?>
            <div class="no-fallback-debug-section">
                <div class="no-fallback-debug-section-title">서버 정보 ($_SERVER)</div>
                <?php foreach ($_SERVER as $k => $v): ?>
                    <p class="no-fallback-debug-server-item"><b><?= htmlspecialchars($k) ?>:</b> <?= htmlspecialchars(print_r($v, true)) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div style="margin-top:32px;font-size:13px;color:#888;">
            <?= date('Y-m-d H:i:s') ?> · <a href="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? '/') ?>">새로고침</a>
        </div>
    </div>
</body>
</html>
