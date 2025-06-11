<?php

namespace Framework\Http\Responses;

class JsonResponse extends AbstractResponse
{
    /** @var array|object|null */
    protected $data;
    protected array $meta = [];

    public function __construct($data = null, int $statusCode = 200, array $headers = [], array $meta = [])
    {
        $headers['Content-Type'] = 'application/json; charset=utf-8';
        parent::__construct('', $statusCode, $headers);
        $this->data = $data;
        $this->meta = $meta;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setMeta(array $meta)
    {
        $this->meta = $meta;
        return $this;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function send(): void
    {
        $this->sendHeaders();
        echo $this->getJsonContent();
    }

    protected function getJsonContent()
    {
        $payload = is_array($this->data) ? $this->data : ['data' => $this->data];
        if (!empty($this->meta)) {
            $payload = array_merge($payload, ['meta' => $this->meta]);
        }
        return json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}

