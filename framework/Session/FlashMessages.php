<?php

namespace Framework\Session;

/**
 * This class contains every flash messages.
 *
 * Removes the message by returning it,
 * but it can return a message without deleting it
 * with method peek() and peekAll()
 */
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
        if ($this->has($type)) {
            return !empty($this->flashes) ? $this->flashes[$type] : $default;
        } else {
            return $default;
        }
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

        if (!empty($this->flashes)) {
            $return = $this->flashes[$type];
            unset($this->flashes[$type]);

            return $return;
        }

        return $default;
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
        return \array_key_exists($type, (array) $this->flashes) && is_array($this->flashes) && $this->flashes[$type];
    }

    public function keys(): ?array
    {
        return array_keys((array) $this->flashes);
    }

    public function clear(): ?array
    {
        return $this->all();
    }
}
