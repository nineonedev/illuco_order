<?php

namespace Framework\Security\Cookie;

class CookieManager
{
    /**
     * @var CookieJar[]
     */
    protected array $queued = [];

    public function get(string $name, $default = null)
    {
        if (isset($this->queued[$name])) {
            return $this->queued[$name]->getValue();
        }

        if (isset($_COOKIE[$name])) {
            // 요청 시점 쿠키를 래핑해서 큐에 저장
            $this->queued[$name] = new CookieJar($name, $_COOKIE[$name]);
            return $_COOKIE[$name];
        }

        return $default;
    }

    public function has(string $name): bool
    {
        return isset($this->queued[$name]) || isset($_COOKIE[$name]);
    }

    public function set(
        string $name,
        string $value,
        int $minutes = 0,
        string $path = '/',
        string $domain = '',
        ?bool $secure = null,
        bool $httpOnly = true,
        string $sameSite = 'Lax'
    ): void {
        if ($secure === null) {
            $secure = request()->http()->isSecure();
        }

        $this->queued[$name] = new CookieJar(
            $name,
            $value,
            $minutes,
            $path,
            $domain,
            $secure,
            $httpOnly,
            $sameSite
        );
    }

    public function setWithOption(string $name, string $value, int $minutes, array $options = []): void
    {
        $path     = $options['path']     ?? '/';
        $domain   = $options['domain']   ?? '';
        $secure   = $options['secure']   ?? null;
        $httpOnly = $options['httpOnly'] ?? true;
        $sameSite = $options['sameSite'] ?? 'Lax';

        $this->set($name, $value, $minutes, $path, $domain, $secure, $httpOnly, $sameSite);
    }


    public function forget(string $name): void
    {
        if (!isset($this->queued[$name]) && !isset($_COOKIE[$name])) {
            // 없는 경우는 기본값으로 제거
            $this->queued[$name] = new CookieJar($name, '', -60);
            return;
        }

        $cookie = $this->queued[$name] ?? new CookieJar($name, $_COOKIE[$name] ?? '');

        $this->set(
            $name,
            '',
            -60,
            $cookie instanceof CookieJar ? $cookie->getPath() : '/',
            $cookie instanceof CookieJar ? $cookie->getDomain() : '',
            $cookie instanceof CookieJar ? $cookie->isSecure() : false,
            $cookie instanceof CookieJar ? $cookie->isHttpOnly() : true,
            $cookie instanceof CookieJar ? $cookie->getSameSite() : 'Lax'
        );
    }

    public function send(): void
    {
        foreach ($this->queued as $cookie) {
            $cookie->send();
        }

        $this->queued = [];
    }

    public function queued(): array
    {
        return $this->queued;
    }
}
