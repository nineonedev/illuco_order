<?php

namespace Framework\Database\Schema;

class Blueprint
{
    /** @var string */
    protected string $table;

    /** @var bool ALTER 모드 여부 */
    protected bool $isAlter = false;

    /** @var AbstractColumnDefinition[] 컬럼 정의 객체들 */
    protected array $columns = [];

    /** @var array DDL 명령 집합 (indexes, foreign 등) */
    protected array $commands = [
        'primary'     => [],
        'indexes'     => [],
        'unique'      => [],
        'dropIndexes' => [],
        'foreignKeys' => [],
    ];

    public function __construct(string $table, bool $isAlter = false)
    {
        $this->table = $table;
        $this->isAlter = $isAlter;
    }

    // ALTER 모드 설정
    public function setAlterMode(bool $alter = true): self
    {
        $this->isAlter = $alter;
        return $this;
    }

    // 컬럼 생성/변경 공통처리
        protected function createColumn(string $type, string $name): AbstractColumnDefinition
    {
        if ($this->isAlter) {
            $col = AlterColumnDefinition::add($name, $type);
        } else {
            $col = new CreateColumnDefinition($name, $type);
        }

        // 이미 같은 이름의 컬럼이 있다면 그걸 리턴 (중복 추가 방지)
        foreach ($this->columns as $idx => $existingCol) {
            if ($existingCol->getName() === $name) {
                return $existingCol;
            }
        }

        $this->columns[] = $col;
        return $col;
    }


    // --- 컬럼 타입별 메서드 (Create/Alter 자동분기) ---

    public function string(string $name, int $length = 255): AbstractColumnDefinition
    {
        return $this->createColumn("VARCHAR($length)", $name);
    }
    public function integer(string $name): AbstractColumnDefinition
    {
        return $this->createColumn('INT', $name);
    }
    public function bigInteger(string $name): AbstractColumnDefinition
    {
        return $this->createColumn('BIGINT', $name);
    }
    public function unsignedBigInteger(string $name): AbstractColumnDefinition
    {
        return $this->createColumn('BIGINT', $name)->unsigned();
    }
    public function unsignedInteger(string $name): AbstractColumnDefinition
    {
        return $this->createColumn('INT', $name)->unsigned();
    }
    public function unsignedSmallInteger(string $name): AbstractColumnDefinition
    {
        return $this->createColumn('SMALLINT', $name)->unsigned();
    }
    public function unsignedTinyInteger(string $name): AbstractColumnDefinition
    {
        return $this->createColumn('TINYINT', $name)->unsigned();
    }
    public function boolean(string $name): AbstractColumnDefinition
    {
        return $this->createColumn('TINYINT(1)', $name);
    }
    public function text(string $name): AbstractColumnDefinition
    {
        return $this->createColumn('TEXT', $name);
    }
    public function mediumText(string $name): AbstractColumnDefinition
    {
        return $this->createColumn('MEDIUMTEXT', $name);
    }
    public function longText(string $name): AbstractColumnDefinition
    {
        return $this->createColumn('LONGTEXT', $name);
    }
    public function float(string $name, int $total = 8, int $places = 2): AbstractColumnDefinition
    {
        return $this->createColumn("FLOAT($total, $places)", $name);
    }
    public function double(string $name, int $total = 8, int $places = 2): AbstractColumnDefinition
    {
        return $this->createColumn("DOUBLE($total, $places)", $name);
    }
    public function decimal(string $name, int $precision = 10, int $scale = 2): AbstractColumnDefinition
    {
        return $this->createColumn("DECIMAL($precision, $scale)", $name);
    }
    public function date(string $name): AbstractColumnDefinition
    {
        return $this->createColumn('DATE', $name);
    }
    public function datetime(string $name): AbstractColumnDefinition
    {
        return $this->createColumn('DATETIME', $name);
    }
    public function timestamp(string $name): AbstractColumnDefinition
    {
        return $this->createColumn('TIMESTAMP', $name);
    }
    public function time(string $name): AbstractColumnDefinition
    {
        return $this->createColumn('TIME', $name);
    }
    public function year(string $name): AbstractColumnDefinition
    {
        return $this->createColumn('YEAR', $name);
    }
    public function enum(string $name, array $values): AbstractColumnDefinition
    {
        $escaped = array_map(fn($v) => "'$v'", $values);
        return $this->createColumn('ENUM(' . implode(',', $escaped) . ')', $name);
    }
    public function set(string $name, array $values): AbstractColumnDefinition
    {
        $escaped = array_map(fn($v) => "'$v'", $values);
        return $this->createColumn('SET(' . implode(',', $escaped) . ')', $name);
    }
    public function json(string $name): AbstractColumnDefinition
    {
        return $this->createColumn('JSON', $name);
    }
    public function binary(string $name): AbstractColumnDefinition
    {
        return $this->createColumn('BLOB', $name);
    }
    public function char(string $name, int $length = 255): AbstractColumnDefinition
    {
        return $this->createColumn("CHAR($length)", $name);
    }

    // --- 복합 컬럼 편의 메서드 ---

    public function timestamps(): void
    {
        $this->datetime('created_at')->default('CURRENT_TIMESTAMP');
        $this->datetime('updated_at')->default('CURRENT_TIMESTAMP')->onUpdate('CURRENT_TIMESTAMP');
    }

    public function softDeletes(string $column = 'deleted_at'): void
    {
        $this->datetime($column)->nullable();
    }

    public function morphs(string $name = 'morph'): void
    {
        $this->string("{$name}_type");
        $this->unsignedBigInteger("{$name}_id");
        $this->index(["{$name}_type", "{$name}_id"]);
    }

    // id 및 primary key 편의 메서드
    public function id(string $name = 'id'): AbstractColumnDefinition
    {
        return $this->unsignedBigInteger($name)->autoIncrement()->primary();
    }

    public function foreignId(string $name): AbstractColumnDefinition
    {
        return $this->unsignedBigInteger($name);
    }

    // Blueprint 클래스 내부에 외래키 명령 자동 등록 메서드 (private)
    protected function addForeignKeysFromColumns(): void
    {
        foreach ($this->columns as $col) {
            $foreign = $col->getForeign();
            if ($foreign) {
                $fkName = "fk_{$this->table}_{$foreign['column']}";
                $this->commands['foreignKeys'][] = [
                    'name'      => $fkName,
                    'column'    => $foreign['column'],
                    'references'=> $foreign['references'],
                    'on'        => $foreign['on'],
                    'onDelete'  => $foreign['onDelete'],
                    'onUpdate'  => $foreign['onUpdate'],
                ];
            }
        }
    }

    // 외래키 추가 (ALTER 모드 시 AlterColumnDefinition::foreign 사용)
    public function foreign(string $column, ?string $name = null): AlterColumnDefinition
    {
        $def = AlterColumnDefinition::foreign($column, $name);
        $this->columns[] = $def;
        return $def;
    }

    // 인덱스 / 유니크 / 기본키 / 드롭 인덱스 처리
    public function index($columns, ?string $name = null): void
    {
        $cols = (array)$columns;
        $name = $name ?: $this->makeIndexName('idx', $cols);
        $this->commands['indexes'][] = ['columns' => $cols, 'name' => $name];
    }
    public function unique($columns, ?string $name = null): void
    {
        $cols = (array)$columns;
        $name = $name ?: $this->makeIndexName('uniq', $cols);
        $this->commands['unique'][] = ['columns' => $cols, 'name' => $name];
    }
    public function primaryKey(string ...$columns): void
    {
        $this->commands['primary'] = $columns;
    }
    public function dropIndex(string $name): void
    {
        $this->commands['dropIndexes'][] = $name;
    }

    // 컬럼 변경 (ALTER 전용)
    public function dropColumn(string ...$names): void
    {
        foreach ($names as $name) {
            $def = AlterColumnDefinition::drop($name);
            $this->columns[] = $def;
        }
    }
    public function renameColumn(string $from, string $to): void
    {
        $def = AlterColumnDefinition::rename($from, $to);
        $this->columns[] = $def;
    }
    public function modifyColumn(string $type, string $name): AlterColumnDefinition
    {
        $def = AlterColumnDefinition::modify($name, $type);
        $this->columns[] = $def;
        return $def;
    }

    protected function addPrimaryKeysFromColumns(): void
    {
        foreach ($this->columns as $col) {
            if (in_array('PRIMARY', $col->getIndexes())) {
                // 이미 등록된 값 중복 방지
                if (!in_array($col->getName(), $this->commands['primary'], true)) {
                    $this->commands['primary'][] = $col->getName();
                }
            }
        }
    }

    // ----------- 컴파일러 -----------

    // CREATE TABLE SQL 생성
    public function compileCreate(): string
    {
        $this->addPrimaryKeysFromColumns();
        $this->addForeignKeysFromColumns();

        $cols = [];
        
        foreach ($this->columns as $col) {
            if ($col instanceof CreateColumnDefinition) {
                $cols[] = $col->toSql();
            }
        }


        $sql = "CREATE TABLE `{$this->table}` (\n  " . implode(",\n  ", $cols);

        // 기본키
        if (!empty($this->commands['primary'])) {
            $pk = implode('`,`', $this->commands['primary']);
            $sql .= ",\n  PRIMARY KEY (`{$pk}`)";
        }

        // 인덱스
        foreach ($this->commands['indexes'] as $idx) {
            $colsStr = implode('`,`', $idx['columns']);
            $sql .= ",\n  INDEX `{$idx['name']}` (`{$colsStr}`)";
        }

        // 유니크
        foreach ($this->commands['unique'] as $uniq) {
            $colsStr = implode('`,`', $uniq['columns']);
            $sql .= ",\n  UNIQUE `{$uniq['name']}` (`{$colsStr}`)";
        }

        // 외래키 제약조건
        foreach ($this->commands['foreignKeys'] as $fk) {
            $onDelete = $fk['onDelete'] ? " ON DELETE {$fk['onDelete']}" : '';
            $onUpdate = $fk['onUpdate'] ? " ON UPDATE {$fk['onUpdate']}" : '';
            $sql .= ",\n  CONSTRAINT `{$fk['name']}` FOREIGN KEY (`{$fk['column']}`) REFERENCES `{$fk['on']}` (`{$fk['references']}`){$onDelete}{$onUpdate}";
        }

        $sql .= "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        return $sql;
    }


    // ALTER TABLE SQL 목록 생성
    public function compileAlter(): array
    {
        $this->addForeignKeysFromColumns();

        $sqls = [];
        foreach ($this->columns as $col) {
            if ($col instanceof AlterColumnDefinition) {
                $sqls[] = "ALTER TABLE `{$this->table}` " . $col->toSql();
            }
        }

        // 외래키 제약조건 추가 (ALTER)
        foreach ($this->commands['foreignKeys'] as $fk) {
            $onDelete = $fk['onDelete'] ? " ON DELETE {$fk['onDelete']}" : '';
            $onUpdate = $fk['onUpdate'] ? " ON UPDATE {$fk['onUpdate']}" : '';
            $sqls[] = "ALTER TABLE `{$this->table}` ADD CONSTRAINT `{$fk['name']}` FOREIGN KEY (`{$fk['column']}`) REFERENCES `{$fk['on']}` (`{$fk['references']}`){$onDelete}{$onUpdate}";
        }

        return $sqls;
    }

    // ----------- Getter -----------

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getCommands(): array
    {
        return $this->commands;
    }

    protected function makeIndexName(string $prefix, array $columns): string
    {
        return $prefix . '_' . $this->table . '_' . implode('_', $columns);
    }
}
