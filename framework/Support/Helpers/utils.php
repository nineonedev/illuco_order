<?php

function output_lines(array $lines, bool $highlight = false): void
{
    foreach ($lines as $line) {
        if ($highlight) {
            echo "\e[1;33m" . $line . "\e[0m" . PHP_EOL; // 노란색
        } else {
            echo $line . PHP_EOL;
        }
    }
}


if (!function_exists('escape')) {
    function escape(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('now')) {
    function now(): string
    {
        return date('Y-m-d H:i:s');
    }
}

if (!function_exists('trait_used')) {
    /**
     * 특정 객체 또는 클래스가 해당 Trait을 사용하는지 확인.
     *
     * @param string $trait   Trait FQCN (ex: SoftDeletes::class)
     * @param object|string $classOrObject  객체 또는 클래스명
     * @return bool
     */
    function trait_used($trait, $classOrObject): bool
    {
        if (is_object($classOrObject)) {
            $classOrObject = get_class($classOrObject);
        }
        return in_array($trait, class_uses($classOrObject));
    }
}

function relative_path(...$path): string
{
    return implode('/', array_map(fn ($p) => trim($p, '/'), $path));
}

function absolute_path(...$path): string
{
    return '/' . relative_path(...$path);
}

function static_path(...$path): string
{
    return str_replace(BASE_PATH, '', absolute_path(...$path));
}

if (!function_exists('base_path')) {
    function base_path(string $path): string
    {
        return absolute_path(BASE_PATH, $path); 
    }
}

if (!function_exists('asset_path')) {
    function asset_path(string $path): string
    {
        return static_path(config('path.asset'), $path); 
    }
}

if (!function_exists('upload_path')) {
    function upload_path(string $path): string
    {
        $symlinks = config('filesystem.symlinks') ?? [];

        // 1. symlink 타겟 매칭 → 링크로 교체
        foreach ($symlinks as $target => $link) {
            // 네이티브로도 처리 가능
            if (strpos($path, $target) === 0) {
                // link의 뒤에 슬래시가 없으면 붙임

                $link = rtrim($link, '/');
                $relative = ltrim(substr($path, strlen($target)), '/');
                $path = $link . ($relative ? '/' . $relative : '');
                break;
            }
        }

        return static_path($path);
    }
}

if (!function_exists('json')) {
    /**
     * @param string|array $response
     */
    function json($response, $decode = false): string
    {
        if ($decode && is_string($response)) {
            return json_decode($response);
        }

        header('Content-Type: application/json'); 
        return json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}


if (!function_exists('dump')) {
    function dump(...$vars): void
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
        $file = $trace['file'] ?? 'unknown file';
        $line = $trace['line'] ?? 'unknown line';

        // 고유 ID 생성 (dd가 여러번 있어도 충돌 방지)
        $uid = uniqid('dd_', true);

        if (php_sapi_name() === 'cli') {
            echo "📌 {$file} (Line {$line})\n";
            foreach ($vars as $var) {
                dump_cli($var); 
            }
        } else {
            echo '<style>
                .dd {background:#1e1e1e; color:#d4d4d4; padding:10px; border:1px solid #333; font-family:monospace; font-size:13px; line-height:1.4;}
                .dd-key {color:#9cdcfe;}
                .dd-type {color:#ce9178;}
                .dd-collapse {cursor:pointer; color:#569cd6; user-select: none;}
                .dd-collapsed > .dd-children {display:none;}
                .dd-indent {margin-left:20px;}
                .dd-arrow {display:inline-block; width:14px; transform:rotate(90deg); transition: transform 0.2s ease;}
                .dd-collapsed .dd-arrow {transform:rotate(0deg);}
            </style>';

            echo "<pre class='dd' id='{$uid}'>";
            echo "📌 <b>{$file}</b> (Line {$line})<br><br>";
            foreach ($vars as $var) {
                dump_html($var); 
            }
            echo '</pre>';

            echo "<script>
                document.addEventListener('DOMContentLoaded', () => {
                    const root = document.getElementById('{$uid}');
                    root.querySelectorAll('.dd-collapse').forEach(el => {
                        el.addEventListener('click', function(e) {
                            e.stopPropagation();

                            const parent = this.closest('.dd-indent');
                            parent.classList.toggle('dd-collapsed');

                            const arrow = this.querySelector('.dd-arrow');
                            if (arrow) {
                                if (parent.classList.contains('dd-collapsed')) {
                                    arrow.style.transform = 'rotate(0deg)';
                                } else {
                                    arrow.style.transform = 'rotate(90deg)';
                                }
                            }
                        });
                    });
                });
                </script>";
        }
    }

    function dump_html($var, $depth = 0, &$seen = [])
    {
        $indent = str_repeat('&nbsp;', $depth * 4);

        if (is_array($var)) {
            // 배열은 ID 기반 재귀 방지 불가 -> 그냥 계속 출력 (이건 무한루프 위험 낮음)
            echo "<div class='dd-indent'><span class='dd-collapse'><span class='dd-arrow'>▶</span>[Array]</span><div class='dd-children'>";
            foreach ($var as $key => $value) {
                echo "<div>{$indent}<span class='dd-key'>" . htmlspecialchars($key) . "</span> => ";
                dump_html($value, $depth + 1, $seen);
                echo "</div>";
            }
            echo "</div></div>";
        } elseif (is_object($var)) {
            $objId = spl_object_id($var);
            if (isset($seen[$objId])) {
                echo "<span class='dd-type'>[Already Dumped Object " . get_class($var) . "]</span>";
                return;
            }
            $seen[$objId] = true;

            echo "<div class='dd-indent'><span class='dd-collapse'><span class='dd-arrow'>▶</span>[Object " . get_class($var) . "]</span><div class='dd-children'>";
            foreach ((array)$var as $key => $value) {
                echo "<div>{$indent}<span class='dd-key'>" . htmlspecialchars($key) . "</span> => ";
                dump_html($value, $depth + 1, $seen);
                echo "</div>";
            }
            echo "</div></div>";
        } else {
            echo "<span class='dd-type'>" . htmlspecialchars(var_export($var, true)) . "</span>";
        }
    }

    function dump_cli($var, $depth = 0, &$seen = [])
    {
        if (is_array($var)) {
            echo str_repeat('  ', $depth) . "[Array]\n";
            foreach ($var as $key => $value) {
                echo str_repeat('  ', $depth + 1) . "$key => ";
                dump_cli($value, $depth + 1, $seen);
            }
        } elseif (is_object($var)) {
            $objId = spl_object_id($var);
            if (isset($seen[$objId])) {
                echo str_repeat('  ', $depth) . "[Already Dumped Object " . get_class($var) . "]\n";
                return;
            }
            $seen[$objId] = true;

            echo str_repeat('  ', $depth) . "[Object " . get_class($var) . "]\n";
            foreach ((array)$var as $key => $value) {
                echo str_repeat('  ', $depth + 1) . "$key => ";
                dump_cli($value, $depth + 1, $seen);
            }
        } else {
            echo str_repeat('  ', $depth) . var_export($var, true) . "\n";
        }
    }

}


if (!function_exists('dd')) {
    function dd(...$vars): void
    {
        dump(...$vars); 
        exit; 
    }
}

if (!function_exists('dump_classes_in')) {
    /**
     * 특정 디렉토리의 PHP 파일들 중 클래스 정의를 찾아 덤프
     */
    function dump_classes_in(string $relativePath): void
    {
        $fullPath = base_path($relativePath);
        if (!is_dir($fullPath)) {
            echo "📛 디렉토리 없음: {$fullPath}" . PHP_EOL;
            return;
        }

        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($fullPath));
        foreach ($rii as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $filePath = $file->getRealPath();
            $content = file_get_contents($filePath);
            $tokens = token_get_all($content);

            $classFound = false;
            $output = '';
            foreach ($tokens as $index => $token) {
                if (is_array($token) && $token[0] === T_CLASS) {
                    $classFound = true;
                    $start = $index;

                    // 클래스 시작에서 끝까지 감지
                    $bracketCount = 0;
                    $output .= "📁 파일: {$filePath}" . PHP_EOL . PHP_EOL;
                    for ($i = $start; $i < count($tokens); $i++) {
                        $tok = $tokens[$i];
                        $text = is_array($tok) ? $tok[1] : $tok;
                        $output .= $text;

                        if ($text === '{') {
                            $bracketCount++;
                        } elseif ($text === '}') {
                            $bracketCount--;
                            if ($bracketCount === 0) {
                                break;
                            }
                        }
                    }
                    break; // 하나의 파일에 클래스 하나만
                }
            }

            if ($classFound) {
                echo str_repeat("=", 100) . PHP_EOL;
                echo $output . PHP_EOL;
            }
        }
    }
}

if (!function_exists('dump_classes_in_html')) {
    /**
     * 특정 디렉토리의 PHP 파일들 중 클래스 정의를 HTML로 <pre> 포맷하여 출력
     */
    function dump_classes_in_html(string $relativePath): void
    {
        $fullPath = base_path($relativePath);
        if (!is_dir($fullPath)) {
            echo "<p style='color:red;'>📛 디렉토리 없음: {$fullPath}</p>";
            return;
        }

        echo "<p style='color:black;'>디렉토리: {$fullPath}</p>";

        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($fullPath));
        foreach ($rii as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $filePath = $file->getRealPath();
            $content = file_get_contents($filePath);
            $tokens = token_get_all($content);

            $classFound = false;
            $output = '';
            foreach ($tokens as $index => $token) {
                if (is_array($token) && $token[0] === T_CLASS) {
                    $classFound = true;
                    $start = $index;

                    // 클래스 시작에서 끝까지 감지
                    $bracketCount = 0;
                    $output .= "📁 파일: {$filePath}\n\n";
                    for ($i = $start; $i < count($tokens); $i++) {
                        $tok = $tokens[$i];
                        $text = is_array($tok) ? $tok[1] : $tok;
                        $output .= $text;

                        if ($text === '{') {
                            $bracketCount++;
                        } elseif ($text === '}') {
                            $bracketCount--;
                            if ($bracketCount === 0) {
                                break;
                            }
                        }
                    }
                    break; // 하나의 파일에 클래스 하나만
                }
            }

            if ($classFound) {
                echo "<pre style='background:#f8f8f8; border:1px solid #ccc; padding:10px; overflow:auto; font-family:monospace; font-size:13px;'>";
                echo htmlspecialchars($output);
                echo "</pre><br>";
            }
        }
    }
}
