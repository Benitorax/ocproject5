<?php

namespace Framework\Response;

use Framework\Cookie\Cookie;

class Headers
{
    public const COOKIES_FLAT = 'flat';
    public const COOKIES_ARRAY = 'array';
    protected const UPPER = '_ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    protected const LOWER = '-abcdefghijklmnopqrstuvwxyz';
    protected array $cookies = [];
    protected array $headers = [];
    protected array $headerNames = [];

    public function __construct(array $headers = [])
    {
        foreach ($headers as $key => $values) {
            $this->set($key, $values);
        }
    }

    public function __toString()
    {
        if (!$headers = $this->all()) {
            return '';
        }

        ksort($headers);
        $max = max(array_map('strlen', array_keys($headers))) + 1;
        $content = '';
        foreach ($headers as $name => $values) {
            $name = ucwords($name, '-');
            foreach ($values as $value) {
                $content .= sprintf("%-{$max}s %s\r\n", $name . ':', $value);
            }
        }

        return $content;
    }

    /**
     * @param string|null $key The name of the headers to return or null to get all of them
     */
    public function all(?string $key = null): array
    {
        $headers = $this->headers;

        if (null !== $key) {
            $key = strtr($key, self::UPPER, self::LOWER);

            return 'set-cookie' !== $key ? $headers[$key] ?? [] : array_map('strval', $this->getCookies());
        }

        foreach ($this->getCookies() as $cookie) {
            $headers['set-cookie'][] = (string) $cookie;
        }

        return $headers;
    }

    /**
     * Returns the first value or default value.
     *
     * @return string|null
     */
    public function get(string $key, string $default = null)
    {
        $headers = $this->all($key);

        if (!$headers) {
            return $default;
        }

        if (null === $headers[0]) {
            return null;
        }

        return (string) $headers[0];
    }

    /**
     * @param string|array $values
     */
    public function set(string $key, $values, bool $replace = true): void
    {
        $uniqueKey = strtr($key, self::UPPER, self::LOWER);
        $this->headerNames[$uniqueKey] = $key;

        if ('set-cookie' === $uniqueKey) {
            if ($replace) {
                $this->cookies = [];
            }
            foreach ((array) $values as $cookie) {
                $this->setCookie($cookie);
            }

            return;
        }

        if (is_array($values)) {
            $values = array_values($values);

            if (true === $replace || !isset($this->headers[$uniqueKey])) {
                $this->headers[$uniqueKey] = $values;
            } else {
                $this->headers[$uniqueKey] = array_merge($this->headers[$uniqueKey], $values);
            }
        } else {
            if (true === $replace || !isset($this->headers[$uniqueKey])) {
                $this->headers[$uniqueKey] = [$values];
            } else {
                $this->headers[$uniqueKey][] = $values;
            }
        }
    }

    public function has(string $key): bool
    {
        return \array_key_exists(strtr($key, self::UPPER, self::LOWER), $this->all());
    }

    public function remove(string $key): void
    {
        $uniqueKey = strtr($key, self::UPPER, self::LOWER);
        unset($this->headerNames[$uniqueKey]);

        if ('set-cookie' === $uniqueKey) {
            $this->cookies = [];

            return;
        }

        $key = strtr($key, self::UPPER, self::LOWER);

        unset($this->headers[$key]);
    }

    /**
     * Returns the headers, with original capitalizations.
     *
     * @return array An array of headers
     */
    public function allPreserveCase(): array
    {
        $headers = [];
        foreach ($this->all() as $name => $value) {
            $headers[$this->headerNames[$name] ?? $name] = $value;
        }

        return $headers;
    }

    public function allPreserveCaseWithoutCookies(): array
    {
        $headers = $this->allPreserveCase();
        if (isset($this->headerNames['set-cookie'])) {
            unset($headers[$this->headerNames['set-cookie']]);
        }

        return $headers;
    }

    public function setCookie(Cookie $cookie): void
    {
        $this->cookies[$cookie->getDomain()][$cookie->getPath()][$cookie->getName()] = $cookie;
        $this->headerNames['set-cookie'] = 'Set-Cookie';
    }

    public function removeCookie(string $name, ?string $path = '/', ?string $domain = null): void
    {
        if (null === $path) {
            $path = '/';
        }

        unset($this->cookies[$domain][$path][$name]);

        if (empty($this->cookies[$domain][$path])) {
            unset($this->cookies[$domain][$path]);

            if (empty($this->cookies[$domain])) {
                unset($this->cookies[$domain]);
            }
        }

        if (empty($this->cookies)) {
            unset($this->headerNames['set-cookie']);
        }
    }

    public function getCookies(string $format = self::COOKIES_FLAT): array
    {
        if (self::COOKIES_ARRAY === $format) {
            return $this->cookies;
        }

        $flattenedCookies = [];
        foreach ($this->cookies as $path) {
            foreach ($path as $cookies) {
                foreach ($cookies as $cookie) {
                    $flattenedCookies[] = $cookie;
                }
            }
        }

        return $flattenedCookies;
    }

    public function clearCookie(
        string $name,
        ?string $path = '/',
        ?string $domain = null,
        ?bool $secure = false,
        ?bool $httpOnly = true/*, $sameSite = null*/
    ): void {
        $sameSite = \func_num_args() > 5 ? func_get_arg(5) : null;

        $this->setCookie(new Cookie($name, null, (string) 1, $path, $domain, $secure, $httpOnly, false, $sameSite));
    }
}
