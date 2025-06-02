<?php

namespace Framework\Console;

class SignatureParser
{
    public function parseSignature(string $signature): array
    {
        $parts = preg_split('/\s+/', $signature);
        $command = array_shift($parts);

        $arguments = [];
        $options = [];

        foreach ($parts as $part) {
            if (preg_match('/^{--(\w+)(=)?}$/', $part, $matches)) {
                $options[] = $matches[1];
            } elseif (preg_match('/^{(\w+)}$/', $part, $matches)) {
                $arguments[] = $matches[1];
            }
        }

        return [
            'command' => $command,
            'arguments' => $arguments,
            'options' => $options
        ];
    }

    public function parseInput(string $signature, array $argv): array
    {
        array_shift($argv); // 실제 커맨드만 제거

        $parsedSignature = $this->parseSignature($signature);

        $arguments = [];
        $options = [];

        foreach ($argv as $arg) {
            if (str_starts_with($arg, '--')) {
                [$key, $value] = array_pad(explode('=', ltrim($arg, '--'), 2), 2, true);
                $options[$key] = $value;
            } else {
                if ($parsedSignature['arguments']) {
                    $key = array_shift($parsedSignature['arguments']);
                    $arguments[$key] = $arg;
                }
            }
        }

        return [
            'command' => $signature,
            'arguments' => $arguments,
            'options' => $options,
        ];
    }
}
