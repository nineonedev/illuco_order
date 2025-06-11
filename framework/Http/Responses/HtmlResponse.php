<?php

namespace Framework\Http\Responses;

class HtmlResponse extends AbstractResponse
{
    /**
     * @param string $content
     * @param int $statusCode
     * @param array $headers
     */
    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $headers['Content-Type'] = 'text/html; charset=utf-8';
        parent::__construct($content, $statusCode, $headers);
    }
}
