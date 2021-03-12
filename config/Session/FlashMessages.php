<?php
namespace Config\Session;

class FlashMessages
{
    private ?array $flashes = [];

    public function initialize(?array &$flashes): void
    {
        $this->flashes = &$flashes;
    }

    public function add(string $type, string $message): void
    {
        $this->flashes[$type][] = $message;
    }

    public function peek(string $type, array $default = []): ?array
    {
        return $this->has($type) ? $this->flashes[$type] : $default;
    }

    public function peekAll(): ?array
    {
        return $this->flashes;
    }

    public function get(string $type, array $default = []): ?array
    {
        if (!$this->has($type)) {
            return $default;
        }

        $return = $this->flashes[$type];

        unset($this->flashes[$type]);

        return $return;
    }

    public function all(): ?array
    {
        $return = $this->peekAll();
        $this->flashes = [];

        return $return;
    }

    /**
     * @param string|array $messages
     */
    public function set(string $type, $messages): void
    {
        $this->flashes[$type] = (array) $messages;
    }

    public function setAll(array $messages): void
    {
        $this->flashes = $messages;
    }

    public function has(string $type): bool
    {
        return \array_key_exists($type, $this->flashes) && $this->flashes[$type];
    }

    public function keys(): ?array
    {
        return array_keys($this->flashes);
    }

    public function clear(): array
    {
        return $this->all();
    }
}
