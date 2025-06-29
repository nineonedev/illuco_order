<?php

namespace Framework\Database\Schema;

abstract class AbstractColumnDefinition
{
    protected string $name;
    protected string $type;

    protected bool $nullable = false;
    protected $default = null;
    protected bool $autoIncrement = false;
    protected bool $unsigned = false;

    /** @var array 인덱스 정보 (PRIMARY, UNIQUE, INDEX 등) */
    protected array $indexes = [];

    protected ?string $onUpdate = null;
    protected ?string $onDelete = null;

    protected ?string $comment = null;
    
    // --- 외래키 및 추가 옵션 ---
    protected ?string $foreignOn = null;
    protected ?string $foreignReferences = null;
    protected array $options = [];

    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    // --- 공통 Fluent API ---
    public function nullable(bool $nullable = true): self { $this->nullable = $nullable; return $this; }
    public function default($value): self { $this->default = $value; return $this; }
    public function autoIncrement(): self { $this->autoIncrement = true; return $this; }
    public function unsigned(): self { $this->unsigned = true; return $this; }
    public function primary(): self { $this->indexes[] = 'PRIMARY'; return $this; }
    public function unique(): self { $this->indexes[] = 'UNIQUE'; return $this; }
    public function index(): self { $this->indexes[] = 'INDEX'; return $this; }
    
    public function comment(string $text): self
    {
        $this->comment = $text;
        return $this;
    }

    // --- 외래키 Fluent API ---
    public function constrained(string $table = null, string $column = 'id'): self
    {
        if ($table === null) {
            $table = rtrim($this->name, '_id');
            $table = preg_match('/y$/', $table)
                ? preg_replace('/y$/', 'ies', $table)
                : $table . 's';
        }
        $this->foreignOn = $table;
        $this->foreignReferences = $column;
        return $this;
    }
    public function references(string $column = 'id'): self { $this->foreignReferences = $column; return $this; }
    public function on(string $table): self { $this->foreignOn = $table; return $this; }
    public function onDelete(string $action): self { $this->onDelete = strtoupper($action); return $this; }
    public function onUpdate(string $action): self { $this->onUpdate = strtoupper($action); return $this; }

    // --- onDelete/onUpdate Shortcuts ---
    public function onDeleteCascade(): self { return $this->onDelete('CASCADE'); }
    public function onDeleteSetNull(): self { return $this->onDelete('SET NULL'); }
    public function onDeleteRestrict(): self { return $this->onDelete('RESTRICT'); }
    public function onDeleteNoAction(): self { return $this->onDelete('NO ACTION'); }
    public function onDeleteSetDefault(): self { return $this->onDelete('SET DEFAULT'); }
    public function onUpdateCascade(): self { return $this->onUpdate('CASCADE'); }
    public function onUpdateSetNull(): self { return $this->onUpdate('SET NULL'); }
    public function onUpdateRestrict(): self { return $this->onUpdate('RESTRICT'); }
    public function onUpdateNoAction(): self { return $this->onUpdate('NO ACTION'); }
    public function onUpdateSetDefault(): self { return $this->onUpdate('SET DEFAULT'); }

    // --- Getter ---
    public function getName(): string { return $this->name; }
    public function getType(): string { return $this->type; }
    public function isNullable(): bool { return $this->nullable; }
    public function isAutoIncrement(): bool { return $this->autoIncrement; }
    public function isUnsigned(): bool { return $this->unsigned; }
    public function getDefault() { return $this->default; }
    public function getIndexes(): array { return $this->indexes; }
    public function getForeign(): ?array
    {
        if (!$this->foreignOn || !$this->foreignReferences) return null;
        return [
            'column'     => $this->name,
            'references' => $this->foreignReferences,
            'on'         => $this->foreignOn,
            'onDelete'   => $this->onDelete,
            'onUpdate'   => $this->onUpdate,
        ];
    }

    // --- SQL 생성(추상) ---
    abstract public function toSql(): string;

    // --- 통합 make 메소드 (Blueprint에서 사용)
    /**
     * @return static
     */
    public static function make(string $type, string $name, ...$args)
    {
        return new static($name, $type, ...$args);
    }

    // --- 타입별 팩토리 메소드 ---
    /**
     * @return static
     */
    public static function string(string $name, int $length = 255)
    {
        return static::make("VARCHAR($length)", $name);
    }
    /**
     * @return static
     */
    public static function integer(string $name)
    {
        return static::make("INT", $name);
    }
    /**
     * @return static
     */
    public static function bigInteger(string $name)
    {
        return static::make("BIGINT", $name);
    }
    /**
     * @return static
     */
    public static function unsignedBigInteger(string $name)
    {
        return static::make("BIGINT", $name)->unsigned();
    }
    /**
     * @return static
     */
    public static function text(string $name)
    {
        return static::make("TEXT", $name);
    }
    /**
     * @return static
     */
    public static function mediumText(string $name)
    {
        return static::make("MEDIUMTEXT", $name);
    }
    /**
     * @return static
     */
    public static function longText(string $name)
    {
        return static::make("LONGTEXT", $name);
    }
    /**
     * @return static
     */
    public static function float(string $name, int $total = 8, int $places = 2)
    {
        return static::make("FLOAT($total, $places)", $name);
    }
    /**
     * @return static
     */
    public static function double(string $name, int $total = 8, int $places = 2)
    {
        return static::make("DOUBLE($total, $places)", $name);
    }
    /**
     * @return static
     */
    public static function decimal(string $name, int $precision = 10, int $scale = 2)
    {
        return static::make("DECIMAL($precision, $scale)", $name);
    }
    /**
     * @return static
     */
    public static function date(string $name)
    {
        return static::make("DATE", $name);
    }
    /**
     * @return static
     */
    public static function datetime(string $name)
    {
        return static::make("DATETIME", $name);
    }
    /**
     * @return static
     */
    public static function timestamp(string $name)
    {
        return static::make("TIMESTAMP", $name);
    }
    /**
     * @return static
     */
    public static function time(string $name)
    {
        return static::make("TIME", $name);
    }
    /**
     * @return static
     */
    public static function year(string $name)
    {
        return static::make("YEAR", $name);
    }
    /**
     * @return static
     */
    public static function enum(string $name, array $values)
    {
        $escaped = array_map(function($v) { return "'$v'"; }, $values);
        return static::make('ENUM(' . implode(',', $escaped) . ')', $name);
    }
    /**
     * @return static
     */
    public static function set(string $name, array $values)
    {
        $escaped = array_map(function($v) { return "'$v'"; }, $values);
        return static::make('SET(' . implode(',', $escaped) . ')', $name);
    }
    /**
     * @return static
     */
    public static function json(string $name)
    {
        return static::make('JSON', $name);
    }
    /**
     * @return static
     */
    public static function binary(string $name)
    {
        return static::make('BLOB', $name);
    }
    /**
     * @return static
     */
    public static function char(string $name, int $length = 255)
    {
        return static::make("CHAR($length)", $name);
    }
    /**
     * @return static
     */
    public static function unsignedInteger(string $name)
    {
        return static::make('INT', $name)->unsigned();
    }
    /**
     * @return static
     */
    public static function unsignedSmallInteger(string $name)
    {
        return static::make('SMALLINT', $name)->unsigned();
    }
    /**
     * @return static
     */
    public static function unsignedTinyInteger(string $name)
    {
        return static::make('TINYINT', $name)->unsigned();
    }
    /**
     * @return static
     */
    public static function boolean(string $name)
    {
        return static::make('TINYINT(1)', $name);
    }

    // --- 유틸 ---
    protected function formatDefault($value): string
    {
        if (is_bool($value)) return $value ? '1' : '0';
        if (is_string($value) && strtoupper($value) === 'CURRENT_TIMESTAMP') return 'CURRENT_TIMESTAMP';
        return is_numeric($value) ? (string)$value : "'$value'";
    }
}
