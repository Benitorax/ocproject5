<?php
namespace Config\Cookie;

use DateTime;
use Exception;
use DateTimeZone;

class Cookie
{
    /**
     * Handles dates as defined by RFC 2616 section 3.3.1, and also some other
     * non-standard, but common formats.
     */
    private const DATE_FORMATS = [
        'D, d M Y H:i:s T',
        'D, d-M-y H:i:s T',
        'D, d-M-Y H:i:s T',
        'D, d-m-y H:i:s T',
        'D, d-m-Y H:i:s T',
        'D M j G:i:s Y',
        'D M d H:i:s Y T',
    ];

    protected $name;
    protected $value;
    protected $expires;
    protected $path;
    protected $domain;
    protected $secure;
    protected $httponly;
    protected $rawValue;
    private $samesite;

    /**
     * Sets a cookie.
     *
     * @param string      $name         The cookie name
     * @param string      $value        The value of the cookie
     * @param string|null $expires      The time the cookie expires
     * @param string|null $path         The path on the server in which the cookie will be available on
     * @param string      $domain       The domain that the cookie is available
     * @param bool        $secure       Indicates that the cookie should only be transmitted over a secure HTTPS connection from the client
     * @param bool        $httponly     The cookie httponly flag
     * @param bool        $encodedValue Whether the value is encoded or not
     * @param string|null $samesite     The cookie samesite attribute
     */
    public function __construct(
        string $name,
        ?string $value,
        string $expires = null,
        ?string $path = null,
        ?string $domain = '',
        ?bool $secure = false,
        ?bool $httponly = true,
        ?bool $encodedValue = false,
        ?string $samesite = null
    ) {
        if ($encodedValue) {
            $this->value = urldecode($value);
            $this->rawValue = $value;
        } else {
            $this->value = $value;
            $this->rawValue = rawurlencode($value);
        }

        $this->name = $name;
        $this->path = empty($path) ? '/' : $path;
        $this->domain = $domain ?? '';
        $this->secure = $secure ?? false;
        $this->httponly = $httponly ?? true;
        $this->samesite = $samesite;

        if (null !== $expires) {
            $timestampAsDateTime = DateTime::createFromFormat('U', $expires);
            if (false === $timestampAsDateTime) {
                throw new Exception(sprintf('The cookie expiration time "%s" is not valid.', $expires));
            }

            $this->expires = $timestampAsDateTime->format('U');
        }
    }

    /**
     * Returns the HTTP representation of the Cookie.
     */
    public function __toString(): string
    {
        $cookie = sprintf('%s=%s', $this->name, $this->rawValue);

        if (null !== $this->expires) {
            $dateTime = DateTime::createFromFormat('U', $this->expires, new DateTimeZone('GMT'));
            $cookie .= '; expires='.str_replace('+0000', '', $dateTime->format(self::DATE_FORMATS[0]));
        }

        if ('' !== $this->domain) {
            $cookie .= '; domain='.$this->domain;
        }

        if ($this->path) {
            $cookie .= '; path='.$this->path;
        }

        if ($this->secure) {
            $cookie .= '; secure';
        }

        if ($this->httponly) {
            $cookie .= '; httponly';
        }

        if (null !== $this->samesite) {
            $cookie .= '; samesite='.$this->samesite;
        }

        return $cookie;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getRawValue(): string
    {
        return $this->rawValue;
    }

    public function getExpiresTime(): ?string
    {
        return $this->expires;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function isSecure(): bool
    {
        return $this->secure;
    }

    public function isHttpOnly(): bool
    {
        return $this->httponly;
    }

    public function isExpired(): bool
    {
        return null !== $this->expires && 0 != $this->expires && $this->expires <= time();
    }

    public function getSameSite(): ?string
    {
        return $this->samesite;
    }
}
