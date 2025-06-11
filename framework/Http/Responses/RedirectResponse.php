<?php

namespace Framework\Http\Responses;


class RedirectResponse extends AbstractResponse
{
    protected string $targetUrl;

    public function __construct(string $targetUrl, int $statusCode = 302, array $headers = [])
    {
        $this->targetUrl = $targetUrl;
        $headers['Location'] = $targetUrl;
        parent::__construct($statusCode, $statusCode, $headers);
    }

    public function getTargetUrl(): string
    {
        return $this->targetUrl;
    }
}
