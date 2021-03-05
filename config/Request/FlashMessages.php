<?php
namespace Config\Request;

class FlashMessages
{
    private $flashes = [];

    public function __construct()
    {
    }

    public function initialize(array &$flashes)
    {
        $this->flashes = &$flashes;
    }

    public function add($type, $message)
    {
        $this->flashes[$type][] = $message;
    }

    public function peek($type, array $default = [])
    {
        return $this->has($type) ? $this->flashes[$type] : $default;
    }

    public function peekAll()
    {
        return $this->flashes;
    }

    public function get($type, array $default = [])
    {
        if (!$this->has($type)) {
            return $default;
        }

        $return = $this->flashes[$type];

        unset($this->flashes[$type]);

        return $return;
    }

    public function all()
    {
        $return = $this->peekAll();
        $this->flashes = [];

        return $return;
    }

    public function set($type, $messages)
    {
        $this->flashes[$type] = (array) $messages;
    }

    public function setAll(array $messages)
    {
        $this->flashes = $messages;
    }

    public function has($type)
    {
        return \array_key_exists($type, $this->flashes) && $this->flashes[$type];
    }

    public function keys()
    {
        return array_keys($this->flashes);
    }

    public function clear()
    {
        return $this->all();
    }
}