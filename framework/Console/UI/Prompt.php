<?php

namespace Framework\Console\UI;

class Prompt
{
    /**
     * 질문을 출력하고 사용자 입력을 문자열로 반환
     */
    public static function ask(string $question, string $default = ''): string
    {
        echo $question;

        if ($default !== '') {
            echo " [{$default}]";
        }

        echo ": ";

        $input = trim(fgets(STDIN));

        return $input === '' ? $default : $input;
    }

    /**
     * 사용자에게 y/n 확인을 받는다 (boolean 반환)
     */
    public static function confirm(string $question, bool $default = false): bool
    {
        $yes = $default ? 'Y' : 'y';
        $no  = $default ? 'n' : 'N';

        $answer = static::ask("{$question} [{$yes}/{$no}]", $default ? 'y' : 'n');

        return in_array(strtolower($answer), ['y', 'yes'], true);
    }

    /**
     * 옵션 리스트 중 하나를 선택하도록 요청
     *
     * @param string $question
     * @param array $choices
     * @param int|null $defaultIndex
     * @return string
     */
    public static function choice(string $question, array $choices, ?int $defaultIndex = null): string
    {
        echo $question . PHP_EOL;

        foreach ($choices as $i => $choice) {
            echo "  [{$i}] {$choice}" . PHP_EOL;
        }

        $default = $defaultIndex !== null ? (string) $defaultIndex : '';

        while (true) {
            $input = static::ask('선택 번호 입력', $default);

            if (is_numeric($input) && isset($choices[(int) $input])) {
                return $choices[(int) $input];
            }

            echo "유효한 번호를 입력해주세요." . PHP_EOL;
        }
    }
}
