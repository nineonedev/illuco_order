<?php

namespace Framework\Http\Responses;

class ViewResponse extends HtmlResponse
{
    protected string $template;

    protected array $data = [];

    public function __construct(
        string $template,
        array $data = [],
        int $statusCode = 200,
        array $headers = []
    ) {
        $this->template = $template;
        $this->data = $data;
        parent::__construct('', $statusCode, $headers);
    }

    public function setTemplate(string $template)
    {
        $this->template = $template;
        return $this;
    }

    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    public function send(): void
    {
        $this->setContent(render($this->template, $this->data));
        parent::send();
    }
    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
