<?php

namespace Config\Request;

use ArrayIterator;

class Parameter implements \IteratorAggregate, \Countable
{
    /** @var mixed[] */
    private $parameters;

    /**
     * @param mixed[] $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return mixed[]
     */
    public function all()
    {
        return $this->parameters;
    }

    /**
     *  @return mixed[]
     */
    public function keys()
    {
        return array_keys($this->parameters);
    }

    /**
     *  @param mixed[] $parameters
     */
    public function replace(array $parameters = []): void
    {
        $this->parameters = $parameters;
    }

    /**
     *  @param mixed[] $parameters
     */
    public function add(array $parameters = []): void
    {
        $this->parameters = array_replace($this->parameters, $parameters);
    }

    /**
     * @return mixed
     */
    public function get(string $key, ?string $default = null)
    {
        return array_key_exists($key, $this->parameters) ? $this->parameters[$key] : $default;
    }

    /**
     * @param string|array|object $value
     */
    public function set(string $key, $value): void
    {
        $this->parameters[$key] = $value;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->parameters);
    }

    public function remove(string $key): void
    {
        unset($this->parameters[$key]);
    }

    public function count(): int
    {
        return count($this->parameters);
    }

    /**
     * Returns an iterator for parameters.
     *
     * @return ArrayIterator An \ArrayIterator instance
     */
    public function getIterator()
    {
        return new ArrayIterator($this->parameters);
    }
}
