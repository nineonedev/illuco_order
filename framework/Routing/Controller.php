<?php
namespace Framework\Routing;

use Framework\Core\Application;
use Framework\Http\Response;

abstract class Controller
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    protected function json(array $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }

    protected function redirect(string $url, int $status = 302): Response
    {
        return Response::redirect($url, $status);
    }

    protected function back(): Response
    {
        return Response::back();
    }

    protected function view(string $template, array $data = [], int $status = 200): Response
    {
        return Response::view($template, $data, $status);
    }
}
