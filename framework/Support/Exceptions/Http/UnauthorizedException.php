<?php

namespace Framework\Support\Exceptions\Http;

use Framework\Http\Response;

class UnauthorizedException extends HttpException
{
    public function __construct(string $message = 'Unauthorized', array $meta = [])
    {
        parent::__construct($message, 401, $meta);
    }

    public function renderHtml(): ?Response
    {
        return redirect_route('home');
    }
}
