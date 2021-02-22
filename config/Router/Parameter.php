<?php
namespace Config\Router;


class Parameter implements \IteratorAggregate, \Countable
{
    private $parameters;

    public function __construct($parameters)
    {
        $this->parameters = $parameters;
    }

    public function all()
    {
        return $this->parameters;
    }

    public function keys()
    {
        return array_keys($this->parameters);
    }

    public function replace(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function add(array $parameters = [])
    {
        $this->parameters = array_replace($this->parameters, $parameters);
    }

    public function get($key, $default = null)
    {
        return \array_key_exists($key, $this->parameters) ? $this->parameters[$key] : $default;
    }

    public function set($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    public function has($key)
    {
        return \array_key_exists($key, $this->parameters);
    }

    public function remove($key)
    {
        unset($this->parameters[$key]);
    }

    public function count()
    {
        return \count($this->parameters);
    }

    /**
     * Returns an iterator for parameters.
     *
     * @return \ArrayIterator An \ArrayIterator instance
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->parameters);
    }
}