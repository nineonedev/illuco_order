<?php

namespace Framework\Console\UI;

class Table
{
    protected array $headers = [];
    protected array $rows = [];

    public function __construct(array $headers = [])
    {
        $this->headers = $headers;
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    public function addRow(array $row): void
    {
        $this->rows[] = $row;
    }

    public function setRows(array $rows): void
    {
        $this->rows = $rows;
    }

    public function render(): void
    {
        $columns = $this->headers;
        $widths = [];

        foreach ($columns as $i => $col) {
            $widths[$i] = mb_strlen($col);
        }

        foreach ($this->rows as $row) {
            foreach ($row as $i => $value) {
                $widths[$i] = max($widths[$i] ?? 0, mb_strlen((string)$value));
            }
        }

        $this->printLine($widths);
        $this->printRow($columns, $widths);
        $this->printLine($widths);

        foreach ($this->rows as $row) {
            $this->printRow($row, $widths);
        }

        $this->printLine($widths);
    }

    protected function printLine(array $widths): void
    {
        echo '+';
        foreach ($widths as $w) {
            echo str_repeat('-', $w + 2) . '+';
        }
        echo PHP_EOL;
    }

    protected function printRow(array $row, array $widths): void
    {
        echo '|';
        foreach ($widths as $i => $w) {
            $val = $row[$i] ?? '';
            $pad = str_pad((string)$val, $w, ' ');
            echo " {$pad} |";
        }
        echo PHP_EOL;
    }
}
