<?php 

namespace Framework\Validation\Rules;

class MimeType extends Rule
{
    protected $types = [];

    /**
     * @param string[] ...$types ex) image,document
     */
    public function __construct(...$types)
    {
        if (count($types) === 1 && is_array($types[0])) {
            $types = $types[0];
        }

        $mimeGroups = config('filesystem.mimetypes') ?: [];

        foreach ($types as $type) {
            if (isset($mimeGroups[$type])) {
                $this->types = array_merge($this->types, $mimeGroups[$type]);
            } else {
                // 단일 마임타입 직접 입력시 (ex: 'application/pdf')
                $this->types[] = $type;
            }
        }
        $this->types = array_unique($this->types);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function passes($value): bool
    {
        $mime = null;
        // $_FILES 방식
        if (is_array($value) && isset($value['type'])) {
            $mime = $value['type'];
        } elseif (is_string($value)) {
            // 경로로 넘어오면 finfo로 검사
            if (is_file($value)) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $value);
                finfo_close($finfo);
            } else {
                // string 자체가 mime이면 바로 검사
                $mime = $value;
            }
        }

        if ($mime === null) {
            return false;
        }

        return in_array(strtolower($mime), array_map('strtolower', $this->types), true);
    }

    public function message(): string
    {
        return lang('rule.mime_type');
    }
}
